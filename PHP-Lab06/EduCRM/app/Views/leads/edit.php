<?php
/** EduCRM - Module A: form sua lead. old() uu tien, fallback ve du lieu cu $lead. */
$val = fn(string $k) => e(old($k, $lead[$k] ?? ''));
$sel = fn(string $k, string $v) => old($k, $lead[$k] ?? '') === $v ? 'selected' : '';
?>
<div class="page-head">
    <h1>Sửa Lead #<?= (int)$lead['id'] ?></h1>
    <div class="head-actions">
        <?php if (in_array($lead['status'], ['qualified', 'contacted'], true)): ?>
        <a href="/leads/convert?id=<?= (int)$lead['id'] ?>" class="btn btn-primary">Tạo phiếu thu</a>
        <?php endif; ?>
        <a href="/leads" class="btn btn-ghost">&laquo; Quay lại</a>
    </div>
</div>

<div class="card">
    <form method="post" action="/leads/update">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$lead['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Họ tên *</label>
                <input type="text" id="full_name" name="full_name" value="<?= $val('full_name') ?>"
                       class="<?= error_of('full_name') ? 'has-error' : '' ?>">
                <?php if (error_of('full_name')): ?><div class="field-error"><?= e(error_of('full_name')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="text" id="email" name="email" value="<?= $val('email') ?>"
                       class="<?= error_of('email') ? 'has-error' : '' ?>">
                <?php if (error_of('email')): ?><div class="field-error"><?= e(error_of('email')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại *</label>
                <input type="text" id="phone" name="phone" value="<?= $val('phone') ?>"
                       class="<?= error_of('phone') ? 'has-error' : '' ?>">
                <?php if (error_of('phone')): ?><div class="field-error"><?= e(error_of('phone')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khóa học *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chọn khóa học --</option>
                    <?php foreach (course_names() as $c): ?>
                        <option value="<?= e($c) ?>" <?= $sel('course', $c) ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="source">Nguồn</label>
                <select id="source" name="source">
                    <?php foreach (config('lead_sources') as $s): ?>
                        <option value="<?= e($s) ?>" <?= $sel('source', $s) ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <?php foreach (config('lead_statuses') as $s): ?>
                        <option value="<?= e($s) ?>" <?= $sel('status', $s) ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note" rows="3"><?= $val('note') ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="/leads" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
