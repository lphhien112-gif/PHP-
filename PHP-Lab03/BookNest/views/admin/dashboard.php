<?php
ob_start();
$pageTitle = '📊 Dashboard';
?>

<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">📚</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalBooks ?></div>
            <div class="stat-label">Tổng đầu sách</div>
        </div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalStock ?></div>
            <div class="stat-label">Tổng tồn kho</div>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <div class="stat-value"><?= $lowStock ?></div>
            <div class="stat-label">Sắp hết hàng</div>
        </div>
    </div>
    <div class="stat-card stat-danger">
        <div class="stat-icon">❌</div>
        <div class="stat-info">
            <div class="stat-value"><?= $outOfStock ?></div>
            <div class="stat-label">Hết hàng</div>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">🏷️</div>
        <div class="stat-info">
            <div class="stat-value"><?= $categories ?></div>
            <div class="stat-label">Thể loại</div>
        </div>
    </div>
    <div class="stat-card stat-accent">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($totalValue, 0, ',', '.') ?>đ</div>
            <div class="stat-label">Giá trị kho</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>📋 Sách mới cập nhật</h2>
        <a href="/books/create" class="btn btn-sm btn-primary">➕ Thêm sách</a>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Thể loại</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($books, 0, 5) as $i => $book): ?>
                <tr>
                    <td class="td-id"><?= $book['id'] ?></td>
                    <td class="td-title"><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><span class="badge badge-category"><?= htmlspecialchars($book['category']) ?></span></td>
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
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <a href="/books" class="link-more">Xem tất cả <?= $totalBooks ?> sách →</a>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>🗺️ Route Map — Admin Server</h2>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Method</th><th>Path</th><th>Controller</th><th>Action</th><th>Status</th></tr>
            </thead>
            <tbody>
                <tr><td><span class="method-get">GET</span></td><td><code>/</code></td><td>AdminController</td><td>dashboard</td><td><span class="badge badge-success">200</span></td></tr>
                <tr><td><span class="method-get">GET</span></td><td><code>/books</code></td><td>AdminController</td><td>books</td><td><span class="badge badge-success">200</span></td></tr>
                <tr><td><span class="method-get">GET</span></td><td><code>/books/create</code></td><td>AdminController</td><td>createBook</td><td><span class="badge badge-success">200</span></td></tr>
                <tr><td><span class="method-post">POST</span></td><td><code>/books</code></td><td>AdminController</td><td>storeBook</td><td><span class="badge badge-info">302</span></td></tr>
                <tr><td><span class="method-get">GET</span></td><td><code>/health</code></td><td>HealthController</td><td>check</td><td><span class="badge badge-success">200</span></td></tr>
                <tr><td><span class="method-get">GET</span></td><td><code>/login</code></td><td>AdminController</td><td>loginForm</td><td><span class="badge badge-success">200</span></td></tr>
                <tr><td><span class="method-post">POST</span></td><td><code>/login</code></td><td>AdminController</td><td>login</td><td><span class="badge badge-info">302</span></td></tr>
                <tr><td><span class="method-get">GET</span></td><td><code>/logout</code></td><td>AdminController</td><td>logout</td><td><span class="badge badge-info">302</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../admin/layout.php';
?>
