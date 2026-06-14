<?php
use ClinicDesk\Core\View;
/**
 * Partial form lich hen, dung chung cho create va edit.
 * Bien: $old, $errors, $action, $mode, $statuses, $departments.
 */
$err = fn($f) => $errors[$f] ?? null;
$statusLabel = [
    'pending' => 'Cho xac nhan', 'confirmed' => 'Da xac nhan',
    'completed' => 'Hoan thanh', 'cancelled' => 'Da huy',
];
$dateValue = $old['appointment_date_input'] ?? '';
?>
<form method="post" action="<?= View::e($action) ?>" class="card form-card" novalidate>
    <?php if (($mode ?? '') === 'edit'): ?>
        <input type="hidden" name="id" value="<?= (int)($old['id'] ?? 0) ?>">
    <?php endif; ?>

    <div class="form-group <?= $err('appointment_code') ? 'has-error' : '' ?>">
        <label>Ma lich hen <span style="color:var(--red)">*</span> <span class="muted">(unique)</span></label>
        <input type="text" name="appointment_code" value="<?= View::e($old['appointment_code'] ?? '') ?>" placeholder="APT-2026-0099">
        <?php if ($err('appointment_code')): ?><div class="field-error"><?= View::e($err('appointment_code')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('patient_name') ? 'has-error' : '' ?>">
        <label>Ten benh nhan <span style="color:var(--red)">*</span></label>
        <input type="text" name="patient_name" value="<?= View::e($old['patient_name'] ?? '') ?>" placeholder="Nguyen Van A">
        <?php if ($err('patient_name')): ?><div class="field-error"><?= View::e($err('patient_name')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('patient_email') ? 'has-error' : '' ?>">
        <label>Email benh nhan <span style="color:var(--red)">*</span></label>
        <input type="text" name="patient_email" value="<?= View::e($old['patient_email'] ?? '') ?>" placeholder="email@example.com">
        <?php if ($err('patient_email')): ?><div class="field-error"><?= View::e($err('patient_email')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('department') ? 'has-error' : '' ?>">
        <label>Chuyen khoa <span style="color:var(--red)">*</span></label>
        <select name="department">
            <?php foreach ($departments as $d): ?>
                <option value="<?= View::e($d) ?>" <?= ($old['department'] ?? '') === $d ? 'selected' : '' ?>><?= View::e($d) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($err('department')): ?><div class="field-error"><?= View::e($err('department')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('appointment_date') ? 'has-error' : '' ?>">
        <label>Ngay gio kham <span style="color:var(--red)">*</span></label>
        <input type="datetime-local" name="appointment_date" value="<?= View::e($dateValue) ?>">
        <?php if ($err('appointment_date')): ?><div class="field-error"><?= View::e($err('appointment_date')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('status') ? 'has-error' : '' ?>">
        <label>Trang thai</label>
        <select name="status">
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= ($old['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= $statusLabel[$s] ?? $s ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($err('status')): ?><div class="field-error"><?= View::e($err('status')) ?></div><?php endif; ?>
    </div>

    <div class="form-group">
        <label>Ghi chu</label>
        <textarea name="note" rows="2" placeholder="Trieu chung, ghi chu them..."><?= View::e($old['note'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= ($mode ?? '') === 'edit' ? '💾 Cap nhat' : '➕ Tao moi' ?></button>
        <a href="/appointments" class="btn btn-ghost">Huy</a>
    </div>
</form>
