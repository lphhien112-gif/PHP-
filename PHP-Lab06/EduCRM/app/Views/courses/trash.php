<?php
/** EduCRM - Module C: thung rac khoa hoc (khoi phuc / xoa vinh vien). */
$money = fn($v) => number_format((float) $v, 0, ',', '.');
?>
<div class="page-head">
    <div><h1>Thùng rác Khóa học</h1><div class="sub"><?= count($rows) ?> khóa học đã xóa mềm</div></div>
    <a href="/courses" class="btn btn-ghost">&laquo; Về danh sách</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thùng rác trống.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Tên khóa học</th><th>Nhóm</th><th>Học phí</th><th>Đã xóa lúc</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['category']) ?></td>
                <td><?= $money($r['price']) ?> đ</td>
                <td><?= e($r['deleted_at']) ?></td>
                <td class="actions">
                    <form method="post" action="/courses/restore" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Khôi phục</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/courses/force-delete" onsubmit="return confirm('Xóa VĨNH VIỄN khóa học này? Không thể hoàn tác.');" style="margin:0;">
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
