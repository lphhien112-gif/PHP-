<?php
/** EduCRM - Form dang nhap. */
?>
<img class="illus" src="/assets/img/illus-login.png" alt="Login">
<h1>Dang nhap</h1>
<p class="muted">EduCRM - He thong quan ly tu van &amp; hoc phi</p>

<form method="post" action="/login">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="username">Ten dang nhap</label>
        <input type="text" id="username" name="username" value="<?= e(old('username')) ?>" autofocus>
    </div>
    <div class="form-group" style="margin-top:14px;">
        <label for="password">Mat khau</label>
        <input type="password" id="password" name="password">
    </div>
    <?php if (error_of('login')): ?>
        <div class="field-error" style="margin-top:10px;"><?= e(error_of('login')) ?></div>
    <?php endif; ?>
    <label class="remember-row" style="display:flex;align-items:center;gap:8px;margin-top:14px;font-size:14px;cursor:pointer;">
        <input type="checkbox" name="remember" value="1" style="width:auto;margin:0;">
        <span>Ghi nho dang nhap (14 ngay)</span>
    </label>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" style="width:100%;">Dang nhap</button>
    </div>
</form>

<div class="demo">
    <strong>Tai khoan demo:</strong><br>
    admin / admin123 (quan tri - toan quyen)<br>
    manager / manager123 (quan ly - doi status, export, restore)<br>
    staff / staff123 (nhan vien - chi them/sua)
</div>
