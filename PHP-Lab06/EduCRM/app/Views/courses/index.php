<?php
/** EduCRM - Module C: danh sach khoa hoc (grid anh + search + filter + sort + pagination). */
$categories = $categories ?? config('course_categories', []);
$perPageOptions = $perPageOptions ?? [10, 20, 50];
$manage = can('manage_courses');

$levelLabels = ['beginner' => 'Co ban', 'intermediate' => 'Trung cap', 'advanced' => 'Nang cao'];
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
if ($q !== '')        { $chips[] = ['Tu khoa: ' . $q, '/courses?' . http_build_query(array_merge($baseParams, ['q' => '']))]; }
if ($category !== '') { $chips[] = ['Nhom: ' . $category, '/courses?' . http_build_query(array_merge($baseParams, ['category' => '']))]; }
if ($active !== '')   { $chips[] = ['Trang thai: ' . ($active === '1' ? 'Dang hien' : 'Da an'), '/courses?' . http_build_query(array_merge($baseParams, ['active' => '']))]; }
?>
<div class="page-head">
    <div><h1>Quan ly Khoa hoc</h1><div class="sub">Tong <?= (int)$total ?> khoa hoc</div></div>
    <div class="head-actions">
        <?php if (can('export')): ?><a href="<?= e($exportLink) ?>" class="btn btn-ghost">Xuat CSV</a><?php endif; ?>
        <?php if ($manage): ?><a href="/courses/trash" class="btn btn-ghost">Thung rac</a><?php endif; ?>
        <?php if ($manage): ?><a href="/courses/create" class="btn btn-primary">+ Them khoa hoc</a><?php endif; ?>
    </div>
</div>

<div class="card">
    <form method="get" action="/courses" class="toolbar">
        <input type="text" name="q" class="grow" placeholder="Tim ten / mo ta khoa hoc..." value="<?= e($q) ?>">
        <select name="category">
            <option value="">-- Tat ca nhom --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="active">
            <option value="">-- Tat ca trang thai --</option>
            <option value="1" <?= $active === '1' ? 'selected' : '' ?>>Dang hien</option>
            <option value="0" <?= $active === '0' ? 'selected' : '' ?>>Da an</option>
        </select>
        <select name="per_page" title="So dong / trang">
            <?php foreach ($perPageOptions as $opt): ?>
                <option value="<?= (int)$opt ?>" <?= (int)$perPage === (int)$opt ? 'selected' : '' ?>><?= (int)$opt ?>/trang</option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Loc</button>
        <a href="/courses" class="btn btn-ghost btn-sm">Xoa loc</a>
    </form>

    <div class="toolbar" style="margin-top:-4px;font-size:13px;">
        <span class="muted">Sap xep:</span>
        <a class="chip" href="<?= e($sortLink('name')) ?>">Ten<?= $arrow('name') ?></a>
        <a class="chip" href="<?= e($sortLink('price')) ?>">Hoc phi<?= $arrow('price') ?></a>
        <a class="chip" href="<?= e($sortLink('created_at')) ?>">Moi nhat<?= $arrow('created_at') ?></a>
    </div>

    <?php if (!empty($chips)): ?>
    <div class="filter-chips">
        <span class="muted">Dang loc:</span>
        <?php foreach ($chips as $c): ?>
            <a class="chip" href="<?= e($c[1]) ?>"><?= e($c[0]) ?> <span class="chip-x">&times;</span></a>
        <?php endforeach; ?>
        <a class="chip chip-clear" href="/courses">Xoa tat ca</a>
    </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:200px;margin:0 auto 12px;display:block;">Khong co khoa hoc nao khop dieu kien.</div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($rows as $r): ?>
            <?php $img = trim((string) ($r['image'] ?? '')); ?>
            <div class="course-card<?= (int)$r['is_active'] === 1 ? '' : ' is-off' ?>">
                <a class="cc-thumb" href="/courses/view?id=<?= (int)$r['id'] ?>" title="Xem chi tiet: <?= e($r['name']) ?>">
                    <?php if ($img !== ''): ?>
                        <img src="/assets/img/courses/<?= e($img) ?>" alt="<?= e($r['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="cc-noimg"><?= e(mb_substr($r['name'], 0, 1)) ?></div>
                    <?php endif; ?>
                    <span class="cc-cat"><?= e($r['category']) ?></span>
                    <span class="cc-state <?= (int)$r['is_active'] === 1 ? 'on' : 'off' ?>"><?= (int)$r['is_active'] === 1 ? 'Dang hien' : 'Da an' ?></span>
                    <span class="cc-view">Xem chi tiet</span>
                </a>
                <div class="cc-body">
                    <h3 class="cc-name"><a href="/courses/view?id=<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a></h3>
                    <div class="cc-meta">
                        <span class="cc-level lv-<?= e($r['level']) ?>"><?= e($levelLabels[$r['level']] ?? $r['level']) ?></span>
                        <span class="muted"><?= (int)$r['duration_weeks'] ?> tuan</span>
                    </div>
                    <?php if (!empty($r['description'])): ?>
                        <p class="cc-desc"><?= e($r['description']) ?></p>
                    <?php endif; ?>
                    <div class="cc-price"><?= $money($r['price']) ?> d</div>
                </div>
                <?php if ($manage): ?>
                <div class="cc-actions">
                    <a href="/courses/edit?id=<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm">Sua</a>
                    <form method="post" action="/courses/toggle" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-sm"><?= (int)$r['is_active'] === 1 ? 'An' : 'Hien' ?></button>
                    </form>
                    <form method="post" action="/courses/delete" style="margin:0;" onsubmit="return confirm('Chuyen khoa hoc nay vao thung rac?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xoa</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= e($pageLink($page - 1)) ?>">&laquo; Truoc</a>
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
