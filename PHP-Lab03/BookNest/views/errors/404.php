<?php
// 404 Not Found error page
ob_start();
?>

<div class="error-page">
    <div class="error-code">404</div>
    <div class="error-title">Không tìm thấy trang</div>
    <p class="error-message">
        Đường dẫn <code style="color: var(--accent); background: var(--bg-card); padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($path ?? '') ?></code> 
        không tồn tại trên BookNest. Vui lòng kiểm tra lại URL.
    </p>
    <a href="/" class="btn btn-primary">🏠 Về trang chủ</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
