<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — Task Tracker</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php $showBack = false; require ROOT . '/views/partial/topbar.php'; ?>

<div class="container">

    <!-- Profile hero -->
    <div class="profile-hero">
        <div class="profile-avatar-wrap">
            <div class="profile-avatar" id="avatarDisplay">
                <?= strtoupper(mb_substr($user['name'], 0, 1)) ?>
            </div>
            <div class="avatar-emoji-picker" id="avatarPicker">
                <?php foreach (['🌸','🌟','🦋','🎀','🌙','⭐','🍀','🦊','🐱','🐼','🎨','🚀','💎','🌈','🎵','🌺'] as $em): ?>
                    <button class="avatar-emoji-btn" onclick="setAvatar('<?= $em ?>')"><?= $em ?></button>
                <?php endforeach; ?>
                <button class="avatar-emoji-btn avatar-emoji-reset" onclick="setAvatar('')">A</button>
            </div>
            <button class="avatar-change-btn" id="avatarChangeBtn" title="Change avatar">✎</button>
        </div>
        <div class="profile-hero-info">
            <div class="profile-hero-name"><?= htmlspecialchars($user['name']) ?></div>
            <div class="profile-hero-email"><?= htmlspecialchars($user['email']) ?></div>
            <div class="profile-hero-since">Member since <?= date('F Y', strtotime($user['created_at'])) ?></div>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="profile-grid">

        <!-- ── Account info ── -->
        <div class="profile-card">
            <div class="profile-card-title">✦ Account Info</div>
            <form method="POST" action="index.php?page=profile">
                <input type="hidden" name="action" value="update_info">

                <div class="field">
                    <label>Display name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                           maxlength="100" required>
                </div>
                <div class="field">
                    <label>Email address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                           maxlength="150" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>

        <!-- ── Change password ── -->
        <div class="profile-card">
            <div class="profile-card-title">🔒 Change Password</div>
            <form method="POST" action="index.php?page=profile">
                <input type="hidden" name="action" value="change_password">

                <div class="field">
                    <label>Current password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="field">
                    <label>New password</label>
                    <input type="password" name="new_password" minlength="6" required id="newPw">
                </div>
                <div class="field">
                    <label>Confirm new password</label>
                    <input type="password" name="confirm_password" required id="confirmPw"
                           oninput="checkPwMatch()">
                    <div class="match-hint" id="pwMatchHint"></div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update password</button>
                </div>
            </form>
        </div>

        <!-- ── Theme picker ── -->
        <div class="profile-card profile-card--wide">
            <div class="profile-card-title">🎨 Theme & Background</div>

            <div class="theme-section-label">Color theme</div>
            <div class="theme-grid" id="themeGrid">
                <?php
                $themes = [
                    'sakura'    => ['🌸', 'Sakura',    '#f9a8d4,#c084fc,#f0eaff'],
                    'ocean'     => ['🌊', 'Ocean',     '#7dd3fc,#38bdf8,#e0f2fe'],
                    'forest'    => ['🌿', 'Forest',    '#86efac,#4ade80,#f0fdf4'],
                    'sunset'    => ['🌅', 'Sunset',    '#fda4af,#fb923c,#fff7ed'],
                    'lavender'  => ['💜', 'Lavender',  '#c4b5fd,#a78bfa,#f5f3ff'],
                    'midnight'  => ['🌙', 'Midnight',  '#6366f1,#4f46e5,#1e1b4b'],
                    'peach'     => ['🍑', 'Peach',     '#fdba74,#fb923c,#fff7ed'],
                    'mint'      => ['🍃', 'Mint',      '#6ee7b7,#34d399,#ecfdf5'],
                ];
                foreach ($themes as $key => [$icon, $label, $colors]):
                    $colArr = explode(',', $colors);
                ?>
                <button class="theme-swatch" data-theme="<?= $key ?>"
                        onclick="applyTheme('<?= $key ?>')" title="<?= $label ?>">
                    <span class="theme-swatch-preview">
                        <span style="background:<?= $colArr[0] ?>"></span>
                        <span style="background:<?= $colArr[1] ?>"></span>
                        <span style="background:<?= $colArr[2] ?>"></span>
                    </span>
                    <span class="theme-swatch-icon"><?= $icon ?></span>
                    <span class="theme-swatch-name"><?= $label ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <div class="theme-section-label" style="margin-top:20px">Background style</div>
            <div class="bg-style-grid" id="bgStyleGrid">
                <?php
                $bgStyles = [
                    'gradient' => ['✦', 'Gradient'],
                    'mesh'     => ['⬡', 'Mesh'],
                    'minimal'  => ['□', 'Minimal'],
                    'aurora'   => ['◈', 'Aurora'],
                ];
                foreach ($bgStyles as $key => [$icon, $label]):
                ?>
                <button class="bg-style-btn" data-bg="<?= $key ?>"
                        onclick="applyBg('<?= $key ?>')" title="<?= $label ?>">
                    <span class="bg-style-icon"><?= $icon ?></span>
                    <span><?= $label ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

    </div><!-- /profile-grid -->

    <!-- Danger zone -->
    <div class="danger-zone">
        <div class="danger-zone-title">⚠️ Danger Zone</div>
        <p class="danger-zone-desc">Deleting your account is permanent and cannot be undone. All your tasks will be removed.</p>
        <button class="btn btn-danger" onclick="document.getElementById('deleteAccountModal').classList.add('show')">
            Delete my account
        </button>
    </div>

</div>

<!-- Delete account modal -->
<div class="modal-overlay" id="deleteAccountModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Delete account?</div>
        <div class="modal-desc">This will permanently delete your account and all your tasks. Type <strong>DELETE</strong> to confirm.</div>
        <input type="text" id="deleteConfirmInput" class="inline-input" placeholder="Type DELETE here..."
               style="margin:12px 0;width:100%;text-align:center;">
        <div class="modal-actions">
            <button class="btn btn-ghost"
                    onclick="document.getElementById('deleteAccountModal').classList.remove('show')">
                Cancel
            </button>
            <a href="#" id="confirmDeleteAccount" class="btn btn-danger" style="pointer-events:none;opacity:.4">
                Delete forever
            </a>
        </div>
    </div>
</div>

<script>
/* ── Avatar picker ── */
const avatarDisplay  = document.getElementById('avatarDisplay');
const avatarPicker   = document.getElementById('avatarPicker');
const avatarChangeBtn= document.getElementById('avatarChangeBtn');

avatarChangeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    avatarPicker.classList.toggle('show');
});
document.addEventListener('click', () => avatarPicker.classList.remove('show'));
avatarPicker.addEventListener('click', e => e.stopPropagation());

function setAvatar(emoji) {
    const initials = '<?= strtoupper(mb_substr($user['name'], 0, 1)) ?>';
    localStorage.setItem('tt_avatar', emoji);
    avatarDisplay.textContent = emoji || initials;
    avatarDisplay.classList.toggle('avatar-emoji-mode', !!emoji);
    avatarPicker.classList.remove('show');
    // Sync topbar avatar
    const topAvatar = document.getElementById('topbarAvatar');
    if (topAvatar) { topAvatar.textContent = emoji || initials; topAvatar.classList.toggle('avatar-emoji-mode', !!emoji); }
}

// Load saved avatar
(function() {
    const saved = localStorage.getItem('tt_avatar');
    if (saved) {
        const initials = '<?= strtoupper(mb_substr($user['name'], 0, 1)) ?>';
        avatarDisplay.textContent = saved;
        avatarDisplay.classList.add('avatar-emoji-mode');
    }
})();

/* ── Password match hint ── */
function checkPwMatch() {
    const hint = document.getElementById('pwMatchHint');
    const match = document.getElementById('newPw').value === document.getElementById('confirmPw').value;
    hint.textContent = match ? '✓ Passwords match' : '✗ Passwords do not match';
    hint.className   = 'match-hint ' + (match ? 'ok' : 'bad');
}

/* ── Theme ── */
function applyTheme(key) {
    localStorage.setItem('tt_theme', key);
    document.documentElement.setAttribute('data-theme', key);
    document.querySelectorAll('.theme-swatch').forEach(b =>
        b.classList.toggle('active', b.dataset.theme === key));
}

function applyBg(key) {
    localStorage.setItem('tt_bg', key);
    document.body.setAttribute('data-bg', key);
    document.querySelectorAll('.bg-style-btn').forEach(b =>
        b.classList.toggle('active', b.dataset.bg === key));
}

// Load saved prefs
(function() {
    const t = localStorage.getItem('tt_theme');
    const b = localStorage.getItem('tt_bg');
    if (t) applyTheme(t);
    if (b) applyBg(b);
})();

/* ── Delete account confirm ── */
const deleteInput  = document.getElementById('deleteConfirmInput');
const deleteBtn    = document.getElementById('confirmDeleteAccount');
deleteInput.addEventListener('input', () => {
    const ok = deleteInput.value === 'DELETE';
    deleteBtn.style.pointerEvents = ok ? 'auto' : 'none';
    deleteBtn.style.opacity       = ok ? '1' : '.4';
    if (ok) deleteBtn.href = 'index.php?page=delete-account';
});
document.getElementById('deleteAccountModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});
</script>

</body>
</html>