<?php
// Book list page — Card grid with book covers
ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">📚 Bộ sưu tập sách</h1>
        <p class="page-subtitle">Khám phá <?= count($books) ?> đầu sách từ văn học Việt Nam đến công nghệ quốc tế</p>
    </div>
    <a href="/books/create" class="btn btn-primary">➕ Thêm sách</a>
</div>

<!-- Book Cards Grid -->
<div class="book-grid">
    <?php foreach ($books as $book): ?>
    <div class="book-card">
        <div class="book-card-image">
            <img src="/assets/images/<?= $book['image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <!-- Status overlay -->
            <?php if ($book['status'] === 'Available'): ?>
                <span class="book-status-overlay status-available">Còn hàng</span>
            <?php elseif ($book['status'] === 'Low stock'): ?>
                <span class="book-status-overlay status-low">Sắp hết</span>
            <?php else: ?>
                <span class="book-status-overlay status-out">Hết hàng</span>
            <?php endif; ?>
        </div>
        <div class="book-card-body">
            <span class="badge badge-category"><?= htmlspecialchars($book['category']) ?></span>
            <h3 class="book-card-title"><?= htmlspecialchars($book['title']) ?></h3>
            <p class="book-card-author">✍️ <?= htmlspecialchars($book['author']) ?> · <?= $book['year'] ?></p>
            <p class="book-card-desc"><?= htmlspecialchars($book['desc']) ?></p>
            <div class="book-card-footer">
                <span class="price"><?= number_format($book['price'], 0, ',', '.') ?>đ</span>
                <span class="book-stock">📦 <?= $book['stock'] ?> cuốn</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Also show as table for data completeness -->
<div class="book-table-wrapper" style="margin-top: 2rem;">
    <div class="book-table-header">
        <h2>📊 Bảng dữ liệu chi tiết</h2>
        <span class="badge badge-available"><?= count($books) ?> cuốn</span>
    </div>
    <table class="book-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Năm XB</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><strong><?= htmlspecialchars($book['title']) ?></strong></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><span class="badge badge-category"><?= htmlspecialchars($book['category']) ?></span></td>
                <td><?= $book['year'] ?></td>
                <td class="price"><?= number_format($book['price'], 0, ',', '.') ?>đ</td>
                <td><?= $book['stock'] ?></td>
                <td>
                    <?php if ($book['status'] === 'Available'): ?>
                        <span class="badge badge-available">✅ Còn hàng</span>
                    <?php elseif ($book['status'] === 'Low stock'): ?>
                        <span class="badge badge-low">⚠️ Sắp hết</span>
                    <?php else: ?>
                        <span class="badge badge-out">❌ Hết hàng</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
