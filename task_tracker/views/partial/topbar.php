<?php
// views/partial/topbar.php — Reusable Navigation Bar
$currentPage  = $_GET['page'] ?? 'dashboard';
$initials     = isset($_SESSION['user_name'])   ? strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) : '';
$sessionAvatar= isset($_SESSION['user_avatar']) ? $_SESSION['user_avatar'] : '';
?>
<nav class="topbar">
    <span class="logo" style="display:flex;align-items:center;gap:2px;">
        <img src="public/images/logo-dailybun.png" alt="logo" style="height:48px;width:48px;object-fit:contain;flex-shrink:0;">
        <span style="color:var(--primary)">DailyBun</span>
    </span>

    <div class="nav-right">
        <?php if (!empty($showBack)): ?>
            <a href="javascript:history.back()" class="btn btn-ghost btn-sm">← Back</a>
        <?php elseif (isset($_SESSION['user_id'])): ?>
            <a href="index.php?page=dashboard"
               class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">📋 Tasks</a>
            <a href="index.php?page=calendar"
               class="nav-link <?= $currentPage === 'calendar'  ? 'active' : '' ?>">📅 Calendar</a>
            <a href="index.php?page=profile"
               class="topbar-avatar <?= $currentPage === 'profile' ? 'active' : '' ?>"
               id="topbarAvatar"
               title="Profile"
               <?php if (!empty($sessionAvatar)): ?>
               style="background-image:url('public/avatars/<?= htmlspecialchars($sessionAvatar) ?>');background-size:cover;background-position:center;font-size:0;"
               <?php endif; ?>
            ><?= empty($sessionAvatar) ? htmlspecialchars($initials) : '' ?></a>
            <a href="index.php?page=logout" class="btn btn-ghost btn-sm" title="Log out"
               style="margin-left:6px;">↪ Log out</a>
        <?php endif; ?>
    </div>
</nav>

<script>
/* Apply saved theme+bg+emoji avatar on every page load */
(function() {
    var t = localStorage.getItem('tt_theme');
    var b = localStorage.getItem('tt_bg');
    var a = localStorage.getItem('tt_avatar');
    if (t) document.documentElement.setAttribute('data-theme', t);
    if (b) document.body.setAttribute('data-bg', b);
    // Only apply emoji from localStorage if there's no server-side photo
    var hasPhoto = <?= !empty($sessionAvatar) ? 'true' : 'false' ?>;
    if (!hasPhoto && a) {
        var av = document.getElementById('topbarAvatar');
        if (av) {
            av.textContent = a;
            av.style.backgroundImage = '';
            av.style.fontSize = '';
            av.classList.add('avatar-emoji-mode');
        }
    }
})();
</script>