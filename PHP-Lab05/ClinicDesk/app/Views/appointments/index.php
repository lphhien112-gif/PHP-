<?php
use ClinicDesk\Core\View;
ob_start();

$sortLink = function (string $col) use ($q, $status, $sort, $direction) {
    $newDir = ($sort === $col && $direction === 'ASC') ? 'DESC' : 'ASC';
    return '/appointments?' . http_build_query([
        'q' => $q, 'status' => $status, 'sort' => $col, 'direction' => $newDir,
    ]);
};
$arrow = function (string $col) use ($sort, $direction) {
    if ($sort !== $col) return '';
    return $direction === 'ASC' ? ' ▲' : ' ▼';
};
$statusLabel = [
    'pending' => 'Cho xac nhan', 'confirmed' => 'Da xac nhan',
    'completed' => 'Hoan thanh', 'cancelled' => 'Da huy',
];
?>
<div class="page-header">
    <div>
        <h1 class="page-title">📅 Lich hen</h1>
        <p class="page-subtitle">Tong <?= (int)$pager->total ?> lich hen &bull; trang <?= (int)$pager->page ?>/<?= (int)$pager->totalPages ?></p>
    </div>
    <a href="/appointments/create" class="btn btn-primary">➕ Tao lich hen</a>
</div>

<?php if (!empty($flash_success)): ?>
    <div class="flash flash-success">✅ <?= View::e($flash_success) ?></div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
    <div class="flash flash-error">⚠️ <?= View::e($flash_error) ?></div>
<?php endif; ?>

<div class="toolbar">
    <form method="get" action="/appointments">
        <input type="text" name="q" value="<?= View::e($q) ?>" placeholder="Tim theo ma, ten, email...">
        <select name="status">
            <option value="">-- Tat ca trang thai --</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $statusLabel[$s] ?? $s ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="sort" value="<?= View::e($sort) ?>">
        <input type="hidden" name="direction" value="<?= View::e($direction) ?>">
        <button type="submit" class="btn btn-primary">🔍 Loc</button>
        <?php if ($q !== '' || $status !== ''): ?><a href="/appointments" class="btn btn-ghost">Xoa loc</a><?php endif; ?>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th><a class="<?= $sort==='appointment_code'?'active':'' ?>" href="<?= $sortLink('appointment_code') ?>">Ma<?= $arrow('appointment_code') ?></a></th>
                <th><a class="<?= $sort==='patient_name'?'active':'' ?>" href="<?= $sortLink('patient_name') ?>">Benh nhan<?= $arrow('patient_name') ?></a></th>
                <th>Chuyen khoa</th>
                <th><a class="<?= $sort==='appointment_date'?'active':'' ?>" href="<?= $sortLink('appointment_date') ?>">Ngay gio<?= $arrow('appointment_date') ?></a></th>
                <th><a class="<?= $sort==='status'?'active':'' ?>" href="<?= $sortLink('status') ?>">Trang thai<?= $arrow('status') ?></a></th>
                <th>Thao tac</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($appointments)): ?>
            <tr><td colspan="6" class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:160px;margin:0 auto 12px;display:block;">Khong co lich hen nao khop dieu kien.</td></tr>
        <?php else: ?>
            <?php foreach ($appointments as $a): ?>
            <tr>
                <td><strong><?= View::e($a['appointment_code']) ?></strong></td>
                <td>
                    <?= View::e($a['patient_name']) ?><br>
                    <span class="muted" style="font-size:.8rem"><?= View::e($a['patient_email']) ?></span>
                </td>
                <td><?= View::e($a['department']) ?></td>
                <td class="muted"><?= View::e($a['appointment_date']) ?></td>
                <td><span class="badge badge-<?= View::e($a['status']) ?>"><?= $statusLabel[$a['status']] ?? View::e($a['status']) ?></span></td>
                <td class="actions">
                    <a href="/appointments/edit?id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-ghost">✏️ Sua</a>
                    <form method="post" action="/appointments/delete" class="inline-form"
                          onsubmit="return confirm('Xoa lich hen <?= View::e($a['appointment_code']) ?>?');">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">🗑️ Xoa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$pageUrl = function (int $page) use ($q, $status, $sort, $direction) {
    return '/appointments?' . http_build_query([
        'q' => $q, 'status' => $status, 'sort' => $sort, 'direction' => $direction, 'page' => $page,
    ]);
};
?>
<div class="pagination">
    <?php if ($pager->hasPrev()): ?>
        <a href="<?= $pageUrl($pager->page - 1) ?>">&laquo; Truoc</a>
    <?php else: ?>
        <span class="disabled">&laquo; Truoc</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pager->totalPages; $i++): ?>
        <?php if ($i === $pager->page): ?>
            <span class="current"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= $pageUrl($i) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($pager->hasNext()): ?>
        <a href="<?= $pageUrl($pager->page + 1) ?>">Sau &raquo;</a>
    <?php else: ?>
        <span class="disabled">Sau &raquo;</span>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
