<?php
/** EduCRM - Dashboard: thong ke nhanh. */
$fmt = fn($n) => number_format((float)$n, 0, ',', '.');
?>
<div class="page-head">
    <div>
        <h1>Tong quan</h1>
        <div class="sub">Xin chao <?= e(current_user()['full_name']) ?>, day la tinh hinh trung tam hom nay.</div>
    </div>
    <a href="/leads/create" class="btn btn-primary">+ Them lead moi</a>
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
</div>

<?php
/* ===== F5: Dashboard Analytics ===== */
$leadsPerDay = $analytics['leadsPerDay'] ?? [];
$funnel      = $analytics['funnel'] ?? [];
$revenue     = $analytics['revenue'] ?? [];
$topCourses  = $analytics['topCourses'] ?? [];

$maxLeads   = max(1, $leadsPerDay ? max($leadsPerDay) : 1);
$maxFunnel  = max(1, $funnel ? max($funnel) : 1);
$maxRevenue = max(1, $revenue ? max($revenue) : 1);
$maxCourse  = max(1, $topCourses ? max(array_column($topCourses, 'orders')) : 1);
$funnelLabels = ['new' => 'Moi', 'contacted' => 'Da lien he', 'qualified' => 'Tiem nang', 'converted' => 'Chuyen doi'];
?>

<div class="analytics-grid">
    <!-- Lead theo ngay (14 ngay) - CSS bars -->
    <div class="card chart-card">
        <h2>Lead theo ngay <span class="chip">14 ngay</span></h2>
        <div class="bar-chart">
            <?php foreach ($leadsPerDay as $day => $count): ?>
                <div class="bar-col" title="<?= e($day) ?>: <?= (int)$count ?> lead">
                    <div class="bar-track">
                        <div class="bar-fill" style="height: <?= max(3, (int)round($count / $maxLeads * 100)) ?>%"></div>
                    </div>
                    <div class="bar-val"><?= (int)$count ?></div>
                    <div class="bar-lbl"><?= e(date('d/m', strtotime($day))) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Phieu chuyen doi (funnel) -->
    <div class="card chart-card">
        <h2>Phieu chuyen doi Lead</h2>
        <div class="funnel">
            <?php foreach ($funnel as $stage => $count): ?>
                <div class="funnel-row">
                    <div class="funnel-lbl"><?= e($funnelLabels[$stage] ?? $stage) ?></div>
                    <div class="funnel-track">
                        <div class="funnel-fill <?= e($stage) ?>" style="width: <?= max(6, (int)round($count / $maxFunnel * 100)) ?>%">
                            <span><?= (int)$count ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="analytics-grid">
    <!-- Doanh thu 6 thang (inline SVG bars) -->
    <div class="card chart-card">
        <h2>Doanh thu (paid) <span class="chip">6 thang</span></h2>
        <div class="revenue-bars">
            <?php foreach ($revenue as $ym => $amount): ?>
                <div class="rev-col" title="<?= e($ym) ?>: <?= $fmt($amount) ?> d">
                    <div class="rev-track">
                        <div class="rev-fill" style="height: <?= max(3, (int)round($amount / $maxRevenue * 100)) ?>%"></div>
                    </div>
                    <div class="rev-val"><?= $amount >= 1000000 ? round($amount/1000000, 1) . 'tr' : $fmt($amount) ?></div>
                    <div class="bar-lbl"><?= e(date('m/y', strtotime($ym . '-01'))) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top 5 khoa hoc -->
    <div class="card chart-card">
        <h2>Top 5 khoa hoc</h2>
        <?php if (empty($topCourses)): ?>
            <div class="empty" style="padding:20px;">Chua co du lieu.</div>
        <?php else: ?>
        <div class="top-courses">
            <?php foreach ($topCourses as $i => $c): ?>
                <div class="tc-row">
                    <div class="tc-rank">#<?= $i + 1 ?></div>
                    <div class="tc-body">
                        <div class="tc-head"><span><?= e($c['course']) ?></span><strong><?= (int)$c['orders'] ?> phieu</strong></div>
                        <div class="tc-track"><div class="tc-fill" style="width: <?= max(6, (int)round($c['orders'] / $maxCourse * 100)) ?>%"></div></div>
                        <div class="tc-rev"><?= $fmt($c['revenue']) ?> d</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="analytics-grid">
    <div class="card">
        <h2 style="margin-top:0;font-size:18px;">Lead theo trang thai</h2>
        <table>
            <thead><tr><th>Trang thai</th><th>So luong</th></tr></thead>
            <tbody>
            <?php foreach (config('lead_statuses') as $st): ?>
                <tr>
                    <td><span class="badge <?= e($st) ?>"><?= e($st) ?></span></td>
                    <td><?= $fmt($leadStats['byStatus'][$st] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 style="margin-top:0;font-size:18px;">Phieu hoc phi theo trang thai</h2>
        <table>
            <thead><tr><th>Trang thai</th><th>So luong</th></tr></thead>
            <tbody>
            <?php foreach (config('order_statuses') as $st): ?>
                <tr>
                    <td><span class="badge <?= e($st) ?>"><?= e($st) ?></span></td>
                    <td><?= $fmt($orderStats['byStatus'][$st] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
