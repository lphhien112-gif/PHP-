<?php
ob_start();
$pageTitle = '404 — Không tìm thấy';
?>

<div class="error-panel">
    <div class="error-code">404</div>
    <h2>Không tìm thấy trang</h2>
    <p>Đường dẫn <code><?= htmlspecialchars($path ?? '') ?></code> không tồn tại trong Admin Panel.</p>
    <a href="/" class="btn btn-primary">← Về Dashboard</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layout.php';
?>
