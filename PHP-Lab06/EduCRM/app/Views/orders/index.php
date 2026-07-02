<?php
/** EduCRM - Module B: danh sach phieu (search + filter + date-range + sort + pagination + bulk). */
$created_from = $created_from ?? '';
$created_to   = $created_to ?? '';
$perPageOptions = $perPageOptions ?? [10, 20, 50];
$money = fn($n) => number_format((float)$n, 0, ',', '.');

$baseParams = [
    'q' => $q, 'status' => $status,
    'created_from' => $created_from, 'created_to' => $created_to,
];
$sortLink = function (string $col) use ($baseParams, $sort, $direction, $perPage) {
    $dir = ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    return '/orders?' . http_build_query($baseParams + ['sort' => $col, 'direction' => $dir, 'per_page' => $perPage]);
};
$arrow = fn(string $col) => $sort === $col ? ($direction === 'asc' ? ' ^' : ' v') : '';
$pageLink = fn(int $p) => '/orders?' . http_build_query($baseParams + [
    'sort' => $sort, 'direction' => $direction, 'per_page' => $perPage, 'page' => $p,
]);
$exportLink = '/orders/export?' . http_build_query($baseParams);

$chips = [];
if ($q !== '')            { $chips[] = ['Từ khóa: ' . $q, '/orders?' . http_build_query(array_merge($baseParams, ['q' => '']))]; }
if ($status !== '')       { $chips[] = ['Trạng thái: ' . $status, '/orders?' . http_build_query(array_merge($baseParams, ['status' => '']))]; }
if ($created_from !== '') { $chips[] = ['Từ ngày: ' . $created_from, '/orders?' . http_build_query(array_merge($baseParams, ['created_from' => '']))]; }
if ($created_to !== '')   { $chips[] = ['Đến ngày: ' . $created_to, '/orders?' . http_build_query(array_merge($baseParams, ['created_to' => '']))]; }
?>
<div class="page-head">
    <div><h1>Quản lý Phiếu học phí</h1><div class="sub">Tổng <?= (int)$total ?> phiếu</div></div>
    <div class="head-actions">
        <?php if (can('export')): ?><a href="<?= e($exportLink) ?>" class="btn btn-ghost">Xuất CSV</a><?php endif; ?>
        <?php if (can('trash')): ?><a href="/orders/trash" class="btn btn-ghost">Thùng rác</a><?php endif; ?>
        <a href="/orders/create" class="btn btn-primary">+ Tạo phiếu</a>
    </div>
</div>

<div class="card">
    <form method="get" action="/orders" class="toolbar">
        <input type="text" name="q" class="grow" placeholder="Tìm mã phiếu, khóa học, tên học viên..." value="<?= e($q) ?>">
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <?php foreach (config('order_statuses') as $st): ?>
                <option value="<?= e($st) ?>" <?= $status === $st ? 'selected' : '' ?>><?= e($st) ?></option>
            <?php endforeach; ?>
        </select>
        <label class="date-field">Từ <input type="date" name="created_from" value="<?= e($created_from) ?>"></label>
        <label class="date-field">Đến <input type="date" name="created_to" value="<?= e($created_to) ?>"></label>
        <select name="per_page" title="Số dòng / trang">
            <?php foreach ($perPageOptions as $opt): ?>
                <option value="<?= (int)$opt ?>" <?= (int)$perPage === (int)$opt ? 'selected' : '' ?>><?= (int)$opt ?>/trang</option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        <a href="/orders" class="btn btn-ghost btn-sm">Xóa lọc</a>
    </form>

    <?php if (!empty($chips)): ?>
    <div class="filter-chips">
        <span class="muted">Đang lọc:</span>
        <?php foreach ($chips as $c): ?>
            <a class="chip" href="<?= e($c[1]) ?>"><?= e($c[0]) ?> <span class="chip-x">&times;</span></a>
        <?php endforeach; ?>
        <a class="chip chip-clear" href="/orders">Xóa tất cả</a>
    </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:200px;margin:0 auto 12px;display:block;">Không có phiếu nào khớp điều kiện.</div>
    <?php else: ?>
    <?php $bulkAllowed = can('soft_delete') || can('status_change'); ?>
    <form method="post" action="/orders/bulk" id="bulkForm" onsubmit="return eduConfirmBulk(this);">
        <?= csrf_field() ?>
        <?php if ($bulkAllowed): ?>
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><span id="bulkN">0</span> đã chọn</span>
            <?php if (can('status_change')): ?>
            <select name="bulk_status" id="bulkStatus">
                <option value="">-- Đổi trạng thái --</option>
                <?php foreach (config('order_statuses') as $st): ?>
                    <option value="<?= e($st) ?>"><?= e($st) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="bulk_action" value="status" class="btn btn-ghost btn-sm">Áp dụng status</button>
            <?php endif; ?>
            <?php if (can('soft_delete')): ?>
            <button type="submit" name="bulk_action" value="delete" class="btn btn-danger btn-sm">Xóa đã chọn</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <table>
        <thead>
            <tr>
                <?php if ($bulkAllowed): ?><th style="width:34px;"><input type="checkbox" id="checkAll" title="Chọn tất cả"></th><?php endif; ?>
                <th><a href="<?= e($sortLink('order_code')) ?>">Mã phiếu<?= $arrow('order_code') ?></a></th>
                <th>Học viên</th>
                <th><a href="<?= e($sortLink('course')) ?>">Khóa học<?= $arrow('course') ?></a></th>
                <th><a href="<?= e($sortLink('amount')) ?>">Học phí<?= $arrow('amount') ?></a></th>
                <th><a href="<?= e($sortLink('status')) ?>">Trạng thái<?= $arrow('status') ?></a></th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <?php if ($bulkAllowed): ?><td><input type="checkbox" class="rowCheck" name="id[]" value="<?= (int)$r['id'] ?>"></td><?php endif; ?>
                <td><strong><?= e($r['order_code']) ?></strong></td>
                <td><?= e($r['lead_name']) ?></td>
                <td><?= e($r['course']) ?></td>
                <td><?= $money($r['amount']) ?> đ</td>
                <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td class="actions">
                    <a href="/orders/invoice?id=<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm" target="_blank">Hóa đơn</a>
                    <a href="/orders/edit?id=<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm">Sửa</a>
                    <?php if (can('soft_delete')): ?>
                    <button type="button" class="btn btn-danger btn-sm js-row-del" data-id="<?= (int)$r['id'] ?>">Xóa</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </form>

    <?php if (can('soft_delete')): ?>
    <form method="post" action="/orders/delete" id="rowDelForm" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="rowDelId" value="">
    </form>
    <?php endif; ?>

    <div class="pagination">
        <?php if ($page > 1): ?><a href="<?= e($pageLink($page - 1)) ?>">&laquo; Trước</a><?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === $page): ?><span class="current"><?= $p ?></span>
            <?php else: ?><a href="<?= e($pageLink($p)) ?>"><?= $p ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="<?= e($pageLink($page + 1)) ?>">Sau &raquo;</a><?php endif; ?>
        <span class="muted">Trang <?= $page ?>/<?= $totalPages ?> &middot; <?= (int)$perPage ?>/trang</span>
    </div>
    <?php endif; ?>
</div>

<script>
(function () {
    var all = document.getElementById('checkAll');
    var checks = function () { return Array.prototype.slice.call(document.querySelectorAll('.rowCheck')); };
    var nEl = document.getElementById('bulkN');
    function refresh() {
        var sel = checks().filter(function (c) { return c.checked; }).length;
        if (nEl) nEl.textContent = sel;
    }
    if (all) all.addEventListener('change', function () {
        checks().forEach(function (c) { c.checked = all.checked; });
        refresh();
    });
    checks().forEach(function (c) { c.addEventListener('change', refresh); });
    refresh();

    document.querySelectorAll('.js-row-del').forEach(function (b) {
        b.addEventListener('click', function () {
            if (!confirm('Chuyển phiếu này vào thùng rác?')) return;
            document.getElementById('rowDelId').value = this.getAttribute('data-id');
            document.getElementById('rowDelForm').submit();
        });
    });
})();
function eduConfirmBulk(form) {
    var sel = form.querySelectorAll('.rowCheck:checked').length;
    if (sel === 0) { alert('Vui lòng chọn ít nhất một dòng.'); return false; }
    var act = document.activeElement ? document.activeElement.value : '';
    if (act === 'delete') return confirm('Xóa ' + sel + ' phiếu đã chọn vào thùng rác?');
    return true;
}
</script>
