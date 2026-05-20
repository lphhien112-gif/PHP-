<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BookNest Admin') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">📚 Book<span>Nest</span></div>
            <div class="sidebar-subtitle">Admin Panel</div>
        </div>

        <nav class="sidebar-nav">
            <a href="/" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="/books" class="nav-item <?= ($currentPage ?? '') === 'books' ? 'active' : '' ?>">
                <span class="nav-icon">📖</span>
                <span class="nav-text">Quản lý sách</span>
            </a>
            <a href="/books/create" class="nav-item <?= ($currentPage ?? '') === 'create' ? 'active' : '' ?>">
                <span class="nav-icon">➕</span>
                <span class="nav-text">Thêm sách mới</span>
            </a>
            <a href="/health" class="nav-item">
                <span class="nav-icon">💚</span>
                <span class="nav-text">Health Check</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-user">
                <div class="admin-avatar">👤</div>
                <div>
                    <div class="admin-name">Admin</div>
                    <div class="admin-role">Quản trị viên</div>
                </div>
            </div>
            <a href="/logout" class="nav-item nav-logout">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Đăng xuất</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? $title ?? '') ?></h1>
            <div class="header-meta">
                <span class="header-time">🕐 <?= date('H:i — d/m/Y') ?></span>
                <span class="header-badge">Server 2 • Port 8001</span>
            </div>
        </header>

        <div class="admin-content">
            <?= $content ?? '' ?>
        </div>

        <footer class="admin-footer">
            <p>📚 <strong>BookNest Admin</strong> • PHP Lab03 • 22110059 — Lê Phạm Hồng Hiên</p>
            <p>Front Controller • Router • Standard Response • <?= date('Y') ?></p>
        </footer>
    </main>
</div>

</body>
</html>
