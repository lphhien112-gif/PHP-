<?php
/**
 * Layout chung — WorkshopHub (modern sidebar-admin shell).
 * Bien nhan vao: $title, $content.
 * Trang /login dung bien the "auth" (the giua man hinh, khong sidebar).
 */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$flashSuccess = flash_get('success');
$flashError   = flash_get('error');
$flashWarning = flash_get('warning');
$loggedIn = is_logged_in();
$user     = current_user();
$isAuth   = ($currentPath === '/login');

$is = fn(string $p) => $currentPath === $p ? 'active' : '';

// Breadcrumb theo route.
$crumb = match (true) {
    $currentPath === '/'                       => 'Trang chu',
    str_starts_with((string)$currentPath, '/registrations/create') => 'Dang ky moi',
    str_starts_with((string)$currentPath, '/registrations')        => 'Danh sach dang ky',
    str_starts_with((string)$currentPath, '/dashboard')            => 'Bang dieu khien',
    str_starts_with((string)$currentPath, '/session-demo')         => 'Session demo',
    $currentPath === '/login'                  => 'Dang nhap',
    default                                     => 'WorkshopHub',
};

// Inline SVG icon set (fill = currentColor).
$icons = [
    'home'    => '<path d="M12 3l9 8h-3v9h-5v-6H11v6H6v-9H3l9-8z"/>',
    'list'    => '<path d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z"/>',
    'add'     => '<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>',
    'dash'    => '<path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>',
    'session' => '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>',
];
$icon = fn(string $k) => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">' . ($icons[$k] ?? '') . '</svg>';
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WorkshopHub — Cong dang ky tham du workshop voi form bao mat, PRG, anti-spam va dang nhap nhan vien.">
    <meta name="author" content="Le Pham Hong Hien — 22110059">
    <title><?= h($title ?? 'WorkshopHub') ?></title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<?php if ($isAuth): ?>
<body class="auth-body">
    <div class="auth-wrap">
        <main>
            <?php if ($flashSuccess): ?><div class="flash flash-success">✓ <?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError):   ?><div class="flash flash-error">✕ <?= h($flashError) ?></div><?php endif; ?>
            <?php if ($flashWarning): ?><div class="flash flash-warning">⚠ <?= h($flashWarning) ?></div><?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>
<?php return; endif; ?>
<body class="app-shell">

<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <a href="/" class="brand">
            <img src="/assets/img/logo-mark.png" alt="" class="brand-mark">
            <span class="brand-txt">Workshop<span class="hl">Hub</span><small>Workshop Registration</small></span>
        </a>
        <button class="drawer-toggle" id="drawerToggle" aria-label="Dong menu" type="button">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
    </div>

    <nav class="side-nav" id="sideNav">
        <a href="/" class="<?= $is('/') ?>"><?= $icon('home') ?><span>Trang chu</span></a>
        <a href="/registrations" class="<?= $is('/registrations') ?>"><?= $icon('list') ?><span>Danh sach</span></a>
        <a href="/registrations/create" class="<?= $is('/registrations/create') ?>"><?= $icon('add') ?><span>Dang ky</span></a>
        <?php if ($loggedIn): ?>
            <div class="side-sep"></div>
            <div class="side-cap">Nhan vien</div>
            <a href="/dashboard" class="<?= $is('/dashboard') ?>"><?= $icon('dash') ?><span>Dashboard</span></a>
            <a href="/session-demo" class="<?= $is('/session-demo') ?>"><?= $icon('session') ?><span>Session demo</span></a>
        <?php endif; ?>
    </nav>

    <?php if ($loggedIn): ?>
    <div class="sidebar-user">
        <div class="su-avatar"><?= h(mb_substr((string)($user['name'] ?? 'U'), 0, 1)) ?></div>
        <div class="su-meta">
            <span class="su-name"><?= h($user['name'] ?? 'Nhan vien') ?></span>
            <span class="badge-role"><?= h($user['role'] ?? 'staff') ?></span>
        </div>
        <form method="post" action="/logout" style="margin:0;">
            <button type="submit" class="btn-logout" title="Dang xuat" aria-label="Dang xuat">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            </button>
        </form>
    </div>
    <?php else: ?>
    <div class="sidebar-cta">
        <p>Khu vuc nhan vien quan ly dang ky.</p>
        <a href="/login" class="btn btn-accent">Dang nhap</a>
    </div>
    <?php endif; ?>
</aside>

<div class="app-main">
    <header class="topbar">
        <div class="topbar-inner">
            <button class="topbar-burger" id="topbarBurger" aria-label="Mo menu" aria-controls="sidebar" type="button">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>
            <div class="crumb">
                <span class="crumb-home">WorkshopHub</span>
                <span class="crumb-sep">/</span>
                <span class="crumb-cur"><?= h($crumb) ?></span>
            </div>
            <div class="topbar-actions">
                <?php if ($loggedIn): ?>
                    <span class="topbar-hi">Xin chao, <strong><?= h($user['name'] ?? 'Nhan vien') ?></strong></span>
                <?php else: ?>
                    <a href="/registrations/create" class="btn btn-primary btn-sm">+ Dang ky</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="content">
        <?php if ($flashSuccess): ?><div class="flash flash-success">✓ <?= h($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError):   ?><div class="flash flash-error">✕ <?= h($flashError) ?></div><?php endif; ?>
        <?php if ($flashWarning): ?><div class="flash flash-warning">⚠ <?= h($flashWarning) ?></div><?php endif; ?>

        <?= $content ?? '' ?>
    </main>
</div>

<div class="drawer-backdrop" id="drawerBackdrop"></div>
<script>
(function () {
    var t  = document.getElementById('drawerToggle');
    var tb = document.getElementById('topbarBurger');
    var sb = document.getElementById('sidebar');
    var bd = document.getElementById('drawerBackdrop');
    function toggle() { sb && sb.classList.toggle('open'); bd && bd.classList.toggle('show'); }
    function close()  { sb && sb.classList.remove('open'); bd && bd.classList.remove('show'); }
    if (t)  t.addEventListener('click', toggle);
    if (tb) tb.addEventListener('click', toggle);
    if (bd) bd.addEventListener('click', close);
})();
</script>
</body>
</html>
