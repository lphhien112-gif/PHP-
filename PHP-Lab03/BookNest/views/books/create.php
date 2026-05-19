<?php
// Create book form
ob_start();
?>

<div class="form-card">
    <h2>➕ Thêm sách mới</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/books">
        <div class="form-group">
            <label for="title">Tên sách *</label>
            <input type="text" id="title" name="title" placeholder="Nhập tên sách..." 
                   value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="author">Tác giả *</label>
            <input type="text" id="author" name="author" placeholder="Nhập tên tác giả..." 
                   value="<?= htmlspecialchars($old['author'] ?? '') ?>" required>
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
                <input type="number" id="price" name="price" placeholder="VD: 85000" min="0"
                       value="<?= htmlspecialchars($old['price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="stock">Số lượng tồn</label>
                <input type="number" id="stock" name="stock" placeholder="VD: 10" min="0"
                       value="<?= htmlspecialchars($old['stock'] ?? '') ?>">
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">💾 Lưu sách</button>
            <a href="/books" class="btn btn-secondary" style="flex: 1; justify-content: center;">← Quay lại</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
