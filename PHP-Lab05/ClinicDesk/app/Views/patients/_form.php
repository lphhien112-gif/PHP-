<?php
use ClinicDesk\Core\View;
/**
 * Partial form benh nhan, dung chung cho create va edit.
 * Bien: $old (gia tri cu), $errors (loi theo field), $action, $mode.
 */
$err = fn($f) => $errors[$f] ?? null;
?>
<form method="post" action="<?= View::e($action) ?>" class="card form-card" novalidate>
    <?php if (($mode ?? '') === 'edit'): ?>
        <input type="hidden" name="id" value="<?= (int)($old['id'] ?? 0) ?>">
    <?php endif; ?>

    <div class="form-group <?= $err('name') ? 'has-error' : '' ?>">
        <label>Ho ten <span style="color:var(--red)">*</span></label>
        <input type="text" name="name" value="<?= View::e($old['name'] ?? '') ?>" placeholder="Nguyen Van A">
        <?php if ($err('name')): ?><div class="field-error"><?= View::e($err('name')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('email') ? 'has-error' : '' ?>">
        <label>Email <span style="color:var(--red)">*</span> <span class="muted">(unique)</span></label>
        <input type="text" name="email" value="<?= View::e($old['email'] ?? '') ?>" placeholder="email@example.com">
        <?php if ($err('email')): ?><div class="field-error"><?= View::e($err('email')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('phone') ? 'has-error' : '' ?>">
        <label>So dien thoai <span style="color:var(--red)">*</span></label>
        <input type="text" name="phone" value="<?= View::e($old['phone'] ?? '') ?>" placeholder="0901234567">
        <?php if ($err('phone')): ?><div class="field-error"><?= View::e($err('phone')) ?></div><?php endif; ?>
    </div>

    <div class="form-group <?= $err('gender') ? 'has-error' : '' ?>">
        <label>Gioi tinh</label>
        <select name="gender">
            <?php foreach (['male' => 'Nam', 'female' => 'Nu', 'other' => 'Khac'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($old['gender'] ?? 'other') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($err('gender')): ?><div class="field-error"><?= View::e($err('gender')) ?></div><?php endif; ?>
    </div>

    <div class="form-group">
        <label>Dia chi</label>
        <textarea name="address" rows="2" placeholder="So nha, duong, quan/huyen..."><?= View::e($old['address'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= ($mode ?? '') === 'edit' ? '💾 Cap nhat' : '➕ Them moi' ?></button>
        <a href="/patients" class="btn btn-ghost">Huy</a>
    </div>
</form>
