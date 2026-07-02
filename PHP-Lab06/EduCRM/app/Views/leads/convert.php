<?php
/** EduCRM - F1: form tao phieu thu tu lead (prefilled). */
$leadCourse = $lead['course'] ?? '';
?>
<div class="page-head">
    <div>
        <h1>Tao phieu thu tu Lead</h1>
        <div class="sub">Chuyen <strong><?= e($lead['full_name']) ?></strong> thanh phieu hoc phi (lead se chuyen sang <span class="badge converted">converted</span>)</div>
    </div>
    <a href="/leads" class="btn btn-ghost">&laquo; Quay lai</a>
</div>

<div class="card lead-snapshot">
    <img src="/assets/img/stat-leads.png" alt="" class="snap-art" loading="lazy">
    <div>
        <div class="snap-row"><span>Hoc vien</span><strong><?= e($lead['full_name']) ?></strong></div>
        <div class="snap-row"><span>Email</span><strong><?= e($lead['email']) ?></strong></div>
        <div class="snap-row"><span>SDT</span><strong><?= e($lead['phone']) ?></strong></div>
        <div class="snap-row"><span>Trang thai</span><span class="badge <?= e($lead['status']) ?>"><?= e($lead['status']) ?></span></div>
    </div>
</div>

<div class="card">
    <form method="post" action="/orders/store">
        <?= csrf_field() ?>
        <input type="hidden" name="from_lead_id" value="<?= (int)$lead['id'] ?>">
        <input type="hidden" name="lead_id" value="<?= (int)$lead['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="order_code">Ma phieu *</label>
                <input type="text" id="order_code" name="order_code"
                       value="<?= e(old('order_code', $suggestCode)) ?>"
                       class="<?= error_of('order_code') ? 'has-error' : '' ?>">
                <?php if (error_of('order_code')): ?><div class="field-error"><?= e(error_of('order_code')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khoa hoc *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chon khoa hoc --</option>
                    <?php foreach (course_names() as $c): ?>
                        <option value="<?= e($c) ?>" <?= old('course', $leadCourse) === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="amount">Hoc phi (VND) *</label>
                <input type="number" id="amount" name="amount" min="0" step="1000" value="<?= e(old('amount')) ?>"
                       class="<?= error_of('amount') ? 'has-error' : '' ?>">
                <?php if (error_of('amount')): ?><div class="field-error"><?= e(error_of('amount')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Trang thai phieu</label>
                <select id="status" name="status">
                    <?php foreach (config('order_statuses') as $s): ?>
                        <option value="<?= e($s) ?>" <?= old('status', 'pending') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="paid_at">Ngay thanh toan</label>
                <input type="date" id="paid_at" name="paid_at" value="<?= e(old('paid_at')) ?>"
                       class="<?= error_of('paid_at') ? 'has-error' : '' ?>">
                <?php if (error_of('paid_at')): ?><div class="field-error"><?= e(error_of('paid_at')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chu</label>
                <textarea id="note" name="note" rows="3"><?= e(old('note')) ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Tao phieu &amp; chuyen doi</button>
            <a href="/leads" class="btn btn-ghost">Huy</a>
        </div>
    </form>
</div>
