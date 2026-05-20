<?php
ob_start();
$pageTitle = '📖 Quản lý sách';
?>

<div class="panel-toolbar">
    <div class="toolbar-info">
        <span>📚 Tổng: <strong><?= count($books) ?></strong> sách</span>
    </div>
    <a href="/books/create" class="btn btn-primary">➕ Thêm sách mới</a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Thể loại</th>
                    <th>Năm XB</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td class="td-id"><?= $book['id'] ?></td>
                    <td class="td-title"><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><span class="badge badge-category"><?= htmlspecialchars($book['category']) ?></span></td>
                    <td><?= $book['year'] ?></td>
                    <td class="td-price"><?= number_format($book['price'], 0, ',', '.') ?>đ</td>
                    <td class="td-stock"><?= $book['stock'] ?></td>
                    <td>
                        <?php
                        $statusClass = match($book['status']) {
                            'Available' => 'badge-success',
                            'Low stock' => 'badge-warning',
                            'Out of stock' => 'badge-danger',
                            default => 'badge-default',
                        };
                        $statusText = match($book['status']) {
                            'Available' => 'Còn hàng',
                            'Low stock' => 'Sắp hết',
                            'Out of stock' => 'Hết hàng',
                            default => $book['status'],
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                    </td>
                    <td class="td-actions">
                        <button class="btn-icon" title="Xem">👁️</button>
                        <button class="btn-icon" title="Sửa">✏️</button>
                        <button class="btn-icon btn-icon-danger" title="Xoá">🗑️</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layout.php';
?>
