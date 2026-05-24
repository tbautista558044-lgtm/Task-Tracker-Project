<?php
// views/partial/topbar.php — Reusable Navigation Bar
// Variables expected: $showBack (optional), $user_name (optional)
$currentPage = $_GET['page'] ?? 'dashboard';
$initials    = isset($_SESSION['user_name']) ? strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) : '';
?>
<nav class="topbar">
    <span class="logo">
        <span style="color:var(--primary)">//</span> task_tracker
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
               id="topbarAvatar" title="Profile"><?= htmlspecialchars($initials) ?></a>
        <?php endif; ?>
    </div>
</nav>

<script>
/* Apply saved theme+bg on every page load */
(function() {
    const t = localStorage.getItem('tt_theme');
    const b = localStorage.getItem('tt_bg');
    const a = localStorage.getItem('tt_avatar');
    if (t) document.documentElement.setAttribute('data-theme', t);
    if (b) document.body.setAttribute('data-bg', b);
    if (a) {
        const av = document.getElementById('topbarAvatar');
        if (av) { av.textContent = a; av.classList.add('avatar-emoji-mode'); }
    }
})();
</script>