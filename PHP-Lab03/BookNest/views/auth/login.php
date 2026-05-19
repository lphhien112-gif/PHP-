<?php
// Login form
ob_start();
?>

<div class="form-card">
    <h2>🔐 Đăng nhập BookNest</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập..." 
                   value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu..." required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
            🔓 Đăng nhập
        </button>
    </form>

    <p style="text-align: center; margin-top: 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
        💡 Demo: Dùng <strong style="color: var(--accent);">admin / admin</strong> để đăng nhập
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
