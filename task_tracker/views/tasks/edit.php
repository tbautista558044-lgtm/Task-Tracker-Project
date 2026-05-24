<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task — Task Tracker</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php $showBack = true; require ROOT . '/views/partial/topbar.php'; ?>

<div class="container">
    <div class="form-card">

        <div class="form-heading-row">
            <h2>✎ Edit task</h2>
            <span class="unsaved-badge" id="unsavedBadge">
                <span class="unsaved-dot"></span> Unsaved changes
            </span>
        </div>

        <!-- Read-only task info -->
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
            </div>
            <div class="meta-item">
                <strong>Task ID</strong>
                #<?= $task['id'] ?>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=edit-task&id=<?= $task['id'] ?>" id="editForm">

            <div class="field">
                <label for="title">Task title <span style="color:var(--danger)">*</span></label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($task['title']) ?>"
                       maxlength="255" required autofocus
                       oninput="updateCounter(this,'titleCounter',255); markUnsaved()">
                <div class="char-counter" id="titleCounter"></div>
                <div class="original-hint" id="originalHint"></div>
            </div>

            <div class="field">
                <label for="description">
                    Description <span style="color:var(--muted);font-weight:400">(optional)</span>
                </label>
                <textarea id="description" name="description"
                          oninput="updateCounter(this,'descCounter',500); markUnsaved()"
                ><?= htmlspecialchars($task['description']) ?></textarea>
                <div class="char-counter" id="descCounter"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✦ Save changes</button>
                <a href="index.php?page=dashboard" class="btn btn-ghost" id="cancelBtn">Cancel</a>
            </div>

        </form>
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

    const titleEl       = document.getElementById('title');
    const descEl        = document.getElementById('description');
    const originalTitle = titleEl.value;
    const originalDesc  = descEl.value;

    updateCounter(titleEl, 'titleCounter', 255);
    updateCounter(descEl,  'descCounter',  500);

    if (originalTitle) {
        document.getElementById('originalHint').textContent = `Original: "${originalTitle}"`;
    }

    let hasChanges = false;

    function markUnsaved() {
        hasChanges = titleEl.value !== originalTitle || descEl.value !== originalDesc;
        document.getElementById('unsavedBadge').classList.toggle('show', hasChanges);
    }

    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) { e.preventDefault(); e.returnValue = ''; }
    });

    document.getElementById('cancelBtn').addEventListener('click', () => hasChanges = false);
    document.getElementById('editForm').addEventListener('submit',  () => hasChanges = false);
</script>

</body>
</html>
