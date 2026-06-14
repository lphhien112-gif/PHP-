<?php
/** EduCRM - Module B: form sua phieu hoc phi. */
$val = fn(string $k) => e(old($k, $order[$k] ?? ''));
$sel = fn(string $k, string $v) => (string)old($k, $order[$k] ?? '') === $v ? 'selected' : '';
?>
<div class="page-head"><h1>Sua phieu <?= e($order['order_code']) ?></h1><a href="/orders" class="btn btn-ghost">&laquo; Quay lai</a></div>

<div class="card">
    <form method="post" action="/orders/update">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="order_code">Ma phieu *</label>
                <input type="text" id="order_code" name="order_code" value="<?= $val('order_code') ?>"
                       class="<?= error_of('order_code') ? 'has-error' : '' ?>">
                <?php if (error_of('order_code')): ?><div class="field-error"><?= e(error_of('order_code')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="lead_id">Hoc vien *</label>
                <select id="lead_id" name="lead_id" class="<?= error_of('lead_id') ? 'has-error' : '' ?>">
                    <?php foreach ($leadOptions as $o): ?>
                        <option value="<?= (int)$o['id'] ?>" <?= $sel('lead_id', (string)$o['id']) ?>>
                            <?= e($o['full_name']) ?> (<?= e($o['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('lead_id')): ?><div class="field-error"><?= e(error_of('lead_id')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khoa hoc *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <?php foreach (config('courses') as $c): ?>
                        <option value="<?= e($c) ?>" <?= $sel('course', $c) ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="amount">Hoc phi (VND) *</label>
                <input type="number" id="amount" name="amount" min="0" step="1000" value="<?= $val('amount') ?>"
                       class="<?= error_of('amount') ? 'has-error' : '' ?>">
                <?php if (error_of('amount')): ?><div class="field-error"><?= e(error_of('amount')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Trang thai</label>
                <select id="status" name="status">
                    <?php foreach (config('order_statuses') as $s): ?>
                        <option value="<?= e($s) ?>" <?= $sel('status', $s) ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="paid_at">Ngay thanh toan</label>
                <input type="date" id="paid_at" name="paid_at"
                       value="<?= e(old('paid_at', $order['paid_at'] ?? '')) ?>"
                       class="<?= error_of('paid_at') ? 'has-error' : '' ?>">
                <?php if (error_of('paid_at')): ?><div class="field-error"><?= e(error_of('paid_at')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chu</label>
                <textarea id="note" name="note" rows="3"><?= $val('note') ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cap nhat</button>
            <a href="/orders" class="btn btn-ghost">Huy</a>
        </div>
    </form>
</div>
