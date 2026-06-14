<?php
/** Form dang nhap nhan vien — WorkshopHub. old()/field_error() tu helpers. */
?>
<div class="auth-card">
    <div class="auth-logo">
        <img src="/assets/img/logo-mark.png" alt="WorkshopHub logo">
        <span>Workshop<span class="hl">Hub</span></span>
    </div>
    <h1>Dang nhap nhan vien</h1>
    <p class="muted">Khu vuc danh cho nhan vien quan ly dang ky workshop.</p>

    <form action="/login" method="post" novalidate class="form">
        <div class="form-group">
            <label for="email">Email <span class="req">*</span></label>
            <input type="text" id="email" name="email" value="<?= h(old('email')) ?>"
                   class="<?= field_error('email') ? 'is-invalid' : '' ?>" autofocus>
            <?php if ($e = field_error('email')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Mat khau <span class="req">*</span></label>
            <input type="password" id="password" name="password"
                   class="<?= field_error('password') ? 'is-invalid' : '' ?>">
            <?php if ($e = field_error('password')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
        </div>

        <div class="form-group remember">
            <label class="check">
                <input type="checkbox" name="remember" value="1" disabled>
                <span>Ghi nho dang nhap (chi minh hoa — khong luu mat khau)</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block">Dang nhap</button>
        </div>
    </form>

    <div class="demo-hint">
        <strong>Tai khoan demo:</strong><br>
        Email: <code>staff@workshophub.vn</code><br>
        Mat khau: <code>Workshop@2026</code>
    </div>
</div>
