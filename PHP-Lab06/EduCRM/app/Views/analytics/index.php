<?php
/** EduCRM - Thong ke (F5): bieu do phan tich (CSS/inline, khong dung thu vien JS). */
$fmt = fn($n) => number_format((float)$n, 0, ',', '.');

$leadsPerDay = $analytics['leadsPerDay'] ?? [];
$funnel      = $analytics['funnel'] ?? [];
$revenue     = $analytics['revenue'] ?? [];
$topCourses  = $analytics['topCourses'] ?? [];

$bySource    = $analytics['bySource'] ?? [];

$maxLeads   = max(1, $leadsPerDay ? max($leadsPerDay) : 1);
$maxFunnel  = max(1, $funnel ? max($funnel) : 1);
$maxRevenue = max(1, $revenue ? max($revenue) : 1);
$maxCourse  = max(1, $topCourses ? max(array_column($topCourses, 'orders')) : 1);
$funnelLabels = ['new' => 'Moi', 'contacted' => 'Da lien he', 'qualified' => 'Tiem nang', 'converted' => 'Chuyen doi'];
$sourceLabels = ['website' => 'Website', 'facebook' => 'Facebook', 'zalo' => 'Zalo', 'referral' => 'Gioi thieu', 'hotline' => 'Hotline'];

// Empty-state: tong cac gia tri = 0 -> chart trong (tranh day cot 0 trong nhu loi).
$leadsTotal   = array_sum($leadsPerDay);
$revenueTotal = array_sum($revenue);
?>
<div class="page-head">
    <div>
        <h1>Thong ke</h1>
        <div class="sub">Phan tich lead, chuyen doi va doanh thu.</div>
    </div>
    <a href="/dashboard" class="btn btn-ghost">&laquo; Tong quan</a>
</div>

<div class="analytics-grid">
    <!-- Lead theo ngay (14 ngay) - CSS bars -->
    <div class="card chart-card">
        <h2>Lead theo ngay <span class="chip">14 ngay</span></h2>
        <?php if ($leadsTotal <= 0): ?>
            <div class="chart-empty">Chua co lead trong 14 ngay gan day.</div>
        <?php else: ?>
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
        <?php endif; ?>
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
    <!-- Doanh thu 6 thang -->
    <div class="card chart-card">
        <h2>Doanh thu (paid) <span class="chip">6 thang</span></h2>
        <?php if ($revenueTotal <= 0): ?>
            <div class="chart-empty">Chua co doanh thu da thu trong 6 thang gan day.</div>
        <?php else: ?>
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
        <?php endif; ?>
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
        <h2 class="stat-h">Lead theo trang thai</h2>
        <div class="status-pills">
            <?php foreach (config('lead_statuses') as $st): ?>
                <span class="sp"><span class="badge <?= e($st) ?>"><?= e($st) ?></span><b><?= $fmt($leadStats['byStatus'][$st] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2 class="stat-h">Phieu hoc phi theo trang thai</h2>
        <div class="status-pills">
            <?php foreach (config('order_statuses') as $st): ?>
                <span class="sp"><span class="badge <?= e($st) ?>"><?= e($st) ?></span><b><?= $fmt($orderStats['byStatus'][$st] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="analytics-grid">
    <div class="card">
        <h2 class="stat-h">Lead theo nguon</h2>
        <div class="status-pills">
            <?php foreach (config('lead_sources') as $src): ?>
                <span class="sp"><span class="badge src-<?= e($src) ?>"><?= e($sourceLabels[$src] ?? $src) ?></span><b><?= $fmt($bySource[$src] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
