<?php
use ClinicDesk\Core\View;
ob_start();
$debug = $debug ?? false;
?>
<div class="error-page">
    <img src="/assets/img/illus-error.png" alt="Error" loading="lazy">
    <div class="error-code">500</div>
    <div class="error-title">Da co loi xay ra</div>
    <p class="error-msg">
        He thong gap su co khi xu ly yeu cau cua ban. Loi da duoc ghi log,
        nhom ky thuat se kiem tra. Vui long thu lai sau.
    </p>
    <?php if ($debug && !empty($detail)): ?>
        <p class="error-msg"><strong>[DEBUG]</strong> <code><?= View::e($detail) ?></code></p>
    <?php endif; ?>
    <a href="/" class="btn btn-primary">🏠 Ve trang chu</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
