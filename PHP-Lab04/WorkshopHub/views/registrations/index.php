<?php
/** Danh sach dang ky — WorkshopHub. Bien: $registrations */
?>
<div class="page-head">
    <div>
        <h1>Danh sach dang ky</h1>
        <p class="muted">Tong cong <strong><?= count($registrations) ?></strong> luot dang ky tham du workshop.</p>
    </div>
    <a href="/registrations/create" class="btn btn-primary">+ Dang ky moi</a>
</div>

<?php if (empty($registrations)): ?>
    <div class="empty-state">
        <img src="/assets/img/empty-registrations.png" alt="No registrations yet">
        <p>Chua co dang ky nao trong he thong.</p>
        <a href="/registrations/create" class="btn btn-primary">Dang ky ngay</a>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ho ten</th>
                    <th>Email</th>
                    <th>So dien thoai</th>
                    <th>Chu de</th>
                    <th>Ca</th>
                    <th>Trang thai</th>
                    <th>Thoi gian</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $r): ?>
                    <tr>
                        <td>#<?= h((string) ($r['id'] ?? '')) ?></td>
                        <td><?= h($r['full_name'] ?? '') ?></td>
                        <td><?= h($r['email'] ?? '') ?></td>
                        <td><?= h($r['phone'] ?? '') ?></td>
                        <td><?= h($r['topic'] ?? '') ?></td>
                        <td><?= h($r['session'] ?? '') ?></td>
                        <td>
                            <?php $st = $r['status'] ?? 'pending'; ?>
                            <span class="badge badge-<?= $st === 'confirmed' ? 'ok' : 'pending' ?>">
                                <?= h($st === 'confirmed' ? 'Da xac nhan' : 'Cho duyet') ?>
                            </span>
                        </td>
                        <td class="muted small"><?= h($r['created_at'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
