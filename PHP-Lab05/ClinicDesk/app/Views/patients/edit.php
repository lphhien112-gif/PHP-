<?php
use ClinicDesk\Core\View;
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">✏️ Sua benh nhan #<?= (int)($old['id'] ?? 0) ?></h1>
        <p class="page-subtitle">Cap nhat thong tin benh nhan.</p>
    </div>
    <a href="/patients" class="btn btn-ghost">← Quay lai danh sach</a>
</div>
<?php include __DIR__ . '/_form.php'; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
