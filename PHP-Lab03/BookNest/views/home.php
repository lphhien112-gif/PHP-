<?php
// Home page content — rendered inside layout
ob_start();
?>

<!-- Hero Section with Background Image -->
<section class="hero" style="background-image: url('/assets/images/hero-banner.png');">
    <div class="hero-overlay">
        <h1>Chào mừng đến <span>BookNest</span></h1>
        <p>Hiệu sách trực tuyến với bộ sưu tập đa dạng từ văn học Việt Nam đến sách công nghệ quốc tế. Khám phá, tìm kiếm và quản lý kho sách dễ dàng.</p>
        <div class="hero-buttons">
            <a href="/books" class="btn btn-primary">📖 Xem danh sách sách</a>
            <a href="/books/create" class="btn btn-secondary">➕ Thêm sách mới</a>
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

<!-- Features -->
<section class="section-title">
    <h2>🛠️ Kiến trúc hệ thống</h2>
    <p>BookNest được xây dựng theo mô hình Front Controller + Router + Response chuẩn HTTP</p>
</section>

<section class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">🔗</div>
        <h3>Front Controller</h3>
        <p>Mọi request đi qua <code>public/index.php</code> — single entry point cho toàn bộ ứng dụng.</p>
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
    <div class="feature-card">
        <div class="feature-icon">📦</div>
        <h3>PSR-4 Autoload</h3>
        <p>Namespace <code>BookNest\\</code> map tới <code>src/</code> qua Composer autoload.</p>
    </div>
</section>

<!-- Route Reference Table -->
<section style="margin-top: 2rem;">
    <div class="book-table-wrapper">
        <div class="book-table-header">
            <h2>📋 Danh sách Routes</h2>
            <span class="badge badge-available">9 routes</span>
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
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/</code></td>
                    <td>HomeController@index</td>
                    <td><span class="badge badge-available">200 HTML</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/health</code></td>
                    <td>HealthController@check</td>
                    <td><span class="badge badge-available">200 JSON</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/books</code></td>
                    <td>BookController@index</td>
                    <td><span class="badge badge-available">200 HTML</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/books/create</code></td>
                    <td>BookController@create</td>
                    <td><span class="badge badge-available">200 HTML</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-post">POST</span></td>
                    <td><code>/books</code></td>
                    <td>BookController@store</td>
                    <td><span class="badge badge-low">302 Redirect</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/login</code></td>
                    <td>AuthController@loginForm</td>
                    <td><span class="badge badge-available">200 HTML</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-post">POST</span></td>
                    <td><code>/login</code></td>
                    <td>AuthController@login</td>
                    <td><span class="badge badge-low">302 Redirect</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/logout</code></td>
                    <td>AuthController@logout</td>
                    <td><span class="badge badge-low">302 Redirect</span></td>
                </tr>
                <tr>
                    <td><span class="method-badge method-get">GET</span></td>
                    <td><code>/go-home</code></td>
                    <td>Closure</td>
                    <td><span class="badge badge-low">302 Redirect</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
