<?php
ob_start();
$pageTitle = '➕ Thêm sách mới';
?>

<div class="panel" style="max-width: 700px;">
    <div class="panel-header">
        <h2>📝 Nhập thông tin sách</h2>
    </div>
    <div class="panel-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/books">
            <div class="form-group">
                <label for="title">Tên sách *</label>
                <input type="text" id="title" name="title" placeholder="Nhập tên sách..." value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Tác giả *</label>
                <input type="text" id="author" name="author" placeholder="Nhập tên tác giả..." value="<?= htmlspecialchars($old['author'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Thể loại</label>
                <select id="category" name="category">
                    <option value="Văn học">Văn học</option>
                    <option value="Tiểu thuyết">Tiểu thuyết</option>
                    <option value="Kỹ năng sống">Kỹ năng sống</option>
                    <option value="Công nghệ">Công nghệ</option>
                    <option value="Khoa học">Khoa học</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Giá (VNĐ)</label>
                    <input type="number" id="price" name="price" placeholder="85000" min="0" value="<?= htmlspecialchars($old['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="stock">Số lượng tồn</label>
                    <input type="number" id="stock" name="stock" placeholder="10" min="0" value="<?= htmlspecialchars($old['stock'] ?? '') ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Lưu sách</button>
                <a href="/books" class="btn btn-secondary">← Quay lại</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layout.php';
?>
