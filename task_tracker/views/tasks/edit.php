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
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=edit-task&id=<?= $task['id'] ?>" id="editForm">
            <input type="hidden" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">

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

            <div class="field">
                <label>Date <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <div class="date-range-row">
                    <div class="date-range-field">
                        <label class="date-range-label" for="start_date">🌸 Start</label>
                        <input type="date" id="start_date" name="start_date"
                               value="<?= htmlspecialchars($task['start_date'] ?? $task['due_date'] ?? '') ?>"
                               onchange="markUnsaved()">
                    </div>
                    <div class="date-range-field">
                        <label class="date-range-label" for="end_date">🌷 End</label>
                        <input type="date" id="end_date" name="end_date"
                               value="<?= htmlspecialchars($task['end_date'] ?? $task['start_date'] ?? $task['due_date'] ?? '') ?>"
                               onchange="markUnsaved()">
                    </div>
                </div>
            </div>

            <div class="field">
                <label>Priority</label>
                <div class="priority-select-row">
                    <label class="priority-option">
                        <input type="radio" name="priority" value="low" <?= ($task['priority'] ?? 'medium') === 'low' ? 'checked' : '' ?> onchange="markUnsaved()">
                        <span class="priority-badge priority-low">🟢 Low</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="medium" <?= ($task['priority'] ?? 'medium') === 'medium' ? 'checked' : '' ?> onchange="markUnsaved()">
                        <span class="priority-badge priority-medium">🟡 Medium</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="high" <?= ($task['priority'] ?? '') === 'high' ? 'checked' : '' ?> onchange="markUnsaved()">
                        <span class="priority-badge priority-high">🔴 High</span>
                    </label>
                </div>
            </div>

            <!-- ── Task Icon Picker (collapsed) ── -->
            <div class="field">
                <label>Task Icon <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <input type="hidden" name="icon" id="iconInput" value="<?= htmlspecialchars($task['icon'] ?? '') ?>">

                <div class="picker-trigger" id="iconTrigger">
                    <span class="picker-trigger-icon" id="iconPreview"><?= !empty($task['icon']) ? htmlspecialchars($task['icon']) : '✦' ?></span>
                    <span class="picker-trigger-label" id="iconLabel"><?= !empty($task['icon']) ? 'Change icon' : 'Choose an icon' ?></span>
                    <span class="picker-trigger-arrow" id="iconArrow">›</span>
                </div>

                <div class="picker-dropdown" id="iconDropdown">
                    <div class="picker-dropdown-inner">
                        <div class="icon-picker-grid" id="iconPickerGrid">
                            <?php
                            $iconOptions = [
                                '✦','⭐','🌟','💫','✨','🔥','❤️','🧡','💛','💚','💙','💜',
                                '📌','📍','🎯','🏆','🎖️','🥇','💡','🔔','📣','🚀','✈️','🏃',
                                '💻','📱','🖥️','⌨️','🖱️','📷','🎵','🎨','🎭','🎮','📚','📖',
                                '📝','✏️','🖊️','📋','📁','📂','🗂️','🗒️','📊','📈','📉','💼',
                                '🛒','🛍️','🍎','🍕','☕','🍵','🎂','🍰','🌸','🌺','🌻','🌹',
                                '🌿','🍀','🌈','⛅','🌙','🌞','❄️','⚡','🎁','🎉','🎊','🪄',
                                '🐱','🐶','🐰','🦊','🐼','🦋','🐝','💎','🔮','🪷','🫧','🎀',
                            ];
                            foreach ($iconOptions as $em):
                            ?>
                            <button type="button" class="icon-opt<?= ($task['icon'] ?? '') === $em ? ' icon-opt--active' : '' ?>"
                                    data-emoji="<?= htmlspecialchars($em) ?>"><?= $em ?></button>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="picker-reset-btn" id="iconClearBtn">↺ Reset to default</button>
                    </div>
                </div>
            </div>

            <div class="field">
                <label for="category">Category <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <input type="text" id="category" name="category"
                       placeholder="e.g. Work, School, Personal"
                       value="<?= htmlspecialchars($task['category'] ?? '') ?>"
                       maxlength="50"
                       oninput="markUnsaved()">
            </div>

            <!-- ── Calendar Style Picker (collapsed, calendar only) ── -->
            <?php if (($_GET['from'] ?? '') === 'calendar'): ?>
            <div class="field">
                <label>🗓️ Calendar Style <span style="color:var(--muted);font-weight:400">(saved to browser)</span></label>

                <div class="picker-trigger cal-trigger" id="calTrigger">
                    <span class="cal-trigger-swatch" id="calPreviewSwatch"></span>
                    <span class="cal-trigger-label" id="calLabel">Sakura 🌸</span>
                    <span class="picker-trigger-arrow" id="calArrow">›</span>
                </div>

                <div class="picker-dropdown" id="calDropdown">
                    <div class="picker-dropdown-inner">
                        <div class="cal-style-grid" id="calStyleGrid">
                            <?php
                            $calStyles = [
                                ['key'=>'default',  'emoji'=>'🌸', 'name'=>'Sakura',   'colors'=>['#f9a8d4','#e9d5ff','#bae6fd']],
                                ['key'=>'ocean',    'emoji'=>'🌊', 'name'=>'Ocean',    'colors'=>['#38bdf8','#7dd3fc','#e0f2fe']],
                                ['key'=>'forest',   'emoji'=>'🌿', 'name'=>'Forest',   'colors'=>['#4ade80','#86efac','#d1fae5']],
                                ['key'=>'sunset',   'emoji'=>'🌅', 'name'=>'Sunset',   'colors'=>['#fb923c','#fda4af','#fed7aa']],
                                ['key'=>'lavender', 'emoji'=>'💜', 'name'=>'Lavender', 'colors'=>['#a78bfa','#c4b5fd','#ede9fe']],
                                ['key'=>'midnight', 'emoji'=>'🌙', 'name'=>'Midnight', 'colors'=>['#1e1b4b','#3730a3','#818cf8']],
                                ['key'=>'peach',    'emoji'=>'🍑', 'name'=>'Peach',    'colors'=>['#fb923c','#fdba74','#fef3c7']],
                                ['key'=>'mint',     'emoji'=>'🍃', 'name'=>'Mint',     'colors'=>['#34d399','#6ee7b7','#d1fae5']],
                                ['key'=>'rose',     'emoji'=>'🌹', 'name'=>'Rose',     'colors'=>['#fb7185','#fda4af','#ffe4e6']],
                                ['key'=>'aurora',   'emoji'=>'🌌', 'name'=>'Aurora',   'colors'=>['#6366f1','#a78bfa','#34d399']],
                                ['key'=>'cherry',   'emoji'=>'🍒', 'name'=>'Cherry',   'colors'=>['#ef4444','#f87171','#fee2e2']],
                                ['key'=>'cloud',    'emoji'=>'☁️', 'name'=>'Cloud',    'colors'=>['#e5e7eb','#f3f4f6','#ffffff']],
                            ];
                            foreach ($calStyles as $s):
                            ?>
                            <button type="button" class="cal-style-btn" data-style="<?= $s['key'] ?>"
                                    data-emoji="<?= $s['emoji'] ?>" data-name="<?= $s['name'] ?>">
                                <span class="cal-style-preview cal-style-preview--<?= $s['key'] ?>"></span>
                                <span class="cal-style-emoji"><?= $s['emoji'] ?></span>
                                <span class="cal-style-name"><?= $s['name'] ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✦ Save changes</button>
                <a href="index.php?page=<?= ($_GET['from'] ?? '') === 'calendar' ? 'calendar' : 'dashboard' ?>" class="btn btn-ghost" id="cancelBtn">Cancel</a>
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
    const startEl       = document.getElementById('start_date');
    const endEl         = document.getElementById('end_date');
    const originalTitle = titleEl.value;
    const originalDesc  = descEl.value;
    const originalStart = startEl.value;
    const originalEnd   = endEl.value;

    updateCounter(titleEl, 'titleCounter', 255);
    updateCounter(descEl,  'descCounter',  500);

    if (originalTitle) {
        document.getElementById('originalHint').textContent = `Original: "${originalTitle}"`;
    }

    startEl.addEventListener('change', () => {
        endEl.min = startEl.value;
        if (endEl.value && endEl.value < startEl.value) endEl.value = startEl.value;
    });

    let hasChanges = false;
    function markUnsaved() {
        hasChanges = titleEl.value !== originalTitle
                  || descEl.value  !== originalDesc
                  || startEl.value !== originalStart
                  || endEl.value   !== originalEnd;
        document.getElementById('unsavedBadge').classList.toggle('show', hasChanges);
    }

    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) { e.preventDefault(); e.returnValue = ''; }
    });

    document.getElementById('cancelBtn').addEventListener('click', () => hasChanges = false);
    document.getElementById('editForm').addEventListener('submit',  () => hasChanges = false);

    /* ── Generic dropdown toggle helper ── */
    function setupDropdown(triggerId, dropdownId, arrowId) {
        const trigger  = document.getElementById(triggerId);
        const dropdown = document.getElementById(dropdownId);
        const arrow    = document.getElementById(arrowId);
        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('picker-dropdown--open');
            closeAllDropdowns();
            if (!isOpen) {
                dropdown.classList.add('picker-dropdown--open');
                arrow.classList.add('picker-trigger-arrow--open');
                trigger.classList.add('picker-trigger--open');
            }
        });
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.picker-dropdown').forEach(d => d.classList.remove('picker-dropdown--open'));
        document.querySelectorAll('.picker-trigger-arrow').forEach(a => a.classList.remove('picker-trigger-arrow--open'));
        document.querySelectorAll('.picker-trigger').forEach(t => t.classList.remove('picker-trigger--open'));
    }

    document.addEventListener('click', closeAllDropdowns);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllDropdowns(); });

    /* ── Icon Picker ── */
    setupDropdown('iconTrigger', 'iconDropdown', 'iconArrow');
    (function () {
        const grid     = document.getElementById('iconPickerGrid');
        const input    = document.getElementById('iconInput');
        const preview  = document.getElementById('iconPreview');
        const label    = document.getElementById('iconLabel');
        const clearBtn = document.getElementById('iconClearBtn');
        if (!grid) return;

        grid.addEventListener('click', function (e) {
            const btn = e.target.closest('.icon-opt');
            if (!btn) return;
            const emoji    = btn.dataset.emoji;
            input.value    = emoji;
            preview.textContent = emoji;
            label.textContent   = 'Change icon';
            grid.querySelectorAll('.icon-opt').forEach(b => b.classList.remove('icon-opt--active'));
            btn.classList.add('icon-opt--active');
            closeAllDropdowns();
            markUnsaved();
        });

        clearBtn.addEventListener('click', function () {
            input.value = '';
            preview.textContent = '✦';
            label.textContent   = 'Choose an icon';
            grid.querySelectorAll('.icon-opt').forEach(b => b.classList.remove('icon-opt--active'));
            closeAllDropdowns();
            markUnsaved();
        });
    })();

    /* ── Calendar Style Picker ── */
    setupDropdown('calTrigger', 'calDropdown', 'calArrow');
    (function () {
        const STORAGE_KEY = 'cal_container_style';
        const STYLE_LABELS = {
            default:'Sakura 🌸', ocean:'Ocean 🌊', forest:'Forest 🌿',
            sunset:'Sunset 🌅', lavender:'Lavender 💜', midnight:'Midnight 🌙',
            peach:'Peach 🍑', mint:'Mint 🍃', rose:'Rose 🌹',
            aurora:'Aurora 🌌', cherry:'Cherry 🍒', cloud:'Cloud ☁️',
        };

        const grid    = document.getElementById('calStyleGrid');
        const label   = document.getElementById('calLabel');
        const swatch  = document.getElementById('calPreviewSwatch');
        if (!grid) return;

        function applyActive(style) {
            grid.querySelectorAll('.cal-style-btn').forEach(btn => {
                const isActive = btn.dataset.style === style;
                btn.classList.toggle('cal-style-btn--active', isActive);
                if (isActive && swatch) {
                    swatch.className = 'cal-trigger-swatch cal-style-preview--' + style;
                }
            });
            if (label) label.textContent = STYLE_LABELS[style] || style;
        }

        const current = localStorage.getItem(STORAGE_KEY) || 'default';
        applyActive(current);

        grid.addEventListener('click', function (e) {
            const btn = e.target.closest('.cal-style-btn');
            if (!btn) return;
            const style = btn.dataset.style;
            localStorage.setItem(STORAGE_KEY, style);
            applyActive(style);
            closeAllDropdowns();
        });
    })();
</script>

</body>
</html>