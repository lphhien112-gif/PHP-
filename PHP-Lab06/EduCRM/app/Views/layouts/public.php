<?php
/** EduCRM - Layout cho form cong khai (khong yeu cau dang nhap). */
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dang ky tu van') ?> - EduCRM</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <div class="public-wrap">
        <div class="public-hero">
            <h1>EduCRM</h1>
            <p>Trung tam dao tao - Dang ky tu van khoa hoc mien phi</p>
        </div>
        <?php partial('flash'); ?>
        <?= $content ?>
        <p style="text-align:center;margin-top:18px;font-size:13px;color:#64748b">
            Ban la nhan vien? <a href="/login">Dang nhap he thong</a>
        </p>
    </div>
</body>
</html>
