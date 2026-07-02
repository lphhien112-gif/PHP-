<?php
/** EduCRM - Tong quan: hero + cac o KPI nhanh (bieu do o trang /analytics). */
$fmt = fn($n) => number_format((float)$n, 0, ',', '.');
$pct = fn($n) => number_format((float)$n, 1, ',', '.');

$delta = $summary['revenueDeltaPct'] ?? null;   // null = thang truoc = 0 (khong so sanh)
if ($delta === null)      { $deltaCls = 'flat'; $deltaTxt = 'Chua co du lieu thang truoc'; }
elseif ($delta > 0)       { $deltaCls = 'up';   $deltaTxt = '▲ ' . $pct($delta) . '% so voi thang truoc'; }
elseif ($delta < 0)       { $deltaCls = 'down'; $deltaTxt = '▼ ' . $pct(abs($delta)) . '% so voi thang truoc'; }
else                      { $deltaCls = 'flat'; $deltaTxt = 'Khong doi so voi thang truoc'; }
?>
<div class="page-head">
    <div>
        <h1>Tong quan</h1>
        <div class="sub">Xin chao <?= e(current_user()['full_name']) ?>, day la tinh hinh trung tam hom nay.</div>
    </div>
    <div class="head-actions">
        <a href="/analytics" class="btn btn-ghost">Xem thong ke</a>
        <a href="/leads/create" class="btn btn-primary">+ Them lead moi</a>
    </div>
</div>

<div class="dash-hero card" style="margin-bottom: 24px;">
    <img src="/assets/img/hero-dashboard.png" alt="EduCRM Hero" loading="eager">
    <div>
        <h2 style="margin-top:0;">Chao mung tro lai!</h2>
        <p>He thong EduCRM giup ban quan ly toan bo vong doi hoc vien: tu nguon Lead tiem nang den khi chuyen doi thanh Order, theo doi tinh trang lop hoc va doanh thu mot cach truc quan.</p>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-leads.png" alt="Leads" loading="lazy">
        <div class="label">Tong lead</div>
        <div class="value"><?= $fmt($leadStats['total']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-system.png" alt="System" loading="lazy">
        <div class="label">Lead moi (new)</div>
        <div class="value amber"><?= $fmt($leadStats['byStatus']['new'] ?? 0) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-orders.png" alt="Orders" loading="lazy">
        <div class="label">Tong phieu</div>
        <div class="value"><?= $fmt($orderStats['total']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-revenue.png" alt="Revenue" loading="lazy">
        <div class="label">Doanh thu da thu (VND)</div>
        <div class="value green"><?= $fmt($orderStats['revenue']) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-courses.png" alt="Courses" loading="lazy">
        <div class="label">Tong khoa hoc (<?= $fmt($courseStats['active'] ?? 0) ?> dang mo)</div>
        <div class="value"><?= $fmt($courseStats['total'] ?? 0) ?></div>
    </div>
    <div class="stat">
        <img class="stat-art" src="/assets/img/stat-students.png" alt="Students" loading="lazy">
        <div class="label">Hoc vien chuyen doi</div>
        <div class="value green"><?= $fmt($leadStats['byStatus']['converted'] ?? 0) ?></div>
    </div>
</div>

<h2 class="stat-h" style="margin-bottom:12px;">Xu huong kinh doanh</h2>
<div class="stats">
    <div class="stat">
        <div class="label">Ty le chuyen doi</div>
        <div class="value"><?= $pct($summary['conversionRate']) ?>%</div>
        <div class="kpi-sub"><?= $fmt($summary['convertedCount']) ?>/<?= $fmt($summary['leadTotal']) ?> lead da chuyen doi</div>
    </div>
    <div class="stat">
        <div class="label">Ty le thanh toan</div>
        <div class="value"><?= $pct($summary['paymentRate']) ?>%</div>
        <div class="kpi-sub"><?= $fmt($summary['paidCount']) ?>/<?= $fmt($summary['orderTotal']) ?> phieu da thu</div>
    </div>
    <div class="stat">
        <div class="label">Doanh thu thang nay</div>
        <div class="value green"><?= $fmt($summary['revenueThisMonth']) ?></div>
        <div class="kpi-delta <?= $deltaCls ?>"><?= $deltaTxt ?></div>
    </div>
</div>
