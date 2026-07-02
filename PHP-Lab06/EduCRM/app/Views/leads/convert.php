<?php
/** EduCRM - F1: form tao phieu thu tu lead (prefilled). */
$leadCourse = $lead['course'] ?? '';
?>
<div class="page-head">
    <div>
        <h1>Tạo phiếu thu từ Lead</h1>
        <div class="sub">Chuyển <strong><?= e($lead['full_name']) ?></strong> thành phiếu học phí (lead sẽ chuyển sang <span class="badge converted">converted</span>)</div>
    </div>
    <a href="/leads" class="btn btn-ghost">&laquo; Quay lại</a>
</div>

<div class="card lead-snapshot">
    <img src="/assets/img/stat-leads.png" alt="" class="snap-art" loading="lazy">
    <div>
        <div class="snap-row"><span>Học viên</span><strong><?= e($lead['full_name']) ?></strong></div>
        <div class="snap-row"><span>Email</span><strong><?= e($lead['email']) ?></strong></div>
        <div class="snap-row"><span>SĐT</span><strong><?= e($lead['phone']) ?></strong></div>
        <div class="snap-row"><span>Trạng thái</span><span class="badge <?= e($lead['status']) ?>"><?= e($lead['status']) ?></span></div>
    </div>
</div>

<div class="card">
    <form method="post" action="/orders/store">
        <?= csrf_field() ?>
        <input type="hidden" name="from_lead_id" value="<?= (int)$lead['id'] ?>">
        <input type="hidden" name="lead_id" value="<?= (int)$lead['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="order_code">Mã phiếu *</label>
                <input type="text" id="order_code" name="order_code"
                       value="<?= e(old('order_code', $suggestCode)) ?>"
                       class="<?= error_of('order_code') ? 'has-error' : '' ?>">
                <?php if (error_of('order_code')): ?><div class="field-error"><?= e(error_of('order_code')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khóa học *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chọn khóa học --</option>
                    <?php foreach (course_names() as $c): ?>
                        <option value="<?= e($c) ?>" <?= old('course', $leadCourse) === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="amount">Học phí (VND) *</label>
                <input type="number" id="amount" name="amount" min="0" step="1000" value="<?= e(old('amount')) ?>"
                       class="<?= error_of('amount') ? 'has-error' : '' ?>">
                <?php if (error_of('amount')): ?><div class="field-error"><?= e(error_of('amount')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Trạng thái phiếu</label>
                <select id="status" name="status">
                    <?php foreach (config('order_statuses') as $s): ?>
                        <option value="<?= e($s) ?>" <?= old('status', 'pending') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="paid_at">Ngày thanh toán</label>
                <input type="date" id="paid_at" name="paid_at" value="<?= e(old('paid_at')) ?>"
                       class="<?= error_of('paid_at') ? 'has-error' : '' ?>">
                <?php if (error_of('paid_at')): ?><div class="field-error"><?= e(error_of('paid_at')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note" rows="3"><?= e(old('note')) ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Tạo phiếu &amp; chuyển đổi</button>
            <a href="/leads" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
