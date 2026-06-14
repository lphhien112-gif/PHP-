<?php /** EduCRM - F2: thung rac Lead (da xoa mem). */ ?>
<div class="page-head">
    <div><h1>Thung rac Lead</h1><div class="sub"><?= count($rows) ?> lead da xoa mem</div></div>
    <a href="/leads" class="btn btn-ghost">&laquo; Ve danh sach</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thung rac trong.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Ho ten</th><th>Email</th><th>Khoa hoc</th>
                <th>Trang thai</th><th>Da xoa luc</th><th>Hanh dong</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= e($r['full_name']) ?></td>
                <td><?= e($r['email']) ?></td>
                <td><?= e($r['course']) ?></td>
                <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td class="muted"><?= e($r['deleted_at']) ?></td>
                <td class="actions">
                    <form method="post" action="/leads/restore" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-sm">Khoi phuc</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/leads/force-delete" onsubmit="return confirm('Xoa VINH VIEN lead nay? Khong the hoan tac.');" style="margin:0;">
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
