<?php
$methodList = implode(', ', $allowedMethods ?? []);
ob_start();
$pageTitle = '405 — Method Not Allowed';
?>

<div class="error-panel">
    <div class="error-code">405</div>
    <h2>Method Not Allowed</h2>
    <p>HTTP method không hợp lệ cho route này.</p>
    <?php if (!empty($allowedMethods)): ?>
        <p>Method hợp lệ: <code><?= htmlspecialchars($methodList) ?></code></p>
    <?php endif; ?>
    <a href="/" class="btn btn-primary">← Về Dashboard</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layout.php';
?>
