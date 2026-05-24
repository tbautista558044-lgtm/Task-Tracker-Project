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
                    <input type="hidden" name="start_date" id="hiddenStartDate">
                    <input type="hidden" name="end_date"   id="hiddenEndDate">
                    <div class="inline-add-fields">
                        <input type="text" name="title" id="inlineTitle"
                               class="inline-input" placeholder="Task title..." maxlength="255" required>
                        <textarea name="description" id="inlineDesc"
                               class="inline-input" placeholder="Description (optional)..." maxlength="500" rows="2" style="resize:vertical;"></textarea>
                        <div class="inline-date-row">
                            <div class="inline-date-field">
                                <label class="inline-date-label">🌸 Starts</label>
                                <input type="date" id="startDateInput" name="start_date_pick"
                                       class="inline-input--date">
                            </div>
                            <div style="width:1px;background:var(--border);align-self:stretch;margin:0 2px;"></div>
                            <div class="inline-date-field">
                                <label class="inline-date-label">🌷 Ends</label>
                                <input type="date" id="endDateInput" name="end_date_pick"
                                       class="inline-input--date">
                            </div>
                        </div>
                        <div class="inline-priority-row">
                            <span class="inline-field-label">Priority</span>
                            <div class="priority-select-row">
                                <label class="priority-option">
                                    <input type="radio" name="priority" value="low">
                                    <span class="priority-badge priority-low">🟢 Low</span>
                                </label>
                                <label class="priority-option">
                                    <input type="radio" name="priority" value="medium" checked>
                                    <span class="priority-badge priority-medium">🟡 Medium</span>
                                </label>
                                <label class="priority-option">
                                    <input type="radio" name="priority" value="high">
                                    <span class="priority-badge priority-high">🔴 High</span>
                                </label>
                            </div>
                        </div>
                        <input type="text" name="category" class="inline-input"
                               placeholder="Category (optional, e.g. Work, School...)" maxlength="50">
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
<script>
/* Apply saved calendar container style immediately before calendar.js renders */
(function () {
    const style = localStorage.getItem('cal_container_style') || 'default';
    if (style && style !== 'default') {
        const card = document.querySelector('.cal-card');
        if (card) card.setAttribute('data-cal-style', style);
    }
})();
</script>
<script src="public/calendar.js"></script>
<script>
/* Re-apply after calendar.js finishes (it may recreate the DOM) */
(function () {
    const style = localStorage.getItem('cal_container_style') || 'default';
    if (style && style !== 'default') {
        const card = document.querySelector('.cal-card');
        if (card) card.setAttribute('data-cal-style', style);
    }
})();
</script>

</body>
</html>