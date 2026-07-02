<?php
/** EduCRM - Module C: danh sach khoa hoc (grid anh + search + filter + sort + pagination). */
$categories = $categories ?? config('course_categories', []);
$perPageOptions = $perPageOptions ?? [10, 20, 50];
$manage = can('manage_courses');

$levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
$money = fn($v) => number_format((float) $v, 0, ',', '.');

$baseParams = ['q' => $q, 'category' => $category, 'active' => $active];
$sortLink = function (string $col) use ($baseParams, $sort, $direction, $perPage) {
    $dir = ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    return '/courses?' . http_build_query($baseParams + ['sort' => $col, 'direction' => $dir, 'per_page' => $perPage]);
};
$arrow = fn(string $col) => $sort === $col ? ($direction === 'asc' ? ' ^' : ' v') : '';
$pageLink = fn(int $p) => '/courses?' . http_build_query($baseParams + [
    'sort' => $sort, 'direction' => $direction, 'per_page' => $perPage, 'page' => $p,
]);
$exportLink = '/courses/export?' . http_build_query($baseParams);

$chips = [];
if ($q !== '')        { $chips[] = ['Từ khóa: ' . $q, '/courses?' . http_build_query(array_merge($baseParams, ['q' => '']))]; }
if ($category !== '') { $chips[] = ['Nhóm: ' . $category, '/courses?' . http_build_query(array_merge($baseParams, ['category' => '']))]; }
if ($active !== '')   { $chips[] = ['Trạng thái: ' . ($active === '1' ? 'Đang hiện' : 'Đã ẩn'), '/courses?' . http_build_query(array_merge($baseParams, ['active' => '']))]; }
?>
<div class="page-head">
    <div><h1>Quản lý Khóa học</h1><div class="sub">Tổng <?= (int)$total ?> khóa học</div></div>
    <div class="head-actions">
        <?php if (can('export')): ?><a href="<?= e($exportLink) ?>" class="btn btn-ghost">Xuất CSV</a><?php endif; ?>
        <?php if ($manage): ?><a href="/courses/trash" class="btn btn-ghost">Thùng rác</a><?php endif; ?>
        <?php if ($manage): ?><a href="/courses/create" class="btn btn-primary">+ Thêm khóa học</a><?php endif; ?>
    </div>
</div>

<div class="card">
    <form method="get" action="/courses" class="toolbar">
        <input type="text" name="q" class="grow" placeholder="Tìm tên / mô tả khóa học..." value="<?= e($q) ?>">
        <select name="category">
            <option value="">-- Tất cả nhóm --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="active">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="1" <?= $active === '1' ? 'selected' : '' ?>>Đang hiện</option>
            <option value="0" <?= $active === '0' ? 'selected' : '' ?>>Đã ẩn</option>
        </select>
        <select name="per_page" title="Số dòng / trang">
            <?php foreach ($perPageOptions as $opt): ?>
                <option value="<?= (int)$opt ?>" <?= (int)$perPage === (int)$opt ? 'selected' : '' ?>><?= (int)$opt ?>/trang</option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        <a href="/courses" class="btn btn-ghost btn-sm">Xóa lọc</a>
    </form>

    <div class="toolbar" style="margin-top:-4px;font-size:13px;">
        <span class="muted">Sắp xếp:</span>
        <a class="chip" href="<?= e($sortLink('name')) ?>">Tên<?= $arrow('name') ?></a>
        <a class="chip" href="<?= e($sortLink('price')) ?>">Học phí<?= $arrow('price') ?></a>
        <a class="chip" href="<?= e($sortLink('created_at')) ?>">Mới nhất<?= $arrow('created_at') ?></a>
    </div>

    <?php if (!empty($chips)): ?>
    <div class="filter-chips">
        <span class="muted">Đang lọc:</span>
        <?php foreach ($chips as $c): ?>
            <a class="chip" href="<?= e($c[1]) ?>"><?= e($c[0]) ?> <span class="chip-x">&times;</span></a>
        <?php endforeach; ?>
        <a class="chip chip-clear" href="/courses">Xóa tất cả</a>
    </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:200px;margin:0 auto 12px;display:block;">Không có khóa học nào khớp điều kiện.</div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($rows as $r): ?>
            <?php $img = trim((string) ($r['image'] ?? '')); ?>
            <div class="course-card<?= (int)$r['is_active'] === 1 ? '' : ' is-off' ?>">
                <a class="cc-thumb" href="/courses/view?id=<?= (int)$r['id'] ?>" title="Xem chi tiết: <?= e($r['name']) ?>">
                    <?php if ($img !== ''): ?>
                        <img src="/assets/img/courses/<?= e($img) ?>" alt="<?= e($r['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="cc-noimg"><?= e(mb_substr($r['name'], 0, 1)) ?></div>
                    <?php endif; ?>
                    <span class="cc-cat"><?= e($r['category']) ?></span>
                    <span class="cc-state <?= (int)$r['is_active'] === 1 ? 'on' : 'off' ?>"><?= (int)$r['is_active'] === 1 ? 'Đang hiện' : 'Đã ẩn' ?></span>
                    <span class="cc-view">Xem chi tiết</span>
                </a>
                <div class="cc-body">
                    <h3 class="cc-name"><a href="/courses/view?id=<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a></h3>
                    <div class="cc-meta">
                        <span class="cc-level lv-<?= e($r['level']) ?>"><?= e($levelLabels[$r['level']] ?? $r['level']) ?></span>
                        <span class="muted"><?= (int)$r['duration_weeks'] ?> tuần</span>
                    </div>
                    <?php if (!empty($r['description'])): ?>
                        <p class="cc-desc"><?= e($r['description']) ?></p>
                    <?php endif; ?>
                    <div class="cc-price"><?= $money($r['price']) ?> đ</div>
                </div>
                <?php if ($manage): ?>
                <div class="cc-actions">
                    <a href="/courses/edit?id=<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm">Sửa</a>
                    <form method="post" action="/courses/toggle" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-sm"><?= (int)$r['is_active'] === 1 ? 'Ẩn' : 'Hiện' ?></button>
                    </form>
                    <form method="post" action="/courses/delete" style="margin:0;" onsubmit="return confirm('Chuyển khóa học này vào thùng rác?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= e($pageLink($page - 1)) ?>">&laquo; Trước</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === $page): ?>
                <span class="current"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= e($pageLink($p)) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="<?= e($pageLink($page + 1)) ?>">Sau &raquo;</a>
        <?php endif; ?>
        <span class="muted">Trang <?= $page ?>/<?= $totalPages ?> &middot; <?= (int)$perPage ?>/trang</span>
    </div>
    <?php endif; ?>
</div>
