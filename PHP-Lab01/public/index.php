<?php require __DIR__ . '/../views/header.php'; ?>

<section class="hero">
    <div class="container hero-content">
        <span class="hero-tag">Week 1 PHP Project</span>
        <h1>Mini Stationery Store App</h1>
        <p>
            A simple stationery store management application for displaying products,
            tracking stock status, and reviewing inventory statistics.
        </p>
        <a href="/products.php" class="btn btn-primary">View Products</a>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <h2 class="section-title">Main Features</h2>

        <div class="feature-grid">
            <div class="feature-card">
                <h3>Product List</h3>
                <p>Display stationery products with category, brand, price, quantity, and status.</p>
            </div>

            <div class="feature-card">
                <h3>Stock Status</h3>
                <p>Automatically classify each product as Available, Low stock, or Out of stock.</p>
            </div>

            <div class="feature-card">
                <h3>Inventory Statistics</h3>
                <p>Show total product lines, total quantity, and available products in one place.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../views/footer.php'; ?>