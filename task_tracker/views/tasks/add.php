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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✦ Save task</button>
                <a href="index.php?page=dashboard" class="btn btn-ghost">Cancel</a>
            </div>

        </form>
    </div>

    <div class="tip-box">
        💡 <strong>Tip:</strong> Keep your task title short and action-oriented —
        start with a verb like "Buy", "Fix", "Send", or "Review".
    </div>
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
</script>

</body>
</html>
