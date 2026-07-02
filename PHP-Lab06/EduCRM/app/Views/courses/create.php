<?php
/** EduCRM - Module C: form them khoa hoc. */
$images = course_images();
$levels = config('course_levels', []);
$levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
?>
<div class="page-head"><h1>Thêm khóa học</h1><a href="/courses" class="btn btn-ghost">&laquo; Quay lại</a></div>

<div class="card">
    <form method="post" action="/courses/store">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group full">
                <label for="name">Tên khóa học *</label>
                <input type="text" id="name" name="name" value="<?= e(old('name')) ?>"
                       class="<?= error_of('name') ? 'has-error' : '' ?>">
                <?php if (error_of('name')): ?><div class="field-error"><?= e(error_of('name')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="category">Nhóm *</label>
                <select id="category" name="category" class="<?= error_of('category') ? 'has-error' : '' ?>">
                    <option value="">-- Chọn nhóm --</option>
                    <?php foreach (config('course_categories') as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= old('category') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('category')): ?><div class="field-error"><?= e(error_of('category')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="level">Trình độ *</label>
                <select id="level" name="level" class="<?= error_of('level') ? 'has-error' : '' ?>">
                    <?php foreach ($levels as $lv): ?>
                        <option value="<?= e($lv) ?>" <?= old('level', 'beginner') === $lv ? 'selected' : '' ?>><?= e($levelLabels[$lv] ?? $lv) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('level')): ?><div class="field-error"><?= e(error_of('level')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price">Học phí (VND) *</label>
                <input type="number" id="price" name="price" min="0" step="1000" value="<?= e(old('price')) ?>"
                       class="<?= error_of('price') ? 'has-error' : '' ?>">
                <?php if (error_of('price')): ?><div class="field-error"><?= e(error_of('price')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="duration_weeks">Thời lượng (tuần) *</label>
                <input type="number" id="duration_weeks" name="duration_weeks" min="1" max="260" value="<?= e(old('duration_weeks', '8')) ?>"
                       class="<?= error_of('duration_weeks') ? 'has-error' : '' ?>">
                <?php if (error_of('duration_weeks')): ?><div class="field-error"><?= e(error_of('duration_weeks')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="image">Ảnh minh họa</label>
                <select id="image" name="image" class="<?= error_of('image') ? 'has-error' : '' ?>" onchange="eduCoursePreview(this.value)">
                    <option value="">-- Không dùng ảnh --</option>
                    <?php foreach ($images as $img): ?>
                        <option value="<?= e($img) ?>" <?= old('image') === $img ? 'selected' : '' ?>><?= e($img) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (error_of('image')): ?><div class="field-error"><?= e(error_of('image')) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Xem trước ảnh</label>
                <div class="course-preview" id="coursePreview">
                    <?php $oi = old('image'); ?>
                    <?php if ($oi !== ''): ?><img src="/assets/img/courses/<?= e($oi) ?>" alt="preview"><?php else: ?><span class="muted">Chưa chọn ảnh</span><?php endif; ?>
                </div>
            </div>
            <div class="form-group full">
                <label for="description">Giới thiệu khóa học</label>
                <textarea id="description" name="description" rows="3" placeholder="Đoạn văn giới thiệu: khóa này dành cho ai, học gì, đạt được gì..."><?= e(old('description')) ?></textarea>
                <?php if (error_of('description')): ?><div class="field-error"><?= e(error_of('description')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label for="outcomes">Bạn sẽ học được gì <span class="muted" style="font-weight:400;">(mỗi dòng 1 ý)</span></label>
                <textarea id="outcomes" name="outcomes" rows="4" placeholder="Nắm vững ngữ pháp cốt lõi&#10;Xây dựng từ vựng theo chủ đề&#10;Tự tin làm bài thi thử"><?= e(old('outcomes')) ?></textarea>
                <?php if (error_of('outcomes')): ?><div class="field-error"><?= e(error_of('outcomes')) ?></div><?php endif; ?>
            </div>
            <div class="form-group full">
                <label class="remember-row" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" style="width:auto;margin:0;" <?= old('is_active', '1') === '1' ? 'checked' : '' ?>>
                    Hiện khóa học này trên form lead/phiếu học phí
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu khóa học</button>
            <a href="/courses" class="btn btn-ghost">Hủy</a>
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
        box.innerHTML = '<span class="muted">Chưa chọn ảnh</span>';
    }
}
</script>
