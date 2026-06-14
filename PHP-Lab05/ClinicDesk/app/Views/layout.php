<?php
/**
 * Layout chung cho ClinicDesk - admin shell hien dai (sidebar + topbar + content).
 * Bien $content da duoc view con chuan bi. View KHONG chua SQL.
 * Giao dien toi (dark navy + sky-blue + violet).
 */
use ClinicDesk\Core\View;
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = fn(string $p, bool $exact = false) => (
    $exact ? ($currentPath === $p) : str_starts_with((string)$currentPath, $p)
) ? 'active' : '';

// Breadcrumb don gian theo segment dau.
$crumb = match (true) {
    str_starts_with((string)$currentPath, '/patients')     => 'Benh nhan',
    str_starts_with((string)$currentPath, '/appointments') => 'Lich hen',
    $currentPath === '/health'                              => 'Health',
    default                                                 => 'Trang chu',
};

// Inline SVG icon set (fill = currentColor de doi mau theo active).
$icons = [
    'home'     => '<path d="M12 3l9 8h-3v9h-4v-6h-4v6H6v-9H3l9-8z"/>',
    'patients' => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>',
    'calendar' => '<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h5v5H7v-5z"/>',
    'health'   => '<path d="M3 13h2.5l1.5 4 3-9 2 5 1.5-2H21v-2h-7.5l-1.5 2-2.5-6L7 11H3v2z"/>',
];
$icon = fn(string $k) => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">' . ($icons[$k] ?? '') . '</svg>';
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Le Pham Hong Hien - 22110059">
    <title><?= View::e($title ?? 'ClinicDesk') ?></title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-shell">
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <a href="/" class="brand">
            <img src="/assets/img/logo-mark.png" alt="" class="brand-mark" width="30" height="30">
            <span class="brand-txt">Clinic<span class="hl">Desk</span><small>Clinic Appointment DB</small></span>
        </a>
        <button class="drawer-toggle" id="drawerToggle" aria-label="Dong menu" type="button">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
    </div>

    <nav class="side-nav" id="sideNav">
        <span class="nav-label">Quan ly</span>
        <a href="/" class="<?= $isActive('/', true) ?>"><?= $icon('home') ?><span>Trang chu</span></a>
        <a href="/patients" class="<?= $isActive('/patients') ?>"><?= $icon('patients') ?><span>Benh nhan</span></a>
        <a href="/appointments" class="<?= $isActive('/appointments') ?>"><?= $icon('calendar') ?><span>Lich hen</span></a>
        <span class="nav-label">He thong</span>
        <a href="/health" class="<?= $isActive('/health', true) ?>"><?= $icon('health') ?><span>Health</span></a>
    </nav>

    <div class="sidebar-foot">
        <span class="sf-dot"></span>
        <span class="sf-meta">
            <b>ClinicDesk</b>
            <small>PHP Lab05 &bull; 22110059</small>
        </span>
    </div>
</aside>

<div class="app-main">
    <header class="topbar">
        <div class="topbar-inner">
            <button class="topbar-burger" id="topbarBurger" aria-label="Mo menu" aria-controls="sidebar" type="button">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>
            <div class="crumb">
                <span class="crumb-home">ClinicDesk</span>
                <span class="crumb-sep">/</span>
                <span class="crumb-cur"><?= View::e($crumb) ?></span>
            </div>
            <div class="topbar-actions">
                <span class="topbar-hi">Le Pham Hong Hien &bull; <strong>22110059</strong></span>
            </div>
        </div>
    </header>

    <main class="content">
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
