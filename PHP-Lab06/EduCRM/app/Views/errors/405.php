<?php /** EduCRM - 405 Method Not Allowed */ ?>
<h1 style="font-size:64px;margin:0;color:#d97706;">405</h1>
<h2 style="margin-top:6px;">Phuong thuc khong duoc phep</h2>
<p class="muted">
    Duong dan ton tai nhung khong chap nhan HTTP method nay.
    <?php if (!empty($allowed)): ?>
        <br>Method hop le: <strong><?= e(implode(', ', $allowed)) ?></strong>
    <?php endif; ?>
</p>
<a href="/dashboard" class="btn btn-primary" style="margin-top:10px;">Ve trang chu</a>
