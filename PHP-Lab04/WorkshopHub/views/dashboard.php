<?php
/** Dashboard nhan vien — WorkshopHub. Bien: $user, $stats, $registrations, $idleTimeout */
?>
<div class="page-head">
    <div>
        <h1>Bang dieu khien</h1>
        <p class="muted">Xin chao <strong><?= h($user['name'] ?? '') ?></strong> (vai tro: <?= h($user['role'] ?? '') ?>)</p>
    </div>
    <a href="/session-demo" class="btn btn-ghost">Xem session</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-num"><?= (int) $stats['total'] ?></div>
        <div class="stat-label">Tong dang ky</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= (int) $stats['pending'] ?></div>
        <div class="stat-label">Cho duyet</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= (int) $stats['confirmed'] ?></div>
        <div class="stat-label">Da xac nhan</div>
    </div>
</div>

<div class="dash-info">
    <p>⏱️ Idle timeout hien tai: <strong><?= (int) $idleTimeout ?> giay</strong>.
       Sau khoang nay neu khong hoat dong, phien se het han va ban bi dang xuat.</p>
    <p>🔎 Xem chi tiet phien tai <a href="/session-demo">/session-demo</a>.</p>
</div>

<h2>Dang ky gan day</h2>
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>#</th><th>Ho ten</th><th>Email</th><th>Chu de</th><th>Ca</th><th>Trang thai</th></tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $r): ?>
                <tr>
                    <td>#<?= h((string) ($r['id'] ?? '')) ?></td>
                    <td><?= h($r['full_name'] ?? '') ?></td>
                    <td><?= h($r['email'] ?? '') ?></td>
                    <td><?= h($r['topic'] ?? '') ?></td>
                    <td><?= h($r['session'] ?? '') ?></td>
                    <td>
                        <?php $st = $r['status'] ?? 'pending'; ?>
                        <span class="badge badge-<?= $st === 'confirmed' ? 'ok' : 'pending' ?>">
                            <?= h($st === 'confirmed' ? 'Da xac nhan' : 'Cho duyet') ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
