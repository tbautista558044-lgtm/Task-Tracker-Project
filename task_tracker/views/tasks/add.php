<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task — Task Tracker</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php $showBack = true; require ROOT . '/views/partial/topbar.php'; ?>

<div class="container">
    <div class="form-card">
        <h2>✦ New task</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=add-task">

            <div class="field">
                <label for="title">Task title <span style="color:var(--danger)">*</span></label>
                <input type="text" id="title" name="title"
                       placeholder="What needs to be done?"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       maxlength="255" required autofocus
                       oninput="updateCounter(this,'titleCounter',255)">
                <div class="char-counter" id="titleCounter">255 characters remaining</div>
            </div>

            <div class="field">
                <label for="description">
                    Description <span style="color:var(--muted);font-weight:400">(optional)</span>
                </label>
                <textarea id="description" name="description"
                          placeholder="Add more details about this task..."
                          oninput="updateCounter(this,'descCounter',500)"
                ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                <div class="char-counter" id="descCounter">500 characters remaining</div>
            </div>

            <div class="field">
                <label>Date <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <div class="date-range-row">
                    <div class="date-range-field">
                        <label class="date-range-label" for="start_date">🌸 Starts</label>
                        <input type="date" id="start_date" name="start_date"
                               value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="date-range-field">
                        <label class="date-range-label" for="end_date">🌷 Ends</label>
                        <input type="date" id="end_date" name="end_date"
                               value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="field-hint">Leave blank to add without a date, or pick a range for multi-day tasks.</div>
            </div>

            <div class="field">
                <label for="priority">Priority</label>
                <div class="priority-select-row">
                    <label class="priority-option">
                        <input type="radio" name="priority" value="low" <?= ($_POST['priority'] ?? '') === 'low' ? 'checked' : '' ?>>
                        <span class="priority-badge priority-low">🟢 Low</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="medium" <?= ($_POST['priority'] ?? 'medium') === 'medium' ? 'checked' : '' ?>>
                        <span class="priority-badge priority-medium">🟡 Medium</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="high" <?= ($_POST['priority'] ?? '') === 'high' ? 'checked' : '' ?>>
                        <span class="priority-badge priority-high">🔴 High</span>
                    </label>
                </div>
            </div>

            <div class="field">
                <label for="category">Category <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <input type="text" id="category" name="category"
                       placeholder="e.g. Work, School, Personal"
                       value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"
                       maxlength="50">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✦ Save task</button>
                <a href="index.php?page=dashboard" class="btn btn-ghost">Cancel</a>
            </div>

        </form>
    </div>

<script>
    function updateCounter(input, id, max) {
        const r  = max - input.value.length;
        const el = document.getElementById(id);
        el.textContent = `${r} character${r === 1 ? '' : 's'} remaining`;
        el.classList.remove('warning','danger');
        if (r < max * 0.10) el.classList.add('danger');
        else if (r < max * 0.20) el.classList.add('warning');
    }

    // Keep end_date >= start_date
    const startInput = document.getElementById('start_date');
    const endInput   = document.getElementById('end_date');
    startInput.addEventListener('change', () => {
        endInput.min = startInput.value;
        if (endInput.value && endInput.value < startInput.value) {
            endInput.value = startInput.value;
        }
    });
</script>

</body>
</html>