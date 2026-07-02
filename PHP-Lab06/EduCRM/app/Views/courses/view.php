<?php
/** EduCRM - Module C: trang chi tiet khoa hoc (doc mo ta). */
$levelLabels = ['beginner' => 'Co ban', 'intermediate' => 'Trung cap', 'advanced' => 'Nang cao'];
$money = fn($v) => number_format((float) $v, 0, ',', '.');
$img   = trim((string) ($course['image'] ?? ''));
$active = (int) $course['is_active'] === 1;
$weeks  = (int) $course['duration_weeks'];
$perWeek = $weeks > 0 ? round(((float) $course['price']) / $weeks) : 0;

// "Ban se hoc duoc gi" - moi dong 1 y (bo dong trong).
$outcomes = array_values(array_filter(array_map('trim', explode("\n", (string) ($course['outcomes'] ?? ''))), fn($s) => $s !== ''));
$hasDesc  = trim((string) $course['description']) !== '';
?>
<div class="page-head">
    <div>
        <div class="crumb"><a href="/courses">Khoa hoc</a> <span>/</span> <?= e($course['name']) ?></div>
        <h1><?= e($course['name']) ?></h1>
    </div>
    <div class="head-actions">
        <a href="/courses" class="btn btn-ghost">&laquo; Danh sach</a>
        <?php if (can('manage_courses')): ?>
        <a href="/courses/edit?id=<?= (int)$course['id'] ?>" class="btn btn-primary">Sua khoa hoc</a>
        <?php endif; ?>
    </div>
</div>

<div class="card cd-hero">
    <div class="cd-poster<?= $img === '' ? ' is-empty' : '' ?>">
        <?php if ($img !== ''): ?>
            <img src="/assets/img/courses/<?= e($img) ?>" alt="<?= e($course['name']) ?>">
        <?php else: ?>
            <span class="cd-poster-letter"><?= e(mb_substr($course['name'], 0, 1)) ?></span>
        <?php endif; ?>
    </div>
    <div class="cd-info">
        <div class="cd-badges">
            <span class="cc-cat" style="position:static;"><?= e($course['category']) ?></span>
            <span class="cc-level lv-<?= e($course['level']) ?>"><?= e($levelLabels[$course['level']] ?? $course['level']) ?></span>
            <span class="cc-state <?= $active ? 'on' : 'off' ?>" style="position:static;"><?= $active ? 'Dang mo' : 'Tam an' ?></span>
        </div>

        <div class="cd-price"><?= $money($course['price']) ?> <small>d / khoa</small></div>

        <div class="cd-facts">
            <div><span class="k">Nhom</span><span class="v"><?= e($course['category']) ?></span></div>
            <div><span class="k">Trinh do</span><span class="v"><?= e($levelLabels[$course['level']] ?? $course['level']) ?></span></div>
            <div><span class="k">Thoi luong</span><span class="v"><?= $weeks ?> tuan</span></div>
            <div><span class="k">Hoc phi / tuan</span><span class="v"><?= $money($perWeek) ?> d</span></div>
            <div><span class="k">Ngay tao</span><span class="v"><?= e($course['created_at']) ?></span></div>
            <div><span class="k">Trang thai</span><span class="v"><?= $active ? 'Hien tren form dang ky' : 'An khoi form' ?></span></div>
        </div>
    </div>
</div>

<div class="detail-cols">
    <div class="card cd-desc">
        <h2>Gioi thieu khoa hoc</h2>
        <?php if ($hasDesc): ?>
            <p class="cd-lead"><?= nl2br(e($course['description'])) ?></p>
        <?php elseif (!$outcomes): ?>
            <p class="muted">Khoa hoc nay chua co mo ta.
                <?php if (can('manage_courses')): ?><a href="/courses/edit?id=<?= (int)$course['id'] ?>">Them mo ta</a>.<?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ($outcomes): ?>
            <h3 class="cd-sub">Ban se hoc duoc gi</h3>
            <ul class="cd-outcomes">
                <?php foreach ($outcomes as $o): ?>
                    <li><?= e($o) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card cd-usage">
        <h2>Muc do quan tam</h2>
        <div class="u-grid">
            <div class="u-item">
                <div class="u-val"><?= $money($usage['leads']) ?></div>
                <div class="u-lbl">Lead quan tam</div>
            </div>
            <div class="u-item">
                <div class="u-val"><?= $money($usage['orders']) ?></div>
                <div class="u-lbl">Phieu da tao</div>
            </div>
            <div class="u-item">
                <div class="u-val green"><?= $money($usage['revenue']) ?></div>
                <div class="u-lbl">Doanh thu da thu (VND)</div>
            </div>
        </div>
        <p class="muted" style="margin:12px 0 0;font-size:13px;">So lieu tinh tu cac lead/phieu dang tham chieu ten khoa hoc nay.</p>
    </div>
</div>
