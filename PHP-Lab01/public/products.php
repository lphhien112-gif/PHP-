<?php

$products = require __DIR__ . '/../src/data/products.php';
require __DIR__ . '/../src/helpers/functions.php';

$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? 'all';
$status = $_GET['status'] ?? 'all';

$filteredProducts = searchProducts($products, $keyword);
$filteredProducts = filterProducts($filteredProducts, $category, $status);

$totalProducts = count($products);
$totalQuantity = getTotalQuantity($products);
$availableCount = count(getAvailableProducts($products));
$categories = getCategories($products);

require __DIR__ . '/../views/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <h2 class="section-title">Inventory Dashboard</h2>

        <div class="stats-grid">
            <div class="stat-card stat-blue">
                <span>Total product lines</span>
                <strong><?php echo $totalProducts; ?></strong>
            </div>

            <div class="stat-card stat-green">
                <span>Total quantity</span>
                <strong><?php echo $totalQuantity; ?></strong>
            </div>

            <div class="stat-card stat-purple">
                <span>Available products</span>
                <strong><?php echo $availableCount; ?></strong>
            </div>
        </div>

        <form method="GET" class="filter-bar">
            <input
                type="text"
                name="keyword"
                placeholder="Search by product, category, or brand"
                value="<?php echo htmlspecialchars($keyword); ?>"
            >

            <select name="category">
                <option value="all">All categories</option>
                <?php foreach ($categories as $itemCategory): ?>
                    <option value="<?php echo htmlspecialchars($itemCategory); ?>" <?php echo $category === $itemCategory ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($itemCategory); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="all">All statuses</option>
                <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
                <option value="Low stock" <?php echo $status === 'Low stock' ? 'selected' : ''; ?>>Low stock</option>
                <option value="Out of stock" <?php echo $status === 'Out of stock' ? 'selected' : ''; ?>>Out of stock</option>
            </select>

            <button type="submit" class="btn btn-dark">Apply</button>
        </form>

        <?php if (count($filteredProducts) > 0): ?>
            <div class="product-grid">
                <?php foreach ($filteredProducts as $product): ?>
                    <?php $statusText = getProductStatus($product['quantity']); ?>
                    <div class="product-card">
                        <div class="product-card-top">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <span class="status-badge <?php echo getStatusClass($statusText); ?>">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </div>

                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-brand">Brand: <?php echo htmlspecialchars($product['brand']); ?></p>

                        <div class="product-info">
                            <p><strong>Price:</strong> <?php echo number_format($product['price'], 2); ?> USD</p>
                            <p><strong>Quantity:</strong> <?php echo $product['quantity']; ?></p>
                        </div>

                        <button class="btn btn-dark full-width" type="button">View Product</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-box">
                <h3>No matching products found</h3>
                <p>Please try another keyword or filter.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../views/footer.php'; ?>