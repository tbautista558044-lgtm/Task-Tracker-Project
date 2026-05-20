<?php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error   = '';

// ── PROCESS FORM SUBMISSION ──────────────────────────────────
// Only runs when the user clicks "Save task" (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim(strip_tags($_POST['title']));
    $description = trim(strip_tags($_POST['description']));

    // Validate: title is required, description is optional
    if (empty($title)) {
        $error = "Task title is required.";
    } else {
        // ── INSERT: Add a new row to the tasks table ──────────
        // user_id links this task to the logged-in user.
        // status defaults to 'pending' (set in the DB schema).
        $stmt = mysqli_prepare($conn,
            "INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $description);

        if (mysqli_stmt_execute($stmt)) {
            // Task saved — sends back to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Could not save the task. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="style.css">

    <style>

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

        /*
            TEXTAREA resize handle styling
            The textarea for description can be dragged taller.
        */
        .field textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Page fade-in animation */
        .form-card {
            animation: fadeUp 0.45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Decorative tip box below the form */
        .tip-box {
            background: rgba(255,255,255,0.45);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-top: 16px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }

        .tip-box strong {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>

<!-- ── TOP NAVIGATION BAR ─────────────────────────────────────
     Same topbar as dashboard but with a back arrow.
-->
<nav class="topbar">
    <span class="logo"><span style="color:var(--primary)">//</span> task_tracker</span>
    <div class="nav-right">
        <a href="dashboard.php" class="btn btn-ghost btn-sm">← Back to Dashboard</a>
    </div>
</nav>


<div class="container">
    <div class="form-card">

        <!-- Page heading with icon -->
        <h2>✦ New task</h2>

        <!--
            ERROR ALERT
            Only visible when $error is not empty.
            This happens if the title was left blank.
        -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!--
            THE ADD TASK FORM
            action="add_task.php" → sends back to this file
            method="POST"         → hides data from the URL bar
        -->
        <form method="POST" action="add_task.php" id="addTaskForm">

            <!-- ── TITLE FIELD (Required) ──────────────────────
                 maxlength="255" matches the VARCHAR(255) in the DB.
                 The character counter below updates in real time via JS.
            -->
            <div class="field">
                <label for="title">Task title <span style="color:var(--danger)">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="What needs to be done?"
                    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                    maxlength="255"
                    required
                    autofocus
                    oninput="updateCounter(this, 'titleCounter', 255)"
                >
                <!--
                    autofocus → the cursor lands in this field automatically
                    oninput   → runs JS every time the user types
                -->
                <div class="char-counter" id="titleCounter">255 characters remaining</div>
            </div>

            <div class="field">
                <label for="description">Description <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Add more details about this task..."
                    oninput="updateCounter(this, 'descCounter', 500)"
                ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                
                <div class="char-counter" id="descCounter">500 characters remaining</div>
            </div>

            <!-- ── FORM ACTION BUTTONS ─────────────────────────
                 "Save task" submits the form.
                 "Cancel" goes back to dashboard without saving.
            -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    ✦ Save task
                </button>
                <a href="dashboard.php" class="btn btn-ghost">
                    Cancel
                </a>
            </div>

        </form>

    </div>

    <!--
        TIP BOX
        A small helpful hint card below the form.
        Purely informational — no functionality.
    -->
    <div class="tip-box">
        💡 <strong>Tip:</strong> Keep your task title short and action-oriented —
        start with a verb like "Buy", "Fix", "Send", or "Review".
        Use the description for extra details.
    </div>

</div>


<script>
    /**
     * updateCounter(input, counterId, maxLength)
     * -------------------------------------------
     * Runs every time the user types in a field.
     * Shows how many characters are left.
     * Changes color when getting close to the limit:
     *   - Normal  → grey  (more than 20% left)
     *   - Warning → orange (10–20% left)
     *   - Danger  → red   (under 10% left)
     *
     * @param {HTMLElement} input     - The input or textarea element
     * @param {string}      counterId - ID of the counter display element
     * @param {number}      maxLength - The character limit
     */
    function updateCounter(input, counterId, maxLength) {
        const remaining = maxLength - input.value.length;
        const el        = document.getElementById(counterId);

        // Update the text
        el.textContent = `${remaining} character${remaining === 1 ? '' : 's'} remaining`;

        // Update the color class based on how close to the limit
        el.classList.remove('warning', 'danger');
        if (remaining < maxLength * 0.10)      el.classList.add('danger');
        else if (remaining < maxLength * 0.20) el.classList.add('warning');
    }

    // Run once on page load to initialize the counter display
    // (in case the field was pre-filled due to a validation error)
    const titleInput = document.getElementById('title');
    if (titleInput.value) updateCounter(titleInput, 'titleCounter', 255);
</script>

</body>
</html>