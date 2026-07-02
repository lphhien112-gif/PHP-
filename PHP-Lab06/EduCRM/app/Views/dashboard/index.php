<?php
/** EduCRM - Tong quan: hero + cac o KPI nhanh (bieu do o trang /analytics). */
$fmt = fn($n) => number_format((float)$n, 0, ',', '.');
$pct = fn($n) => number_format((float)$n, 1, ',', '.');

$delta = $summary['revenueDeltaPct'] ?? null;   // null = thang truoc = 0 (khong so sanh)
if ($delta === null)      { $deltaCls = 'flat'; $deltaTxt = 'Chưa có dữ liệu tháng trước'; }
elseif ($delta > 0)       { $deltaCls = 'up';   $deltaTxt = '▲ ' . $pct($delta) . '% so với tháng trước'; }
elseif ($delta < 0)       { $deltaCls = 'down'; $deltaTxt = '▼ ' . $pct(abs($delta)) . '% so với tháng trước'; }
else                      { $deltaCls = 'flat'; $deltaTxt = 'Không đổi so với tháng trước'; }
?>
<div class="page-head">
    <div>
        <h1>Tổng quan</h1>
        <div class="sub">Xin chào <?= e(current_user()['full_name']) ?>, đây là tình hình trung tâm hôm nay.</div>
    </div>
    <div class="head-actions">
        <a href="/analytics" class="btn btn-ghost">Xem thống kê</a>
        <a href="/leads/create" class="btn btn-primary">+ Thêm lead mới</a>
    </div>
</div>

<div class="dash-hero card" style="margin-bottom: 24px;">
    <img src="/assets/img/hero-dashboard.png" alt="EduCRM Hero" loading="eager">
    <div>
        <h2 style="margin-top:0;">Chào mừng trở lại!</h2>
        <p>Hệ thống EduCRM giúp bạn quản lý toàn bộ vòng đời học viên: từ nguồn Lead tiềm năng đến khi chuyển đổi thành Order, theo dõi tình trạng lớp học và doanh thu một cách trực quan.</p>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-leads.png" alt="Leads" loading="lazy">
        <div class="label">Tổng lead</div>
        <div class="value"><?= $fmt($leadStats['total']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-system.png" alt="System" loading="lazy">
        <div class="label">Lead mới (new)</div>
        <div class="value amber"><?= $fmt($leadStats['byStatus']['new'] ?? 0) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-orders.png" alt="Orders" loading="lazy">
        <div class="label">Tổng phiếu</div>
        <div class="value"><?= $fmt($orderStats['total']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-revenue.png" alt="Revenue" loading="lazy">
        <div class="label">Doanh thu đã thu (VND)</div>
        <div class="value green"><?= $fmt($orderStats['revenue']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-courses.png" alt="Courses" loading="lazy">
        <div class="label">Tổng khóa học (<?= $fmt($courseStats['active'] ?? 0) ?> đang mở)</div>
        <div class="value"><?= $fmt($courseStats['total'] ?? 0) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-students.png" alt="Students" loading="lazy">
        <div class="label">Học viên chuyển đổi</div>
        <div class="value green"><?= $fmt($leadStats['byStatus']['converted'] ?? 0) ?></div>
    </div>
</div>

<h2 class="stat-h" style="margin-bottom:12px;">Xu hướng kinh doanh</h2>
<div class="stats">
    <div class="stat">
        <div class="label">Tỷ lệ chuyển đổi</div>
        <div class="value"><?= $pct($summary['conversionRate']) ?>%</div>
        <div class="kpi-sub"><?= $fmt($summary['convertedCount']) ?>/<?= $fmt($summary['leadTotal']) ?> lead đã chuyển đổi</div>
    </div>
    <div class="stat">
        <div class="label">Tỷ lệ thanh toán</div>
        <div class="value"><?= $pct($summary['paymentRate']) ?>%</div>
        <div class="kpi-sub"><?= $fmt($summary['paidCount']) ?>/<?= $fmt($summary['orderTotal']) ?> phiếu đã thu</div>
    </div>
    <div class="stat">
        <div class="label">Doanh thu tháng này</div>
        <div class="value green"><?= $fmt($summary['revenueThisMonth']) ?></div>
        <div class="kpi-delta <?= $deltaCls ?>"><?= $deltaTxt ?></div>
    </div>
</div>
