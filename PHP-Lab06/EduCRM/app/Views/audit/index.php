<?php
/** EduCRM - F3: nhat ky hoat dong (admin-only). */
$pageLink = fn(int $p) => '/audit?' . http_build_query([
    'user_id' => $f_user, 'action' => $f_action, 'entity' => $f_entity, 'page' => $p,
]);
// Mau badge cho action.
$actionClass = function (string $a): string {
    return match ($a) {
        'create', 'restore' => 'converted',
        'update', 'convert' => 'qualified',
        'delete'            => 'refunded',
        'login'             => 'paid',
        'logout'            => 'lost',
        'login_failed'      => 'cancelled',
        default             => 'new',
    };
};
?>
<div class="page-head">
    <div><h1>Nhat ky hoat dong</h1><div class="sub">Tong <?= (int)$total ?> ban ghi - ai lam gi luc nao</div></div>
</div>

<div class="card">
    <form method="get" action="/audit" class="toolbar">
        <select name="user_id">
            <option value="">-- Tat ca nguoi dung --</option>
            <?php foreach ($userOptions as $u): ?>
                <option value="<?= (int)$u['id'] ?>" <?= (string)$f_user === (string)$u['id'] ? 'selected' : '' ?>><?= e($u['username']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="action">
            <option value="">-- Tat ca hanh dong --</option>
            <?php foreach ($actions as $a): ?>
                <option value="<?= e($a) ?>" <?= $f_action === $a ? 'selected' : '' ?>><?= e($a) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="entity">
            <option value="">-- Tat ca doi tuong --</option>
            <?php foreach ($entities as $en): ?>
                <option value="<?= e($en) ?>" <?= $f_entity === $en ? 'selected' : '' ?>><?= e($en) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Loc</button>
        <a href="/audit" class="btn btn-ghost btn-sm">Xoa loc</a>
    </form>

    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Chua co nhat ky nao khop dieu kien.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Thoi gian</th><th>Nguoi dung</th><th>Hanh dong</th>
                <th>Doi tuong</th><th>Mo ta</th><th>IP</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td class="muted"><?= e($r['created_at']) ?></td>
                <td><?= e($r['username'] ?? '-') ?></td>
                <td><span class="badge <?= $actionClass($r['action']) ?>"><?= e($r['action']) ?></span></td>
                <td><?= e($r['entity'] ?? '-') ?><?= $r['entity_id'] ? ' #' . (int)$r['entity_id'] : '' ?></td>
                <td><?= e($r['summary'] ?? '') ?></td>
                <td class="muted"><?= e($r['ip'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?><a href="<?= e($pageLink($page - 1)) ?>">&laquo; Truoc</a><?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === $page): ?><span class="current"><?= $p ?></span>
            <?php else: ?><a href="<?= e($pageLink($p)) ?>"><?= $p ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="<?= e($pageLink($page + 1)) ?>">Sau &raquo;</a><?php endif; ?>
        <span class="muted">Trang <?= $page ?>/<?= $totalPages ?></span>
    </div>
    <?php endif; ?>
</div>
