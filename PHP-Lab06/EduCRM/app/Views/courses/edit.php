<?php
/** EduCRM - Module C: form sua khoa hoc (prefill tu $course, old() ghi de khi loi). */
$images = course_images();
$levels = config('course_levels', []);
$levelLabels = ['beginner' => 'Co ban', 'intermediate' => 'Trung cap', 'advanced' => 'Nang cao'];

$curName  = old('name', $course['name']);
$curCat   = old('category', $course['category']);
$curLevel = old('level', $course['level']);
$curPrice = old('price', (string) (int) $course['price']);
$curWeeks = old('duration_weeks', (string) (int) $course['duration_weeks']);
$curImage = old('image', (string) $course['image']);
$curDesc  = old('description', (string) $course['description']);
$curOutcomes = old('outcomes', (string) ($course['outcomes'] ?? ''));
$curActive = old('is_active', (string) (int) $course['is_active']);
?>
<div class="page-head"><h1>Sua khoa hoc</h1><a href="/courses" class="btn btn-ghost">&laquo; Quay lai</a></div>

<div class="card">
    <form method="post" action="/courses/update">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
        <div class="form-grid">
            <div class="form-group full">
                <label for="name">Ten khoa hoc *</label>
                <input type="text" id="name" name="name" value="<?= e($curName) ?>"
                       class="<?= error_of('name') ? 'has-error' : '' ?>">
                <?php if (error_of('name')): ?><div class="field-error"><?= e(error_of('name')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="category">Nhom *</label>
                <select id="category" name="category" class="<?= error_of('category') ? 'has-error' : '' ?>">
                    <option value="">-- Chon nhom --</option>
                    <?php foreach (config('course_categories') as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= $curCat === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('category')): ?><div class="field-error"><?= e(error_of('category')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="level">Trinh do *</label>
                <select id="level" name="level" class="<?= error_of('level') ? 'has-error' : '' ?>">
                    <?php foreach ($levels as $lv): ?>
                        <option value="<?= e($lv) ?>" <?= $curLevel === $lv ? 'selected' : '' ?>><?= e($levelLabels[$lv] ?? $lv) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('level')): ?><div class="field-error"><?= e(error_of('level')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price">Hoc phi (VND) *</label>
                <input type="number" id="price" name="price" min="0" step="1000" value="<?= e($curPrice) ?>"
                       class="<?= error_of('price') ? 'has-error' : '' ?>">
                <?php if (error_of('price')): ?><div class="field-error"><?= e(error_of('price')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="duration_weeks">Thoi luong (tuan) *</label>
                <input type="number" id="duration_weeks" name="duration_weeks" min="1" max="260" value="<?= e($curWeeks) ?>"
                       class="<?= error_of('duration_weeks') ? 'has-error' : '' ?>">
                <?php if (error_of('duration_weeks')): ?><div class="field-error"><?= e(error_of('duration_weeks')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="image">Anh minh hoa</label>
                <select id="image" name="image" class="<?= error_of('image') ? 'has-error' : '' ?>" onchange="eduCoursePreview(this.value)">
                    <option value="">-- Khong dung anh --</option>
                    <?php foreach ($images as $img): ?>
                        <option value="<?= e($img) ?>" <?= $curImage === $img ? 'selected' : '' ?>><?= e($img) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('image')): ?><div class="field-error"><?= e(error_of('image')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Xem truoc anh</label>
                <div class="course-preview" id="coursePreview">
                    <?php if ($curImage !== ''): ?><img src="/assets/img/courses/<?= e($curImage) ?>" alt="preview"><?php else: ?><span class="muted">Chua chon anh</span><?php endif; ?>
                </div>
            </div>
            <div class="form-group full">
                <label for="description">Gioi thieu khoa hoc</label>
                <textarea id="description" name="description" rows="3" placeholder="Doan van gioi thieu: khoa nay danh cho ai, hoc gi, dat duoc gi..."><?= e($curDesc) ?></textarea>
                <?php if (error_of('description')): ?><div class="field-error"><?= e(error_of('description')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="outcomes">Ban se hoc duoc gi <span class="muted" style="font-weight:400;">(moi dong 1 y)</span></label>
                <textarea id="outcomes" name="outcomes" rows="4" placeholder="Nam vung ngu phap cot loi&#10;Xay dung tu vung theo chu de&#10;Tu tin lam bai thi thu"><?= e($curOutcomes) ?></textarea>
                <?php if (error_of('outcomes')): ?><div class="field-error"><?= e(error_of('outcomes')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label class="remember-row" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" style="width:auto;margin:0;" <?= $curActive === '1' ? 'checked' : '' ?>>
                    Hien khoa hoc nay tren form lead/phieu hoc phi
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cap nhat</button>
            <a href="/courses" class="btn btn-ghost">Huy</a>
        </div>
    </form>
</div>

<script>
function eduCoursePreview(file) {
    var box = document.getElementById('coursePreview');
    if (!box) return;
    if (file) {
        box.innerHTML = '<img src="/assets/img/courses/' + encodeURIComponent(file) + '" alt="preview">';
    } else {
        box.innerHTML = '<span class="muted">Chua chon anh</span>';
    }
}
</script>
