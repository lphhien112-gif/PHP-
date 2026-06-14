<?php
/** Session demo (debug) — WorkshopHub. Bien: $sessionId, $sessionData, $idleTimeout */
?>
<div class="page-head">
    <div>
        <h1>Session Demo</h1>
        <p class="muted">Thong tin phien hien tai (phuc vu debug).</p>
    </div>
    <a href="/dashboard" class="btn btn-ghost">Ve Dashboard</a>
</div>

<div class="session-box">
    <h3>Session ID</h3>
    <p><code class="block"><?= h($sessionId) ?></code></p>
    <p class="muted small">Sau khi dang nhap, ID nay da duoc doi moi bang <code>session_regenerate_id(true)</code> de chong session fixation.</p>
</div>

<div class="session-box">
    <h3>Du lieu session</h3>
    <table class="data-table">
        <thead><tr><th>Key</th><th>Gia tri</th></tr></thead>
        <tbody>
            <?php foreach ($sessionData as $key => $val): ?>
                <tr>
                    <td><code><?= h((string) $key) ?></code></td>
                    <td>
                        <?php
                        if (in_array($key, ['login_at', 'last_activity_at'], true) && is_numeric($val)) {
                            echo h(date('Y-m-d H:i:s', (int) $val)) . ' (' . h((string) $val) . ')';
                        } else {
                            echo h(is_scalar($val) ? (string) $val : json_encode($val));
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="muted small">Idle timeout: <strong><?= (int) $idleTimeout ?> giay</strong>.</p>
</div>
