<?php
// 405 Method Not Allowed error page
$methodList = implode(', ', $allowedMethods ?? []);
ob_start();
?>

<div class="error-page">
    <div class="error-code">405</div>
    <div class="error-title">Method Not Allowed</div>
    <p class="error-message">
        HTTP method bạn sử dụng không được phép cho route này.<br>
        <?php if (!empty($allowedMethods)): ?>
            Các method hợp lệ: 
            <code style="color: var(--accent-green); background: var(--bg-card); padding: 2px 8px; border-radius: 4px;">
                <?= htmlspecialchars($methodList) ?>
            </code>
        <?php endif; ?>
    </p>
    <a href="/" class="btn btn-primary">🏠 Về trang chủ</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
