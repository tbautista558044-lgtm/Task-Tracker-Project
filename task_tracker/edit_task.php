<?php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error   = '';

$stmt = mysqli_prepare($conn,
    "SELECT * FROM tasks WHERE id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task   = mysqli_fetch_assoc($result);

// If task not found OR doesn't belong to this user → redirect
if (!$task) {
    header("Location: dashboard.php");
    exit;
}

// ── PROCESS FORM SUBMISSION ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(strip_tags($_POST['title']));
    $description = trim(strip_tags($_POST['description']));

    if (empty($title)) {
        $error = "Task title is required.";
    } else {
        // ── UPDATE: Save the new title and description ────────
        $update = mysqli_prepare($conn,
            "UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?"
        );
        // "ss" = two strings, "ii" = two integers
        mysqli_stmt_bind_param($update, "ssii", $title, $description, $task_id, $user_id);

        if (mysqli_stmt_execute($update)) {
            // Updated — user get sent back to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Could not update the task. Please try again.";
        }
    }

    $task['title']       = $_POST['title'];
    $task['description'] = $_POST['description'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Character counter */
        .char-counter {
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            margin-top: 4px;
            transition: color 0.2s;
        }

        .char-counter.warning { color: var(--warning-text); }
        .char-counter.danger  { color: var(--danger-text);  }

        .field textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Card fade-in */
        .form-card {
            animation: fadeUp 0.45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /*
            ORIGINAL VALUE HINT
            Shows the original task title in small text below
            the input, so the user knows what they're changing from.
        */
        .original-hint {
            font-size: 11px;
            color: var(--muted);
            margin-top: 5px;
            font-style: italic;
        }

        /*
            TASK META INFO CARD
            Shows when the task was created and its current status.
            Purely informational — read-only display.
        */
        .task-meta-card {
            background: rgba(255,255,255,0.45);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .task-meta-card .meta-item {
            font-size: 12px;
            color: var(--muted);
        }

        .task-meta-card .meta-item strong {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: var(--text);
        }

        /*
            UNSAVED CHANGES INDICATOR
            A small dot + text that appears when the user
            has modified the form but hasn't saved yet.
        */
        .unsaved-badge {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--warning-text);
            background: var(--warning);
            padding: 4px 10px;
            border-radius: 99px;
        }

        .unsaved-badge.show { display: inline-flex; }

        .unsaved-dot {
            width: 6px;
            height: 6px;
            background: currentColor;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* Form heading row: title + unsaved badge side by side */
        .form-heading-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .form-heading-row h2 {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<!-- ── TOP NAVIGATION BAR ─────────────────────────────────────-->
<nav class="topbar">
    <span class="logo"><span style="color:var(--primary)">Your</span> ToDoBun</span>
    <div class="nav-right">
        <a href="dashboard.php" class="btn btn-ghost btn-sm">← Back to Dashboard</a>
    </div>
</nav>


<div class="container">
    <div class="form-card">

        <!-- Heading + unsaved indicator -->
        <div class="form-heading-row">
            <h2>✎ Edit task</h2>
            <span class="unsaved-badge" id="unsavedBadge">
                <span class="unsaved-dot"></span>
                Unsaved changes
            </span>
        </div>

        <!--
            TASK META INFO
            Shows the current status and creation date.
            Read-only — these can't be changed on this form.
            (Status is changed via the ✓ Done / ↩ Undo buttons on the dashboard)
        -->
        <div class="task-meta-card">
            <div class="meta-item">
                <strong>Status</strong>
                <span class="badge badge-<?= $task['status'] ?>">
                    <?= $task['status'] === 'complete' ? '✓ done' : '⏳ pending' ?>
                </span>
            </div>
            <div class="meta-item">
                <strong>Created</strong>
                <?= date('F j, Y · g:i A', strtotime($task['created_at'])) ?>
                <!--
                    'F j, Y · g:i A' = "May 7, 2025 · 2:30 PM"
                    g = 12-hour clock, i = minutes, A = AM/PM
                -->
            </div>
            <div class="meta-item">
                <strong>Task ID</strong>
                #<?= $task['id'] ?>
            </div>
        </div>

        <!-- Error alert -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_task.php?id=<?= $task_id ?>" id="editForm">

            <!-- ── TITLE FIELD ─────────────────────────────────-->
            <div class="field">
                <label for="title">Task title <span style="color:var(--danger)">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars($task['title']) ?>"
                    maxlength="255"
                    required
                    autofocus
                    oninput="updateCounter(this, 'titleCounter', 255); markUnsaved()"
                >
                <!--
                    The original title is stored in data-original.
                    JS uses this to detect if the user made changes.
                -->
                <div class="char-counter" id="titleCounter"></div>
                <div class="original-hint" id="originalHint"></div>
            </div>

            <!-- ── DESCRIPTION FIELD ───────────────────────────-->
            <div class="field">
                <label for="description">Description <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <textarea
                    id="description"
                    name="description"
                    oninput="updateCounter(this, 'descCounter', 500); markUnsaved()"
                ><?= htmlspecialchars($task['description']) ?></textarea>
                <div class="char-counter" id="descCounter"></div>
            </div>

            <!-- ── FORM ACTION BUTTONS ─────────────────────────-->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    ✦ Save changes
                </button>
                <a href="dashboard.php" class="btn btn-ghost" id="cancelBtn">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>


<script>
    // ── CHARACTER COUNTER ─────────────────────────────────────
    function updateCounter(input, counterId, maxLength) {
        const remaining = maxLength - input.value.length;
        const el        = document.getElementById(counterId);

        el.textContent = `${remaining} character${remaining === 1 ? '' : 's'} remaining`;
        el.classList.remove('warning', 'danger');

        if (remaining < maxLength * 0.10)      el.classList.add('danger');
        else if (remaining < maxLength * 0.20) el.classList.add('warning');
    }

    // ── INITIALIZE ON PAGE LOAD ───────────────────────────────
    // Pre-fill counters based on existing values
    const titleEl = document.getElementById('title');
    const descEl  = document.getElementById('description');

    // Store the original values so we can detect changes
    const originalTitle = titleEl.value;
    const originalDesc  = descEl.value;

    updateCounter(titleEl, 'titleCounter', 255);
    updateCounter(descEl,  'descCounter',  500);

    // Show original title as hint
    const hintEl = document.getElementById('originalHint');
    if (originalTitle) {
        hintEl.textContent = `Original: "${originalTitle}"`;
    }

    // ── UNSAVED CHANGES INDICATOR ─────────────────────────────
    /**
     * markUnsaved()
     * Shows the "● Unsaved changes" badge when the user edits
     * any field. Makes it clear they haven't saved yet.
     * Also warns before leaving the page without saving.
     */
    let hasChanges = false;

    function markUnsaved() {
        const currentTitle = titleEl.value;
        const currentDesc  = descEl.value;

        // Checks if anything actually changed from original
        hasChanges = (currentTitle !== originalTitle) || (currentDesc !== originalDesc);

        const badge = document.getElementById('unsavedBadge');
        if (hasChanges) {
            badge.classList.add('show');
        } else {
            badge.classList.remove('show');
        }
    }

    /*
        Warn the user if they try to leave the page
        (e.g. click browser back) with unsaved changes.
        The browser shows a standard "Leave site?" dialog.
        We skip this if they click "Cancel" (handled separately).
    */
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = ''; // Required for Chrome
        }
    });

    // "Cancel" should bypass the unsaved warning — it's intentional
    document.getElementById('cancelBtn').addEventListener('click', function() {
        hasChanges = false;
    });

    // Saving the form is intentional — no warning needed
    document.getElementById('editForm').addEventListener('submit', function() {
        hasChanges = false;
    });
</script>

</body>
</html>