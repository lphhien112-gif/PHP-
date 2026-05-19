<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BookNest') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <a href="/" class="navbar-brand">📚 Book<span>Nest</span></a>
    <ul class="navbar-links">
        <li><a href="/">Trang chủ</a></li>
        <li><a href="/books">Sách</a></li>
        <li><a href="/books/create">Thêm sách</a></li>
        <li><a href="/health">Health</a></li>
        <li><a href="/login">Đăng nhập</a></li>
    </ul>
</nav>

<!-- Main Content -->
<main class="container">
    <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer class="footer">
    <p>📚 <strong>BookNest</strong> — Mini Bookstore Routing App &bull; PHP Lab03 &bull; 22110059 — Lê Phạm Hồng Hiên</p>
    <p style="margin-top: 4px;">Front Controller &bull; Router &bull; Standard Response &bull; <?= date('Y') ?></p>
</footer>

</body>
</html>
