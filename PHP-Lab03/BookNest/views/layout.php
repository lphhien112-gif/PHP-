<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BookNest — Hiệu sách trực tuyến mini với kiến trúc Front Controller, Router và Standard Response.">
    <meta name="author" content="Lê Phạm Hồng Hiên — 22110059">
    <title><?= htmlspecialchars($title ?? 'BookNest — Hiệu Sách Trực Tuyến') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>

<!-- Navigation -->
<nav class="navbar">
    <a href="/" class="navbar-brand">📚 Book<span>Nest</span></a>
    <ul class="navbar-links">
        <li><a href="/" class="<?= $currentPath === '/' ? 'active' : '' ?>">Trang chủ</a></li>
        <li><a href="/books" class="<?= $currentPath === '/books' ? 'active' : '' ?>">Sách</a></li>
        <li><a href="/books/create" class="<?= $currentPath === '/books/create' ? 'active' : '' ?>">Thêm sách</a></li>
        <li><a href="/health" class="<?= $currentPath === '/health' ? 'active' : '' ?>">Health</a></li>
        <li><a href="/login" class="<?= $currentPath === '/login' ? 'active' : '' ?>">Đăng nhập</a></li>
        <li><a href="http://localhost:8001" class="nav-admin-link" target="_blank">⚙️ Admin</a></li>
    </ul>
</nav>

<!-- Server Badge -->
<div class="server-badge">
    <span class="server-dot"></span>
    Server 1 • Port 8000 • User
</div>

<!-- Main Content -->
<main class="container">
    <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <p>📚 <strong>BookNest</strong> — Mini Bookstore Routing App &bull; PHP Lab03 &bull; 22110059 — Lê Phạm Hồng Hiên</p>
        <p style="margin-top: 4px;">Front Controller &bull; Router &bull; Standard Response &bull; <?= date('Y') ?></p>
        <div class="footer-servers">
            <span class="footer-server active">🟢 Server 1: User (Port 8000)</span>
            <span class="footer-server">🔵 Server 2: Admin (Port 8001)</span>
        </div>
    </div>
</footer>

</body>
</html>
