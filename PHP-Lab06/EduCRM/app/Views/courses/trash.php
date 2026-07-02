<?php
/** EduCRM - Module C: thung rac khoa hoc (khoi phuc / xoa vinh vien). */
$money = fn($v) => number_format((float) $v, 0, ',', '.');
?>
<div class="page-head">
    <div><h1>Thung rac Khoa hoc</h1><div class="sub"><?= count($rows) ?> khoa hoc da xoa mem</div></div>
    <a href="/courses" class="btn btn-ghost">&laquo; Ve danh sach</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thung rac trong.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Ten khoa hoc</th><th>Nhom</th><th>Hoc phi</th><th>Da xoa luc</th><th>Hanh dong</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['category']) ?></td>
                <td><?= $money($r['price']) ?> d</td>
                <td><?= e($r['deleted_at']) ?></td>
                <td class="actions">
                    <form method="post" action="/courses/restore" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Khoi phuc</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/courses/force-delete" onsubmit="return confirm('Xoa VINH VIEN khoa hoc nay? Khong the hoan tac.');" style="margin:0;">
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
