<?php
/** EduCRM - Form dang nhap. */
?>
<img class="illus" src="/assets/img/illus-login.png" alt="Login">
<h1>Đăng nhập</h1>
<p class="muted">EduCRM - Hệ thống quản lý tư vấn &amp; học phí</p>

<form method="post" action="/login">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="username">Tên đăng nhập</label>
        <input type="text" id="username" name="username" value="<?= e(old('username')) ?>" autofocus>
    </div>
    <div class="form-group" style="margin-top:14px;">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password">
    </div>
    <?php if (error_of('login')): ?>
        <div class="field-error" style="margin-top:10px;"><?= e(error_of('login')) ?></div>
    <?php endif; ?>
    <label class="remember-row" style="display:flex;align-items:center;gap:8px;margin-top:14px;font-size:14px;cursor:pointer;">
        <input type="checkbox" name="remember" value="1" style="width:auto;margin:0;">
        <span>Ghi nhớ đăng nhập (14 ngày)</span>
    </label>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" style="width:100%;">Đăng nhập</button>
    </div>
</form>

<div class="demo">
    <strong>Tài khoản demo:</strong><br>
    admin / admin123 (quản trị - toàn quyền)<br>
    manager / manager123 (quản lý - đổi status, export, restore)<br>
    staff / staff123 (nhân viên - chỉ thêm/sửa)
</div>
