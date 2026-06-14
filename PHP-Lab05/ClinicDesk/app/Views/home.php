<?php
use ClinicDesk\Core\View;
ob_start();
?>
<div class="clinic-hero" style="text-align: center; margin-bottom: 20px;">
    <img src="/assets/img/hero-clinic.png" alt="Clinic Hero" loading="eager">
</div>
<div class="page-header">
    <div>
        <h1 class="page-title">🩺 ClinicDesk Dashboard</h1>
        <p class="page-subtitle">Mini Clinic Appointment DB App - quan ly benh nhan va lich hen kham</p>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="num"><?= $stats['patients'] !== null ? (int)$stats['patients'] : '--' ?></div>
        <div class="lbl">Benh nhan</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $stats['appointments'] !== null ? (int)$stats['appointments'] : '--' ?></div>
        <div class="lbl">Lich hen</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color: <?= $stats['connected'] ? 'var(--green)' : 'var(--red)' ?>">
            <?= $stats['connected'] ? '● Online' : '● Offline' ?>
        </div>
        <div class="lbl">Trang thai Database</div>
    </div>
</div>

<div class="module-grid">
    <div class="module-card">
        <h3>👤 Module Benh nhan</h3>
        <p>Quan ly ho so benh nhan: ho ten, email (unique), so dien thoai, gioi tinh, dia chi. Co tim kiem, sap xep va phan trang.</p>
        <a href="/patients" class="btn btn-primary">Mo danh sach benh nhan</a>
    </div>
    <div class="module-card">
        <h3>📅 Module Lich hen</h3>
        <p>Quan ly lich hen kham: ma lich hen (unique), benh nhan, chuyen khoa, ngay gio, trang thai. Co loc theo trang thai.</p>
        <a href="/appointments" class="btn btn-primary">Mo danh sach lich hen</a>
    </div>
    <div class="module-card">
        <h3>❤️ Health Check</h3>
        <p>Kiem tra ket noi Database qua JSON. Tra status=ok va database=connected neu PDO chay duoc SELECT 1.</p>
        <a href="/health" class="btn btn-ghost">Xem /health (JSON)</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
