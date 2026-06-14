<?php
/** 405 Method Not Allowed — WorkshopHub. Bien: $allowedMethods */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 Method Not Allowed — WorkshopHub</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<main class="container error-page">
    <img src="/assets/img/illus-error.png" alt="Method Not Allowed">
    <div class="error-code">405</div>
    <h1>Method khong duoc phep</h1>
    <p class="muted">
        Duong dan co ton tai nhung HTTP method ban dung khong hop le.
        <?php if (!empty($allowedMethods)): ?>
            <br>Method duoc phep: <code><?= h(implode(', ', $allowedMethods)) ?></code>.
        <?php endif; ?>
    </p>
    <a href="/" class="btn btn-primary">Ve trang chu</a>
</main>
</body>
</html>
