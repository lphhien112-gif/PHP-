<?php
use ClinicDesk\Core\View;
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">➕ Them benh nhan</h1>
        <p class="page-subtitle">Nhap thong tin benh nhan moi. Email phai duy nhat.</p>
    </div>
    <a href="/patients" class="btn btn-ghost">← Quay lai danh sach</a>
</div>
<?php include __DIR__ . '/_form.php'; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
