<?php /** EduCRM - Form cong khai dang ky tu van (honeypot + validate). */ ?>
<div class="public-hero">
    <img src="/assets/img/hero-public-lead.png" alt="Public Lead Hero">
</div>
<div class="card">
    <h2 style="margin-top:0;">Dang ky nhan tu van</h2>
    <p class="muted" style="margin-top:-6px;">Dien thong tin, doi ngu tu van cua chung toi se lien he trong 24h.</p>

    <form method="post" action="/public-leads">
        <!-- Honeypot: field an. Nguoi that khong nhin thay; bot dien -> bi chan. -->
        <div style="position:absolute;left:-9999px;" aria-hidden="true">
            <label for="website">Website (de trong)</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Ho ten *</label>
                <input type="text" id="full_name" name="full_name"
                       class="<?= error_of('full_name') ? 'has-error' : '' ?>"
                       value="<?= e(old('full_name')) ?>">
                <?php if (error_of('full_name')): ?><div class="field-error"><?= e(error_of('full_name')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="text" id="email" name="email"
                       class="<?= error_of('email') ? 'has-error' : '' ?>"
                       value="<?= e(old('email')) ?>">
                <?php if (error_of('email')): ?><div class="field-error"><?= e(error_of('email')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">So dien thoai *</label>
                <input type="text" id="phone" name="phone"
                       class="<?= error_of('phone') ? 'has-error' : '' ?>"
                       value="<?= e(old('phone')) ?>">
                <?php if (error_of('phone')): ?><div class="field-error"><?= e(error_of('phone')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="course">Khoa hoc quan tam *</label>
                <select id="course" name="course" class="<?= error_of('course') ? 'has-error' : '' ?>">
                    <option value="">-- Chon khoa hoc --</option>
                    <?php foreach (course_names() as $c): ?>
                        <option value="<?= e($c) ?>" <?= old('course') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('course')): ?><div class="field-error"><?= e(error_of('course')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="note">Ghi chu them</label>
                <textarea id="note" name="note" rows="3"><?= e(old('note')) ?></textarea>
                <?php if (error_of('note')): ?><div class="field-error"><?= e(error_of('note')) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Gui dang ky</button>
        </div>
    </form>
</div>
