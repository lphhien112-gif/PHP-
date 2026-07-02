<?php
/** EduCRM - Module C: trang chi tiet khoa hoc (doc mo ta). */
$levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
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
        <div class="crumb"><a href="/courses">Khóa học</a> <span>/</span> <?= e($course['name']) ?></div>
        <h1><?= e($course['name']) ?></h1>
    </div>
    <div class="head-actions">
        <a href="/courses" class="btn btn-ghost">&laquo; Danh sách</a>
        <?php if (can('manage_courses')): ?>
        <a href="/courses/edit?id=<?= (int)$course['id'] ?>" class="btn btn-primary">Sửa khóa học</a>
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
            <span class="cc-state <?= $active ? 'on' : 'off' ?>" style="position:static;"><?= $active ? 'Đang mở' : 'Tạm ẩn' ?></span>
        </div>

        <div class="cd-price"><?= $money($course['price']) ?> <small>đ / khóa</small></div>

        <div class="cd-facts">
            <div><span class="k">Nhóm</span><span class="v"><?= e($course['category']) ?></span></div>
            <div><span class="k">Trình độ</span><span class="v"><?= e($levelLabels[$course['level']] ?? $course['level']) ?></span></div>
            <div><span class="k">Thời lượng</span><span class="v"><?= $weeks ?> tuần</span></div>
            <div><span class="k">Học phí / tuần</span><span class="v"><?= $money($perWeek) ?> đ</span></div>
            <div><span class="k">Ngày tạo</span><span class="v"><?= e($course['created_at']) ?></span></div>
            <div><span class="k">Trạng thái</span><span class="v"><?= $active ? 'Hiện trên form đăng ký' : 'Ẩn khỏi form' ?></span></div>
        </div>
    </div>
</div>

<div class="detail-cols">
    <div class="card cd-desc">
        <h2>Giới thiệu khóa học</h2>
        <?php if ($hasDesc): ?>
            <p class="cd-lead"><?= nl2br(e($course['description'])) ?></p>
        <?php elseif (!$outcomes): ?>
            <p class="muted">Khóa học này chưa có mô tả.
                <?php if (can('manage_courses')): ?><a href="/courses/edit?id=<?= (int)$course['id'] ?>">Thêm mô tả</a>.<?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ($outcomes): ?>
            <h3 class="cd-sub">Bạn sẽ học được gì</h3>
            <ul class="cd-outcomes">
                <?php foreach ($outcomes as $o): ?>
                    <li><?= e($o) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card cd-usage">
        <h2>Mức độ quan tâm</h2>
        <div class="u-grid">
            <div class="u-item">
                <div class="u-val"><?= $money($usage['leads']) ?></div>
                <div class="u-lbl">Lead quan tâm</div>
            </div>
            <div class="u-item">
                <div class="u-val"><?= $money($usage['orders']) ?></div>
                <div class="u-lbl">Phiếu đã tạo</div>
            </div>
            <div class="u-item">
                <div class="u-val green"><?= $money($usage['revenue']) ?></div>
                <div class="u-lbl">Doanh thu đã thu (VND)</div>
            </div>
        </div>
        <p class="muted" style="margin:12px 0 0;font-size:13px;">Số liệu tính từ các lead/phiếu đang tham chiếu tên khóa học này.</p>
    </div>
</div>
