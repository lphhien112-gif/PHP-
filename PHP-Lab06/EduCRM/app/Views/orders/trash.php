<?php
/** EduCRM - F2: thung rac Phieu hoc phi (da xoa mem). */
$money = fn($n) => number_format((float)$n, 0, ',', '.');
?>
<div class="page-head">
    <div><h1>Thung rac Phieu hoc phi</h1><div class="sub"><?= count($rows) ?> phieu da xoa mem</div></div>
    <a href="/orders" class="btn btn-ghost">&laquo; Ve danh sach</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thung rac trong.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Ma phieu</th><th>Hoc vien</th><th>Khoa hoc</th><th>Hoc phi</th>
                <th>Trang thai</th><th>Da xoa luc</th><th>Hanh dong</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['order_code']) ?></strong></td>
                <td><?= e($r['lead_name']) ?></td>
                <td><?= e($r['course']) ?></td>
                <td><?= $money($r['amount']) ?> d</td>
                <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td class="muted"><?= e($r['deleted_at']) ?></td>
                <td class="actions">
                    <form method="post" action="/orders/restore" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-sm">Khoi phuc</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/orders/force-delete" onsubmit="return confirm('Xoa VINH VIEN phieu nay? Khong the hoan tac.');" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xoa vinh vien</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
