<?php
// Home page content — rendered inside layout
ob_start();
?>

<!-- Hero Section with Background Image -->
<section class="hero" style="background-image: url('/assets/images/hero-banner.png');">
    <div class="hero-overlay">
        <h1>Chào mừng đến <span>BookNest</span></h1>
        <p>Hiệu sách trực tuyến với bộ sưu tập đa dạng từ văn học Việt Nam đến sách công nghệ quốc tế. Kiến trúc 2 Server — User Storefront & Admin Dashboard.</p>
        <div class="hero-buttons">
            <a href="/books" class="btn btn-primary">📖 Xem danh sách sách</a>
            <a href="/books/create" class="btn btn-secondary">➕ Thêm sách mới</a>
            <a href="http://localhost:8001" class="btn btn-secondary" target="_blank">⚙️ Mở Admin Panel</a>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value"><?= $totalBooks ?? 10 ?></div>
        <div class="stat-label">Đầu sách</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📂</div>
        <div class="stat-value"><?= $categories ?? 5 ?></div>
        <div class="stat-label">Thể loại</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✍️</div>
        <div class="stat-value"><?= $totalAuthors ?? 10 ?></div>
        <div class="stat-label">Tác giả</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">6</div>
        <div class="stat-label">Còn hàng</div>
    </div>
</section>

<!-- Dual Server Architecture -->
<section class="section-title">
    <h2>🏗️ Kiến trúc 2 Server</h2>
    <p>BookNest tách biệt giao diện User và Admin thành 2 Front Controller độc lập</p>
</section>

<section class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">🌐</div>
        <h3>Server 1 — User (Port 8000)</h3>
        <p>Giao diện người dùng: xem sách, tìm kiếm, đăng nhập. Entry point: <code>public/index.php</code></p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">⚙️</div>
        <h3>Server 2 — Admin (Port 8001)</h3>
        <p>Bảng điều khiển quản trị: dashboard, quản lý sách, CRUD. Entry point: <code>public/admin.php</code></p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🗺️</div>
        <h3>Router Pattern</h3>
        <p>Map <code>METHOD + PATH → Controller@Action</code>. Phân biệt rõ 404 và 405.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3>Response chuẩn HTTP</h3>
        <p>HTML, JSON, Redirect — mỗi loại có đúng status code, Content-Type, và headers.</p>
    </div>
</section>

<!-- Route Reference Table — Server 1 -->
<section style="margin-top: 2rem;">
    <div class="book-table-wrapper">
        <div class="book-table-header">
            <h2>📋 Routes — Server 1 (User)</h2>
            <span class="badge badge-available">🟢 Port 8000 • 9 routes</span>
        </div>
        <table class="book-table">
            <thead>
                <tr>
                    <th>Method</th>
                    <th>URL</th>
                    <th>Controller@Action</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/</code></td><td>HomeController@index</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/health</code></td><td>HealthController@check</td><td><span class="badge badge-available">200 JSON</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/books</code></td><td>BookController@index</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/books/create</code></td><td>BookController@create</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-post">POST</span></td><td><code>/books</code></td><td>BookController@store</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/login</code></td><td>AuthController@loginForm</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-post">POST</span></td><td><code>/login</code></td><td>AuthController@login</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/logout</code></td><td>AuthController@logout</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/go-home</code></td><td>Closure</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Route Reference Table — Server 2 -->
<section style="margin-top: 1.5rem;">
    <div class="book-table-wrapper">
        <div class="book-table-header">
            <h2>📋 Routes — Server 2 (Admin)</h2>
            <span class="badge badge-category">🔵 Port 8001 • 8 routes</span>
        </div>
        <table class="book-table">
            <thead>
                <tr>
                    <th>Method</th>
                    <th>URL</th>
                    <th>Controller@Action</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/</code></td><td>AdminController@dashboard</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/books</code></td><td>AdminController@books</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/books/create</code></td><td>AdminController@createBook</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-post">POST</span></td><td><code>/books</code></td><td>AdminController@storeBook</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/health</code></td><td>HealthController@check</td><td><span class="badge badge-available">200 JSON</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/login</code></td><td>AdminController@loginForm</td><td><span class="badge badge-available">200 HTML</span></td></tr>
                <tr><td><span class="method-badge method-post">POST</span></td><td><code>/login</code></td><td>AdminController@login</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
                <tr><td><span class="method-badge method-get">GET</span></td><td><code>/logout</code></td><td>AdminController@logout</td><td><span class="badge badge-low">302 Redirect</span></td></tr>
            </tbody>
        </table>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
