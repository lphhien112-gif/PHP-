<?php
/** EduCRM - Layout cho trang loi 404/405/500. */
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Loi') ?> - EduCRM</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card" style="text-align:center;">
            <?= $content ?>
        </div>
    </div>
</body>
</html>
