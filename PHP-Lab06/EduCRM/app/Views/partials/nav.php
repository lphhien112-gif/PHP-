<?php
/** EduCRM - Sidebar dieu huong (admin shell). */
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$is = fn(string $p) => str_starts_with((string)$path, $p) ? 'active' : '';

// Inline SVG icon set (stroke = currentColor de doi mau theo active).
$icons = [
    'dashboard' => '<path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>',
    'leads'     => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>',
    'orders'    => '<path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>',
    'audit'     => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 4h5v2h-5V7zm0 4h5v2h-5v-2zM7 7h3v3H7V7zm0 4h3v3H7v-3z"/>',
    'public'    => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
];
$icon = fn(string $k) => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">' . ($icons[$k] ?? '') . '</svg>';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <a href="/dashboard" class="brand">
            <img src="/assets/img/logo-mark.png" alt="" class="brand-mark">
            <span class="brand-txt">EduCRM<small>Training Center CRM</small></span>
        </a>
        <button class="drawer-toggle" id="drawerToggle" aria-label="Mo menu" type="button">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
    </div>

    <nav class="side-nav" id="sideNav">
        <a href="/dashboard" class="<?= $is('/dashboard') ?>"><?= $icon('dashboard') ?><span>Tong quan</span></a>
        <a href="/leads" class="<?= $is('/leads') ?>"><?= $icon('leads') ?><span>Lead</span></a>
        <a href="/orders" class="<?= $is('/orders') ?>"><?= $icon('orders') ?><span>Phieu hoc phi</span></a>
        <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
        <a href="/audit" class="<?= $is('/audit') ?>"><?= $icon('audit') ?><span>Nhat ky</span></a>
        <?php endif; ?>
        <a href="/public-leads/create" class="<?= $is('/public-leads') ?>"><?= $icon('public') ?><span>Form cong khai</span></a>
    </nav>

    <?php if ($user): ?>
    <div class="sidebar-user">
        <div class="su-avatar"><?= e(mb_substr($user['full_name'], 0, 1)) ?></div>
        <div class="su-meta">
            <span class="su-name"><?= e($user['full_name']) ?></span>
            <span class="badge-role"><?= e($user['role']) ?></span>
        </div>
        <form method="post" action="/logout" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="btn-logout" title="Dang xuat" aria-label="Dang xuat">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            </button>
        </form>
    </div>
    <?php endif; ?>
</aside>
