<?php /** EduCRM - F2: thung rac Lead (da xoa mem). */ ?>
<div class="page-head">
    <div><h1>Thùng rác Lead</h1><div class="sub"><?= count($rows) ?> lead đã xóa mềm</div></div>
    <a href="/leads" class="btn btn-ghost">&laquo; Về danh sách</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thùng rác trống.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Họ tên</th><th>Email</th><th>Khóa học</th>
                <th>Trạng thái</th><th>Đã xóa lúc</th><th>Hành động</th>
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
                        <button type="submit" class="btn btn-ghost btn-sm">Khôi phục</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/leads/force-delete" onsubmit="return confirm('Xóa VĨNH VIỄN lead này? Không thể hoàn tác.');" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
