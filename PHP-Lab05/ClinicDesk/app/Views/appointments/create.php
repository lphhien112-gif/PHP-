<?php
use ClinicDesk\Core\View;
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">➕ Tao lich hen</h1>
        <p class="page-subtitle">Nhap thong tin lich hen moi. Ma lich hen phai duy nhat.</p>
    </div>
    <a href="/appointments" class="btn btn-ghost">← Quay lai danh sach</a>
</div>
<?php include __DIR__ . '/_form.php'; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
