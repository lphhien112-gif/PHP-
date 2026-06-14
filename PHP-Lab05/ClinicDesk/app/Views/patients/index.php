<?php
use ClinicDesk\Core\View;
ob_start();

/**
 * Helper tao link sort: doi huong neu dang sort cot do, mac dinh ASC.
 */
$sortLink = function (string $col) use ($q, $sort, $direction) {
    $newDir = ($sort === $col && $direction === 'ASC') ? 'DESC' : 'ASC';
    $params = http_build_query(['q' => $q, 'sort' => $col, 'direction' => $newDir]);
    return '/patients?' . $params;
};
$arrow = function (string $col) use ($sort, $direction) {
    if ($sort !== $col) return '';
    return $direction === 'ASC' ? ' ▲' : ' ▼';
};
?>
<div class="page-header">
    <div>
        <h1 class="page-title">👤 Benh nhan</h1>
        <p class="page-subtitle">Tong <?= (int)$pager->total ?> benh nhan &bull; trang <?= (int)$pager->page ?>/<?= (int)$pager->totalPages ?></p>
    </div>
    <a href="/patients/create" class="btn btn-primary">➕ Them benh nhan</a>
</div>

<?php if (!empty($flash_success)): ?>
    <div class="flash flash-success">✅ <?= View::e($flash_success) ?></div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
    <div class="flash flash-error">⚠️ <?= View::e($flash_error) ?></div>
<?php endif; ?>

<div class="toolbar">
    <form method="get" action="/patients">
        <input type="text" name="q" value="<?= View::e($q) ?>" placeholder="Tim theo ten, email, SDT...">
        <input type="hidden" name="sort" value="<?= View::e($sort) ?>">
        <input type="hidden" name="direction" value="<?= View::e($direction) ?>">
        <button type="submit" class="btn btn-primary">🔍 Tim</button>
        <?php if ($q !== ''): ?><a href="/patients" class="btn btn-ghost">Xoa loc</a><?php endif; ?>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th><a class="<?= $sort==='id'?'active':'' ?>" href="<?= $sortLink('id') ?>">ID<?= $arrow('id') ?></a></th>
                <th><a class="<?= $sort==='name'?'active':'' ?>" href="<?= $sortLink('name') ?>">Ho ten<?= $arrow('name') ?></a></th>
                <th><a class="<?= $sort==='email'?'active':'' ?>" href="<?= $sortLink('email') ?>">Email<?= $arrow('email') ?></a></th>
                <th>SDT</th>
                <th>Gioi tinh</th>
                <th><a class="<?= $sort==='created_at'?'active':'' ?>" href="<?= $sortLink('created_at') ?>">Ngay tao<?= $arrow('created_at') ?></a></th>
                <th>Thao tac</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($patients)): ?>
            <tr><td colspan="7" class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:160px;margin:0 auto 12px;display:block;">Khong co benh nhan nao khop dieu kien.</td></tr>
        <?php else: ?>
            <?php foreach ($patients as $p): ?>
            <tr>
                <td>#<?= (int)$p['id'] ?></td>
                <td><strong><?= View::e($p['name']) ?></strong></td>
                <td><?= View::e($p['email']) ?></td>
                <td><?= View::e($p['phone']) ?></td>
                <td><span class="badge badge-<?= View::e($p['gender']) ?>"><?= View::e($p['gender']) ?></span></td>
                <td class="muted"><?= View::e($p['created_at']) ?></td>
                <td class="actions">
                    <a href="/patients/edit?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-ghost">✏️ Sua</a>
                    <form method="post" action="/patients/delete" class="inline-form"
                          onsubmit="return confirm('Xoa benh nhan #<?= (int)$p['id'] ?> (<?= View::e($p['name']) ?>)?');">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
// Pagination giu nguyen q/sort/direction.
$pageUrl = function (int $page) use ($q, $sort, $direction) {
    return '/patients?' . http_build_query([
        'q' => $q, 'sort' => $sort, 'direction' => $direction, 'page' => $page,
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
