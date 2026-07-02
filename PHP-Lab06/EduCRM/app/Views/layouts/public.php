<?php
/** EduCRM - Layout cho form cong khai (khong yeu cau dang nhap). */
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Đăng ký tư vấn') ?> - EduCRM</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <div class="public-wrap">
        <div class="public-hero">
            <h1>EduCRM</h1>
            <p>Trung tâm đào tạo - Đăng ký tư vấn khóa học miễn phí</p>
        </div>
        <?php partial('flash'); ?>
        <?= $content ?>
        <p style="text-align:center;margin-top:18px;font-size:13px;color:#64748b">
            Bạn là nhân viên? <a href="/login">Đăng nhập hệ thống</a>
        </p>
    </div>
</body>
</html>
