<?php
use ClinicDesk\Core\View;
ob_start();
?>
<div class="error-page">
    <img src="/assets/img/illus-error.png" alt="Error" loading="lazy">
    <div class="error-code">404</div>
    <div class="error-title">Khong tim thay trang</div>
    <p class="error-msg">
        Duong dan <code><?= View::e($path ?? '') ?></code> khong ton tai tren ClinicDesk.
        Vui long kiem tra lai URL.
    </p>
    <a href="/" class="btn btn-primary">🏠 Ve trang chu</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
