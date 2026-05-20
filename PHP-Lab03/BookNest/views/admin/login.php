<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Login') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-body">

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">📚</div>
            <h1>BookNest <span>Admin</span></h1>
            <p>Đăng nhập để quản trị hệ thống</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="username">👤 Tên đăng nhập</label>
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập..." value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">🔒 Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu..." required>
            </div>
            <button type="submit" class="btn btn-primary btn-login">🔓 Đăng nhập</button>
        </form>

        <p class="login-hint">💡 Demo: <strong>admin / admin</strong></p>
        <div class="login-footer">
            <p>Server 2 • Port 8001 • Admin Panel</p>
        </div>
    </div>
</div>

</body>
</html>
