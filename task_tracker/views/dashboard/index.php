<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require ROOT . '/views/partial/topbar.php'; ?>

<div class="container">

    <!-- Greeting row + live clock -->
    <div class="greeting-row">
        <div>
            <div class="greeting-text">
                <?= htmlspecialchars($greeting) ?>, <?= htmlspecialchars($user_name) ?> 🌸
            </div>
            <div class="greeting-sub"><?= date('l, F j, Y') ?></div>
        </div>
        <div class="live-clock" id="clock">--:--:--</div>
    </div>

    <!-- Progress bar -->
    <?php if ($total > 0): ?>
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Progress ✦</span>
            <span><?= $complete ?> of <?= $total ?> done — <?= $progress ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width:<?= $progress ?>%"></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page header -->
    <div class="page-header">
        <h2>My Tasks</h2>
        <a href="index.php?page=add-task" class="btn btn-primary btn-sm" style="width:auto;">
            + Add task
        </a>
    </div>

    <!-- Stats pills -->
    <div class="stats-bar">
        <span class="stat-pill">📋 <?= $total ?> total</span>
        <span class="stat-pill done">✓ <?= $complete ?> done</span>
        <span class="stat-pill pending">⏳ <?= $pending ?> pending</span>
        <?php if ($dueToday > 0): ?>
        <span class="stat-pill today">🔥 <?= $dueToday ?> due today</span>
        <?php endif; ?>
    </div>

    <!-- Filter tabs -->
    <div class="filter-tabs">
        <a href="index.php?page=dashboard"
           class="filter-tab <?= $filter === 'all'      ? 'active' : '' ?>">
            All <span class="tab-count"><?= $total ?></span>
        </a>
        <a href="index.php?page=dashboard&filter=pending"
           class="filter-tab <?= $filter === 'pending'  ? 'active' : '' ?>">
            Pending <span class="tab-count"><?= $pending ?></span>
        </a>
        <a href="index.php?page=dashboard&filter=complete"
           class="filter-tab <?= $filter === 'complete' ? 'active' : '' ?>">
            Done <span class="tab-count"><?= $complete ?></span>
        </a>
    </div>

    <!-- Task list -->
    <div class="task-list">
        <?php
        $filtered = array_filter($tasks, function($t) use ($filter) {
            return $filter === 'all' ? true : $t['status'] === $filter;
        });
        ?>

        <?php if (empty($filtered)): ?>
            <div class="empty-state">
                <div class="icon">
                    <?php
                    if ($filter === 'complete')     echo '✨';
                    elseif ($filter === 'pending')  echo '🎉';
                    else                            echo '📋';
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
            <div class="task-card <?= $task['status'] === 'complete' ? 'complete' : '' ?>">
                <?php if (!empty($task['icon'])): ?>
                <div class="task-card-icon"><?= htmlspecialchars($task['icon']) ?></div>
                <?php endif; ?>

                <div class="task-info">
                    <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>

                    <?php if (!empty($task['description'])): ?>
                        <div class="task-desc"><?= htmlspecialchars($task['description']) ?></div>
                    <?php endif; ?>

                    <div class="task-meta">
                        <span class="badge badge-<?= $task['status'] ?>">
                            <?= $task['status'] === 'complete' ? '✓ done' : '⏳ pending' ?>
                        </span>
                        <?php
                        $priority = $task['priority'] ?? 'medium';
                        $priorityIcons = ['low' => '🟢', 'medium' => '🟡', 'high' => '🔴'];
                        $taskStart = $task['start_date'] ?? $task['due_date'] ?? null;
                        $taskEnd   = $task['end_date']   ?? $taskStart;
                        $isDueToday = $task['status'] === 'pending'
                            && $taskStart <= $today && $taskEnd >= $today;
                        ?>
                        <?php if ($isDueToday): ?>
                            <span class="badge-due-today">🔥 Due Today</span>
                        <?php endif; ?>
                        <span class="priority-badge priority-<?= $priority ?>">
                            <?= $priorityIcons[$priority] ?> <?= ucfirst($priority) ?>
                        </span>
                        <?php if (!empty($task['category'])): ?>
                            <span class="category-tag"># <?= htmlspecialchars($task['category']) ?></span>
                        <?php endif; ?>
                        <?php
                        $start = !empty($task['start_date']) ? $task['start_date']
                               : (!empty($task['due_date'])  ? $task['due_date'] : null);
                        $end   = !empty($task['end_date']) ? $task['end_date'] : $start;
                        if ($start):
                            $startFmt = date('M j, Y', strtotime($start));
                            $endFmt   = date('M j, Y', strtotime($end));
                        ?>
                        <span class="task-date">
                            📅 <?= ($start === $end) ? $startFmt : "$startFmt → $endFmt" ?>
                        </span>
                        <?php else: ?>
                        <span class="task-date">
                            <?= date('M j, Y', strtotime($task['created_at'])) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="task-actions">
                    <?php if ($task['status'] === 'pending'): ?>
                        <a href="index.php?page=mark-complete&id=<?= $task['id'] ?>"
                           class="btn btn-success btn-sm">✓ Done</a>
                    <?php else: ?>
                        <a href="index.php?page=mark-pending&id=<?= $task['id'] ?>"
                           class="btn btn-ghost btn-sm">↩ Undo</a>
                    <?php endif; ?>

                    <a href="index.php?page=edit-task&id=<?= $task['id'] ?>"
                       class="btn btn-ghost btn-sm">✎ Edit</a>

                    <button class="btn btn-danger btn-sm"
                            onclick="openDeleteModal(<?= $task['id'] ?>, '<?= addslashes(htmlspecialchars($task['title'])) ?>')">
                        🗑 Delete
                    </button>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Delete modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">🗑️</div>
        <div class="modal-title">Delete this task?</div>
        <div class="modal-desc" id="modalDesc">This action cannot be undone.</div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes, delete</a>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        document.getElementById('clock').textContent =
            new Date().toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    updateClock();
    setInterval(updateClock, 1000);

    function openDeleteModal(id, title) {
        document.getElementById('modalDesc').textContent =
            `"${title}" will be permanently deleted. This cannot be undone.`;
        document.getElementById('confirmDeleteBtn').href =
            `index.php?page=delete-task&id=${id}`;
        document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>

</body>
</html>