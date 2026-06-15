<?php
/** Form dang ky — WorkshopHub. Bien: $topics, $sessions. old()/field_error() tu helpers. */
?>
<div class="form-card">
    <h1>Dang ky tham du Workshop</h1>
    <p class="muted">Dien thong tin ben duoi. Cac truong co dau <span class="req">*</span> la bat buoc.</p>

    <form action="/registrations" method="post" novalidate class="form">
        <!-- Honeypot: an khoi nguoi dung; bot dien vao se bi chan -->
        <div class="hp-field" aria-hidden="true">
            <label for="website">Website (de trong)</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="full_name">Ho ten <span class="req">*</span></label>
            <input type="text" id="full_name" name="full_name" value="<?= h(old('full_name')) ?>"
                   class="<?= field_error('full_name') ? 'is-invalid' : '' ?>" maxlength="80">
            <?php if ($e = field_error('full_name')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email <span class="req">*</span></label>
                <input type="text" id="email" name="email" value="<?= h(old('email')) ?>"
                       class="<?= field_error('email') ? 'is-invalid' : '' ?>">
                <?php if ($e = field_error('email')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">So dien thoai <span class="req">*</span></label>
                <input type="text" id="phone" name="phone" value="<?= h(old('phone')) ?>"
                       placeholder="0xxxxxxxxx" class="<?= field_error('phone') ? 'is-invalid' : '' ?>">
                <?php if ($e = field_error('phone')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="topic">Chu de workshop <span class="req">*</span></label>
                <?php $selectedTopic = old('topic') ?: ($defaultTopic ?? ''); ?>
                <select id="topic" name="topic" class="<?= field_error('topic') ? 'is-invalid' : '' ?>">
                    <option value="">-- Chon chu de --</option>
                    <?php foreach ($topics as $t): ?>
                        <option value="<?= h($t) ?>" <?= $selectedTopic === $t ? 'selected' : '' ?>><?= h($t) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($e = field_error('topic')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="session">Ca tham du <span class="req">*</span></label>
                <select id="session" name="session" class="<?= field_error('session') ? 'is-invalid' : '' ?>">
                    <option value="">-- Chon ca --</option>
                    <?php foreach ($sessions as $s): ?>
                        <option value="<?= h($s) ?>" <?= old('session') === $s ? 'selected' : '' ?>><?= h($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($e = field_error('session')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Ghi chu (tuy chon)</label>
            <textarea id="notes" name="notes" rows="4" maxlength="500"
                      class="<?= field_error('notes') ? 'is-invalid' : '' ?>"><?= h(old('notes')) ?></textarea>
            <?php if ($e = field_error('notes')): ?><small class="err"><?= h($e) ?></small><?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Gui dang ky</button>
            <a href="/registrations" class="btn btn-ghost">Huy</a>
        </div>
    </form>
</div>
