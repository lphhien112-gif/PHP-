<?php /** EduCRM - Module A: form them lead. */ ?>
<div class="page-head"><h1>Thêm Lead mới</h1><a href="/leads" class="btn btn-ghost">&laquo; Quay lại</a></div>

<div class="card">
    <form method="post" action="/leads/store">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Họ tên *</label>
                <input type="text" id="full_name" name="full_name" value="<?= e(old('full_name')) ?>"
                       class="<?= error_of('full_name') ? 'has-error' : '' ?>">
                <?php if (error_of('full_name')): ?><div class="field-error"><?= e(error_of('full_name')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="text" id="email" name="email" value="<?= e(old('email')) ?>"
                       class="<?= error_of('email') ? 'has-error' : '' ?>">
                <?php if (error_of('email')): ?><div class="field-error"><?= e(error_of('email')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại *</label>
                <input type="text" id="phone" name="phone" value="<?= e(old('phone')) ?>"
                       class="<?= error_of('phone') ? 'has-error' : '' ?>">
                <?php if (error_of('phone')): ?><div class="field-error"><?= e(error_of('phone')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khóa học *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chọn khóa học --</option>
                    <?php foreach (course_names() as $c): ?>
                        <option value="<?= e($c) ?>" <?= old('course') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="source">Nguồn</label>
                <select id="source" name="source">
                    <?php foreach (config('lead_sources') as $s): ?>
                        <option value="<?= e($s) ?>" <?= old('source', 'website') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <?php foreach (config('lead_statuses') as $s): ?>
                        <option value="<?= e($s) ?>" <?= old('status', 'new') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note" rows="3"><?= e(old('note')) ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu lead</button>
            <a href="/leads" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
