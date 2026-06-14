<?php
use ClinicDesk\Core\View;
ob_start();
$allowed = $allowedMethods ?? [];
?>
<div class="error-page">
    <img src="/assets/img/illus-error.png" alt="Error" loading="lazy">
    <div class="error-code">405</div>
    <div class="error-title">Phuong thuc khong duoc phep</div>
    <p class="error-msg">
        Route ton tai nhung HTTP method ban dung khong hop le.
        <?php if (!empty($allowed)): ?>
            Method duoc phep: <code><?= View::e(implode(', ', $allowed)) ?></code>.
        <?php endif; ?>
    </p>
    <a href="/" class="btn btn-primary">🏠 Ve trang chu</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
