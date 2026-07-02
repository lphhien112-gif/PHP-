<?php
/**
 * EduCRM - Layout chinh (admin shell: sidebar + topbar + content).
 * Bien nhan vao: $title, $content.
 */
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Breadcrumb don gian theo segment dau.
$crumb = match (true) {
    str_starts_with((string)$path, '/dashboard')     => 'Tong quan',
    str_starts_with((string)$path, '/analytics')     => 'Thong ke',
    str_starts_with((string)$path, '/courses')        => 'Khoa hoc',
    str_starts_with((string)$path, '/leads/trash')    => 'Lead / Thung rac',
    str_starts_with((string)$path, '/leads')          => 'Lead',
    str_starts_with((string)$path, '/orders/trash')   => 'Phieu hoc phi / Thung rac',
    str_starts_with((string)$path, '/orders')         => 'Phieu hoc phi',
    str_starts_with((string)$path, '/audit')          => 'Nhat ky hoat dong',
    default                                            => 'EduCRM',
};
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'EduCRM') ?> - EduCRM</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body class="app-shell">
    <?php partial('nav'); ?>
    <div class="app-main">
        <header class="topbar">
            <div class="topbar-inner">
                <button class="topbar-burger" id="topbarBurger" aria-label="Mo menu" aria-controls="sidebar" type="button">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
                </button>
                <div class="crumb">
                    <span class="crumb-home">EduCRM</span>
                    <span class="crumb-sep">/</span>
                    <span class="crumb-cur"><?= e($crumb) ?></span>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-hi">Xin chao, <strong><?= e($user['full_name'] ?? 'Khach') ?></strong></span>
                </div>
            </div>
        </header>
        <main class="content">
            <div class="content-inner">
                <?php partial('flash'); ?>
                <?= $content ?>
            </div>
        </main>
    </div>
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <script>
    (function () {
        var t  = document.getElementById('drawerToggle');   // nut trong drawer (dong)
        var tb = document.getElementById('topbarBurger');    // nut tren topbar (luon thay - mo)
        var sb = document.getElementById('sidebar');
        var bd = document.getElementById('drawerBackdrop');
        function toggle() { sb && sb.classList.toggle('open'); bd && bd.classList.toggle('show'); }
        function close()  { sb && sb.classList.remove('open'); bd && bd.classList.remove('show'); }
        if (t)  t.addEventListener('click', toggle);
        if (tb) tb.addEventListener('click', toggle);
        if (bd) bd.addEventListener('click', close);
    })();

    // Trang thai "dang xu ly" khi submit form: khoa nut bam + spinner
    // -> phan hoi truc quan + chong double-submit (tao trung lead/phieu).
    (function () {
        document.addEventListener('submit', function (ev) {
            var form = ev.target;
            // Bo qua neu submit da bi huy (vd: onsubmit confirm() tra false) -> tranh ket nut.
            if (ev.defaultPrevented) return;
            if (!(form instanceof HTMLFormElement) || form.hasAttribute('data-no-loading')) return;
            // Nut da kich hoat submit (event.submitter) hoac nut submit dau tien.
            var btn = ev.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
            if (!btn || btn.classList.contains('is-loading')) return;

            // Doi nhan cho nut primary (create/edit) sang "Dang luu..." (hoac data-loading).
            if (btn.classList.contains('btn-primary') && btn.tagName === 'BUTTON') {
                btn.dataset.origLabel = btn.textContent;
                btn.textContent = btn.getAttribute('data-loading') || 'Dang luu...';
            }
            btn.classList.add('is-loading');
            // Khoa nut SAU khi submit da khoi chay -> gia tri nut van duoc gui di.
            setTimeout(function () { btn.disabled = true; }, 0);

            // Loc/tim (form GET): mo noi dung trong luc tai lai trang -> co cam giac "dang loc".
            if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') {
                var inner = document.querySelector('.content-inner');
                if (inner) inner.classList.add('is-filtering');
            }
        });
    })();
    </script>
</body>
</html>
