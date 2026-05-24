<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require ROOT . '/views/partial/topbar.php'; ?>

<div class="container cal-page-layout">

    <!-- LEFT: Calendar -->
    <div class="cal-sidebar">
        <div class="cal-card">
            <div class="cal-header">
                <button class="cal-nav-btn" id="prevBtn">&#8249;</button>
                <div class="cal-month-label" id="monthLabel"></div>
                <button class="cal-nav-btn" id="nextBtn">&#8250;</button>
            </div>
            <div class="cal-grid cal-weekdays">
                <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                    <div class="cal-weekday"><?= $d ?></div>
                <?php endforeach; ?>
            </div>
            <div class="cal-grid cal-days" id="calDays"></div>
        </div>

        <!-- Mini stats -->
        <div class="cal-mini-stats">
            <div class="cal-stat-item">
                <span class="cal-stat-num"><?= $total ?></span>
                <span class="cal-stat-label">Total</span>
            </div>
            <div class="cal-stat-divider"></div>
            <div class="cal-stat-item">
                <span class="cal-stat-num" style="color:#86efac"><?= $complete ?></span>
                <span class="cal-stat-label">Done</span>
            </div>
            <div class="cal-stat-divider"></div>
            <div class="cal-stat-item">
                <span class="cal-stat-num" style="color:#f97316"><?= $pending ?></span>
                <span class="cal-stat-label">Pending</span>
            </div>
        </div>
    </div>

    <!-- RIGHT: Schedule -->
    <div class="cal-main">
        <div class="schedule-section">
            <div class="schedule-heading">
                <span class="schedule-title">Schedule</span>
                <span class="schedule-date-label" id="scheduleDateLabel"></span>
            </div>

            <!-- Add task inline form (hidden by default) -->
            <div class="inline-add-form" id="inlineAddForm">
                <form method="POST" action="index.php?page=add-task-calendar">
                    <input type="hidden" name="date" id="hiddenDate">
                    <div class="inline-add-fields">
                        <input type="text" name="title" id="inlineTitle"
                               class="inline-input" placeholder="Task title..." maxlength="255" required>
                        <input type="text" name="description" id="inlineDesc"
                               class="inline-input" placeholder="Description (optional)..." maxlength="500">
                    </div>
                    <div class="inline-add-actions">
                        <button type="submit" class="btn btn-primary btn-sm">✦ Add task</button>
                        <button type="button" class="btn btn-ghost btn-sm" id="cancelInlineAdd">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="schedule-list" id="scheduleList">
                <div class="schedule-empty">
                    <div class="icon">📅</div>
                    <p>Click a day to see its tasks.</p>
                </div>
            </div>
        </div>
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
const PHP_TASKS   = <?= json_encode($tasks) ?>;
const RETURN_PAGE = 'calendar';
</script>
<script src="public/calendar.js"></script>

</body>
</html>