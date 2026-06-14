<?php /** EduCRM - Module B: form tao phieu hoc phi. */ ?>
<div class="page-head"><h1>Tao phieu hoc phi</h1><a href="/orders" class="btn btn-ghost">&laquo; Quay lai</a></div>

<div class="card">
    <form method="post" action="/orders/store">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group">
                <label for="order_code">Ma phieu *</label>
                <input type="text" id="order_code" name="order_code"
                       value="<?= e(old('order_code', $suggestCode)) ?>"
                       class="<?= error_of('order_code') ? 'has-error' : '' ?>">
                <?php if (error_of('order_code')): ?><div class="field-error"><?= e(error_of('order_code')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="lead_id">Hoc vien *</label>
                <select id="lead_id" name="lead_id" class="<?= error_of('lead_id') ? 'has-error' : '' ?>">
                    <option value="">-- Chon hoc vien --</option>
                    <?php foreach ($leadOptions as $o): ?>
                        <option value="<?= (int)$o['id'] ?>" <?= (string)old('lead_id') === (string)$o['id'] ? 'selected' : '' ?>>
                            <?= e($o['full_name']) ?> (<?= e($o['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('lead_id')): ?><div class="field-error"><?= e(error_of('lead_id')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khoa hoc *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chon khoa hoc --</option>
                    <?php foreach (config('courses') as $c): ?>
                        <option value="<?= e($c) ?>" <?= old('course') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
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
                <label for="status">Trang thai</label>
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
            <button type="submit" class="btn btn-primary">Tao phieu</button>
            <a href="/orders" class="btn btn-ghost">Huy</a>
        </div>
    </form>
</div>
