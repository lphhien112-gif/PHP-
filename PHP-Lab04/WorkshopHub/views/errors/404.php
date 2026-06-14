<?php
/** 404 Not Found — WorkshopHub. Bien: $path */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found — WorkshopHub</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<main class="container error-page">
    <img src="/assets/img/illus-error.png" alt="Page Not Found">
    <div class="error-code">404</div>
    <h1>Khong tim thay trang</h1>
    <p class="muted">Duong dan <code><?= h($path ?? '') ?></code> khong ton tai tren WorkshopHub.</p>
    <a href="/" class="btn btn-primary">Ve trang chu</a>
</main>
</body>
</html>
