<?php
/** EduCRM - F2: thung rac Phieu hoc phi (da xoa mem). */
$money = fn($n) => number_format((float)$n, 0, ',', '.');
?>
<div class="page-head">
    <div><h1>Thùng rác Phiếu học phí</h1><div class="sub"><?= count($rows) ?> phiếu đã xóa mềm</div></div>
    <a href="/orders" class="btn btn-ghost">&laquo; Về danh sách</a>
</div>

<div class="card">
    <?php if (empty($rows)): ?>
        <div class="empty"><img src="/assets/img/empty-data.png" alt="Empty" loading="lazy" style="width:180px;margin:0 auto 12px;display:block;">Thùng rác trống.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Mã phiếu</th><th>Học viên</th><th>Khóa học</th><th>Học phí</th>
                <th>Trạng thái</th><th>Đã xóa lúc</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['order_code']) ?></strong></td>
                <td><?= e($r['lead_name']) ?></td>
                <td><?= e($r['course']) ?></td>
                <td><?= $money($r['amount']) ?> đ</td>
                <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td class="muted"><?= e($r['deleted_at']) ?></td>
                <td class="actions">
                    <form method="post" action="/orders/restore" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-sm">Khôi phục</button>
                    </form>
                    <?php if (can('force_delete')): ?>
                    <form method="post" action="/orders/force-delete" onsubmit="return confirm('Xóa VĨNH VIỄN phiếu này? Không thể hoàn tác.');" style="margin:0;">
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
