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
$funnelLabels = ['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'qualified' => 'Tiềm năng', 'converted' => 'Chuyển đổi'];
$sourceLabels = ['website' => 'Website', 'facebook' => 'Facebook', 'zalo' => 'Zalo', 'referral' => 'Giới thiệu', 'hotline' => 'Hotline'];

// Empty-state: tong cac gia tri = 0 -> chart trong (tranh day cot 0 trong nhu loi).
$leadsTotal   = array_sum($leadsPerDay);
$revenueTotal = array_sum($revenue);
?>
<div class="page-head">
    <div>
        <h1>Thống kê</h1>
        <div class="sub">Phân tích lead, chuyển đổi và doanh thu.</div>
    </div>
    <a href="/dashboard" class="btn btn-ghost">&laquo; Tổng quan</a>
</div>

<div class="analytics-grid">
    <!-- Lead theo ngay (14 ngay) - CSS bars -->
    <div class="card chart-card">
        <h2>Lead theo ngày <span class="chip">14 ngày</span></h2>
        <?php if ($leadsTotal <= 0): ?>
            <div class="chart-empty">Chưa có lead trong 14 ngày gần đây.</div>
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
        <h2>Phiếu chuyển đổi Lead</h2>
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
        <h2>Doanh thu (paid) <span class="chip">6 tháng</span></h2>
        <?php if ($revenueTotal <= 0): ?>
            <div class="chart-empty">Chưa có doanh thu đã thu trong 6 tháng gần đây.</div>
        <?php else: ?>
        <div class="revenue-bars">
            <?php foreach ($revenue as $ym => $amount): ?>
                <div class="rev-col" title="<?= e($ym) ?>: <?= $fmt($amount) ?> đ">
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
        <h2>Top 5 khóa học</h2>
        <?php if (empty($topCourses)): ?>
            <div class="empty" style="padding:20px;">Chưa có dữ liệu.</div>
        <?php else: ?>
        <div class="top-courses">
            <?php foreach ($topCourses as $i => $c): ?>
                <div class="tc-row">
                    <div class="tc-rank">#<?= $i + 1 ?></div>
                    <div class="tc-body">
                        <div class="tc-head"><span><?= e($c['course']) ?></span><strong><?= (int)$c['orders'] ?> phiếu</strong></div>
                        <div class="tc-track"><div class="tc-fill" style="width: <?= max(6, (int)round($c['orders'] / $maxCourse * 100)) ?>%"></div></div>
                        <div class="tc-rev"><?= $fmt($c['revenue']) ?> đ</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="analytics-grid">
    <div class="card">
        <h2 class="stat-h">Lead theo trạng thái</h2>
        <div class="status-pills">
            <?php foreach (config('lead_statuses') as $st): ?>
                <span class="sp"><span class="badge <?= e($st) ?>"><?= e($st) ?></span><b><?= $fmt($leadStats['byStatus'][$st] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2 class="stat-h">Phiếu học phí theo trạng thái</h2>
        <div class="status-pills">
            <?php foreach (config('order_statuses') as $st): ?>
                <span class="sp"><span class="badge <?= e($st) ?>"><?= e($st) ?></span><b><?= $fmt($orderStats['byStatus'][$st] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="analytics-grid">
    <div class="card">
        <h2 class="stat-h">Lead theo nguồn</h2>
        <div class="status-pills">
            <?php foreach (config('lead_sources') as $src): ?>
                <span class="sp"><span class="badge src-<?= e($src) ?>"><?= e($sourceLabels[$src] ?? $src) ?></span><b><?= $fmt($bySource[$src] ?? 0) ?></b></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
