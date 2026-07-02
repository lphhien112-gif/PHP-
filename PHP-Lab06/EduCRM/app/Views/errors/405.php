<?php /** EduCRM - 405 Method Not Allowed */ ?>
<h1 style="font-size:64px;margin:0;color:#d97706;">405</h1>
<h2 style="margin-top:6px;">Phương thức không được phép</h2>
<p class="muted">
    Đường dẫn tồn tại nhưng không chấp nhận HTTP method này.
    <?php if (!empty($allowed)): ?>
        <br>Method hợp lệ: <strong><?= e(implode(', ', $allowed)) ?></strong>
    <?php endif; ?>
</p>
<a href="/dashboard" class="btn btn-primary" style="margin-top:10px;">Về trang chủ</a>
