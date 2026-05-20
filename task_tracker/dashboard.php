<?php

session_start();
require 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];


$stmt = mysqli_prepare($conn,
    "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC"
);
mysqli_stmt_bind_param($stmt, "i", $user_id); 
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tasks  = mysqli_fetch_all($result, MYSQLI_ASSOC); 


$total    = count($tasks);
$complete = count(array_filter($tasks, fn($t) => $t['status'] === 'complete'));
$pending  = $total - $complete;

$progress = $total > 0 ? round(($complete / $total) * 100) : 0;

// ── GREETING ─────────────────────────────────────────────────
// Shows "Good morning", "Good afternoon", or "Good evening"
// based on the current hour.
date_default_timezone_set('Asia/Manila'); // For Philippines

$hour = (int) date('G');
if ($hour < 12) {
    $greeting = "Good morning";
} elseif ($hour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

$filter = $_GET['filter'] ?? 'all'; // Default: show all tasks
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bun Dashboard</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /*
            FILTER TABS
            The "All / Pending / Done" tabs above the task list.
            The active tab has a solid background; others are ghost.
        */
        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            border: 1.5px solid var(--border);
            color: var(--muted);
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
        }

        .filter-tab:hover {
            background: rgba(255,255,255,0.85);
            color: var(--text-dark);
            text-decoration: none;
        }

        /* Active tab — filled with primary color */
        .filter-tab.active {
            background: linear-gradient(135deg, var(--primary), #e879f9);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(192,132,252,0.30);
        }

        /*
            TASK COUNT BADGE on filter tabs
            e.g. "Pending (3)"
        */
        .tab-count {
            display: inline-block;
            background: rgba(255,255,255,0.30);
            border-radius: 99px;
            padding: 1px 7px;
            font-size: 10px;
            margin-left: 2px;
        }

        .filter-tab:not(.active) .tab-count {
            background: var(--lav-mid);
            color: var(--text);
        }

        /*
            CLOCK display in page header
            Shows the current time (updated by JS every second)
        */
        .live-clock {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            background: rgba(255,255,255,0.60);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 5px 14px;
            letter-spacing: 0.5px;
        }

        /*
            TASK CARD entrance animation
            Each card fades up when the page loads.
            animation-delay staggers them so they appear one by one.
        */
        .task-card {
            animation: cardIn 0.4s ease both;
        }

        /* nth-child targets each card individually for stagger */
        .task-card:nth-child(1)  { animation-delay: 0.05s; }
        .task-card:nth-child(2)  { animation-delay: 0.10s; }
        .task-card:nth-child(3)  { animation-delay: 0.15s; }
        .task-card:nth-child(4)  { animation-delay: 0.20s; }
        .task-card:nth-child(5)  { animation-delay: 0.25s; }
        .task-card:nth-child(6)  { animation-delay: 0.30s; }
        .task-card:nth-child(7)  { animation-delay: 0.35s; }
        .task-card:nth-child(8)  { animation-delay: 0.40s; }
        .task-card:nth-child(9)  { animation-delay: 0.45s; }
        .task-card:nth-child(10) { animation-delay: 0.50s; }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .greeting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .greeting-text {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .greeting-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        /*
            DELETE CONFIRMATION MODAL
            A soft modal that pops up when you click "Delete"
            instead of the browser's default ugly confirm() dialog.
        */
        .modal-overlay {
            display: none;                /* Hidden by default */
            position: fixed;
            inset: 0;                     /* Covers the entire screen */
            background: rgba(100, 80, 130, 0.25);
            backdrop-filter: blur(6px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: rgba(255,255,255,0.90);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 36px 32px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: var(--shadow-lg);
            animation: modalIn 0.3s ease both;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.92) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-icon  { font-size: 40px; margin-bottom: 12px; }
        .modal-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
        .modal-desc  { font-size: 13px; color: var(--muted); margin-bottom: 24px; line-height: 1.6; }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .modal-actions .btn {
            width: auto;
            flex: 1;
        }
    </style>
</head>
<body>

<!-- TOP NAVIGATION BAR -->
<nav class="topbar">
    <span class="logo"><span style="color:var(--primary)">Your</span> ToDoBun</span>
    <div class="nav-right">
        <span>Hello, <strong><?= htmlspecialchars($user_name) ?></strong>   🌸</span>
        <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
    </div>
</nav>


<div class="container">

        <!-- Greetings -->
    <div class="greeting-row">
        <div>
            <div class="greeting-text">
                <?= htmlspecialchars($greeting) ?>,
                <?= htmlspecialchars($user_name) ?> ✿
            </div>
            <div class="greeting-sub">
                <?= date('l, F j, Y') ?>
                <!--
                    date() formats the current date.
                    'l' = full weekday name (e.g. Wednesday)
                    'F' = full month name (e.g. May)
                    'j' = day without leading zero
                    'Y' = 4-digit year
                -->
            </div>
        </div>

        <div class="live-clock" id="clock">--:--:--</div>
    </div>


    <!-- ── PROGRESS BAR ──────────────────────────────────────
         This shows how many tasks are done as a percentage.
    -->
    <?php if ($total > 0): ?>
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Progress ✦</span>
            <span><?= $complete ?> of <?= $total ?> done — <?= $progress ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
        </div>
    </div>
    <?php endif; ?>


    <!-- PAGE HEADER ROW -->
    <div class="page-header">
        <div>
            <h2>My Tasks</h2>
        </div>
        <a href="add_task.php" class="btn btn-primary btn-sm" style="width:auto;">
            + Add task
        </a>
    </div>


    <!-- STATS PILLS -->
    <div class="stats-bar">
        <span class="stat-pill">📋 <?= $total ?> total</span>
        <span class="stat-pill done">✓ <?= $complete ?> done</span>
        <span class="stat-pill pending">⏳ <?= $pending ?> pending</span>
    </div>


    <!-- FILTER TABS - Pending, Done, Complete Section -->
    <div class="filter-tabs">
        <a href="dashboard.php"
           class="filter-tab <?= $filter === 'all'      ? 'active' : '' ?>">
            All <span class="tab-count"><?= $total ?></span>
        </a>
        <a href="dashboard.php?filter=pending"
           class="filter-tab <?= $filter === 'pending'  ? 'active' : '' ?>">
            Pending <span class="tab-count"><?= $pending ?></span>
        </a>
        <a href="dashboard.php?filter=complete"
           class="filter-tab <?= $filter === 'complete' ? 'active' : '' ?>">
            Done <span class="tab-count"><?= $complete ?></span>
        </a>
    </div>


    <!-- TASK LIST -->
    <div class="task-list">

        <?php

        $filtered = array_filter($tasks, function($t) use ($filter) {
            if ($filter === 'all') return true;
            return $t['status'] === $filter;
        });
        ?>

        <?php if (empty($filtered)): ?>
            <!-- EMPTY STATE — shown when there are no tasks to display -->
            <div class="empty-state">
                <div class="icon">
                    <?php
                    // emoji for active filters
                    if ($filter === 'complete')    echo '✨';
                    elseif ($filter === 'pending') echo '🎉';
                    else                           echo '📋';
                    ?>
                </div>
                <p>
                    <?php if ($filter === 'complete'): ?>
                        No completed tasks yet. Keep going! ✿
                    <?php elseif ($filter === 'pending'): ?>
                        No pending tasks. You're all caught up! 🎉
                    <?php else: ?>
                        No tasks yet. Click <strong>+ Add task</strong> to get started.
                    <?php endif; ?>
                </p>
            </div>

        <?php else: ?>
            <?php foreach ($filtered as $task): ?>
            <!--
                TASK CARD - your tasklists or todolist
            -->
            <div class="task-card <?= $task['status'] === 'complete' ? 'complete' : '' ?>">

                <!-- LEFT: task info -->
                <div class="task-info">

                    <!-- Task title — strikethrough if complete -->
                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <!-- Optional description — only shown if it exists -->
                    <?php if (!empty($task['description'])): ?>
                        <div class="task-desc">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Status badge + creation date -->
                    <div class="task-meta">
                        <!--
                            The badge class changes based on status:
                            badge-pending → yellow
                            badge-complete → green
                        -->
                        <span class="badge badge-<?= $task['status'] ?>">
                            <?= $task['status'] === 'complete' ? '✓ done' : '⏳ pending' ?>
                        </span>

                        <span class="task-date">
                            <?= date('M j, Y', strtotime($task['created_at'])) ?>
                        </span>
                    </div>
                </div>

                <!-- RIGHT SECTION: action buttons -->
                <div class="task-actions">

                    <!--
                        COMPLETE / UNDO BUTTON
                        If pending → show "✓ Done" (links to mark_complete.php)
                        If complete → show "↩ Undo" (links to mark_pending.php)
                        Both pages just update the status and redirect back here.
                    -->
                    <?php if ($task['status'] === 'pending'): ?>
                        <a href="mark_complete.php?id=<?= $task['id'] ?>"
                           class="btn btn-success btn-sm">
                           ✓ Done
                        </a>
                    <?php else: ?>
                        <a href="mark_pending.php?id=<?= $task['id'] ?>"
                           class="btn btn-ghost btn-sm">
                           ↩ Undo
                        </a>
                    <?php endif; ?>

                    <!-- EDIT — goes to edit_task.php with the task ID in the URL -->
                    <a href="edit_task.php?id=<?= $task['id'] ?>"
                       class="btn btn-ghost btn-sm">
                       ✎ Edit
                    </a>

                    <button
                        class="btn btn-danger btn-sm"
                        onclick="openDeleteModal(<?= $task['id'] ?>, '<?= addslashes(htmlspecialchars($task['title'])) ?>')"
                    >
                        🗑 Delete
                    </button>

                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div><!-- /.task-list -->

</div><!-- /.container -->


<!-- ── DELETE CONFIRMATION MODAL ── -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">🗑️</div>
        <div class="modal-title">Delete this task?</div>
        <div class="modal-desc" id="modalDesc">
            This action cannot be undone.
        </div>
        <div class="modal-actions">
            <!-- Cancel — closes the modal without doing anything -->
            <button class="btn btn-ghost" onclick="closeDeleteModal()">
                Cancel
            </button>
            <!--
                Confirm — When clicked, it navigates to delete_task.php?id=X
                which permanently removes the task from the database.
            -->
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                Yes, delete
            </a>
        </div>
    </div>
</div>


<script>
    // LIVE CLOCK 
    // Updates the clock display every 1000ms (1 second).
    // toLocaleTimeString() returns "14:53:07" format.
    function updateClock() {
        const now  = new Date();
        const time = now.toLocaleTimeString('en-PH', { hour12: true });
        document.getElementById('clock').textContent = time;
    }
    updateClock();                     // Run immediately on load
    setInterval(updateClock, 1000);    // Then repeat every second

    // Delete Modal
    function openDeleteModal(id, title) {
        // Update the modal description with the task title
        document.getElementById('modalDesc').textContent =
            `"${title}" will be permanently deleted. This cannot be undone.`;

        // Set the confirm button's href to the correct delete URL
        document.getElementById('confirmDeleteBtn').href =
            `delete_task.php?id=${id}`;

        // Show the modal overlay
        document.getElementById('deleteModal').classList.add('show');
    }

    /**
     * closeDeleteModal()
     */
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }

    // Close modal if user clicks on the dark overlay (outside the box)
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Close modal if user presses the Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>

</body>
</html>