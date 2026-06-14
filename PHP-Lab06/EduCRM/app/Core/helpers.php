<?php

/**
 * EduCRM - Core helpers
 *
 * Cac ham tien ich dung chung toan ung dung:
 *  e(), redirect(), render(), partial(), flash()/get_flash(), old(),
 *  require_login(), current_user(), config(), csrf_token(), csrf_field(),
 *  verify_csrf(), log_app().
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */

use App\Core\Database;

if (!function_exists('config')) {
    /**
     * Doc gia tri tu config/app.php theo key (cache trong static).
     */
    function config(string $key, $default = null)
    {
        static $cfg = null;
        if ($cfg === null) {
            $cfg = require __DIR__ . '/../../config/app.php';
        }
        return $cfg[$key] ?? $default;
    }
}

if (!function_exists('e')) {
    /**
     * Escape output chong XSS. Dung cho MOI du lieu tu DB / user input
     * khi in ra HTML trong View.
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect 302 (mac dinh) + dung script ngay (PRG pattern).
     */
    function redirect(string $url, int $code = 302): void
    {
        http_response_code($code);
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('view_path')) {
    function view_path(string $name): string
    {
        return __DIR__ . '/../Views/' . $name . '.php';
    }
}

if (!function_exists('render')) {
    /**
     * Render mot view con BEN TRONG layout chung (layouts/main.php).
     * Tra ve chuoi HTML hoan chinh - Controller chi viec echo.
     */
    function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        // Render view con -> bien $content
        extract($data, EXTR_SKIP);
        ob_start();
        require view_path($view);
        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        // Bo $content vao layout
        ob_start();
        require view_path($layout);
        return ob_get_clean();
    }
}

if (!function_exists('partial')) {
    /**
     * Nhung mot partial (vi du partials/nav, partials/flash) vao view.
     */
    function partial(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require view_path('partials/' . $name);
    }
}

if (!function_exists('flash')) {
    /**
     * Dat flash message (hien 1 lan o request ke tiep).
     * type: success | error | info
     */
    function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('get_flash')) {
    /**
     * Lay het flash message va xoa khoi session (chi hien 1 lan).
     */
    function get_flash(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $messages;
    }
}

if (!function_exists('set_old')) {
    /**
     * Luu old input + loi vao session de hien lai khi form loi (sau redirect).
     */
    function set_old(array $input, array $errors): void
    {
        $_SESSION['_old']    = $input;
        $_SESSION['_errors'] = $errors;
    }
}

if (!function_exists('old')) {
    /**
     * Lay gia tri da nhap truoc do (giu old input khi validate that bai).
     */
    function old(string $key, $default = '')
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    /** Lay mang loi hien tai (theo field). */
    function errors(): array
    {
        return $_SESSION['_errors'] ?? [];
    }
}

if (!function_exists('error_of')) {
    /** Lay thong bao loi cua mot field cu the (rong neu khong co). */
    function error_of(string $field): string
    {
        return $_SESSION['_errors'][$field] ?? '';
    }
}

if (!function_exists('clear_old')) {
    /** Xoa old input + errors sau khi da render form loi xong. */
    function clear_old(): void
    {
        unset($_SESSION['_old'], $_SESSION['_errors']);
    }
}

if (!function_exists('current_user')) {
    /** Tra ve thong tin user dang dang nhap (hoac null). */
    function current_user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return (current_user()['role'] ?? null) === 'admin';
    }
}

if (!function_exists('current_role')) {
    /** Vai tro cua user dang dang nhap ('' neu chua login). */
    function current_role(): string
    {
        return (string) (current_user()['role'] ?? '');
    }
}

if (!function_exists('is_manager')) {
    /** Manager hoac admin (admin co tat ca quyen cua manager). */
    function is_manager(): bool
    {
        return in_array(current_role(), ['admin', 'manager'], true);
    }
}

if (!function_exists('can')) {
    /**
     * F11: ma tran phan quyen (enforce SERVER-SIDE). Tra true neu vai tro hien
     * tai duoc phep thuc hien $permission. Cac quyen:
     *   - 'create','update'         : admin, manager, staff
     *   - 'soft_delete','restore',
     *     'trash','export',
     *     'status_change'           : admin, manager
     *   - 'force_delete','audit',
     *     'manage_users'            : admin
     */
    function can(string $permission): bool
    {
        $role = current_role();
        if ($role === '') {
            return false;
        }

        $matrix = [
            'admin'   => [
                'create', 'update', 'soft_delete', 'restore', 'trash',
                'export', 'status_change', 'force_delete', 'audit', 'manage_users',
            ],
            'manager' => [
                'create', 'update', 'soft_delete', 'restore', 'trash',
                'export', 'status_change',
            ],
            'staff'   => [
                'create', 'update',
            ],
        ];

        return in_array($permission, $matrix[$role] ?? [], true);
    }
}

if (!function_exists('require_can')) {
    /**
     * Chan action neu vai tro hien tai khong co quyen $permission.
     * Flash + redirect ve $redirectTo (mac dinh /dashboard). Dung trong
     * Controller TRUOC khi goi Service (server-side enforcement).
     */
    function require_can(string $permission, string $redirectTo = '/dashboard'): void
    {
        if (!can($permission)) {
            flash('error', 'Ban khong co quyen thuc hien thao tac nay.');
            redirect($redirectTo);
        }
    }
}

if (!function_exists('require_login')) {
    /**
     * Chan route can dang nhap. Kiem tra ca idle timeout.
     * Chua login hoac het han -> flash + redirect /login.
     */
    function require_login(): void
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Vui long dang nhap de tiep tuc.');
            redirect('/login');
        }

        // Kiem tra idle timeout
        $timeout = (int) config('session_idle_timeout', 1800);
        $last    = $_SESSION['last_activity'] ?? time();
        if (time() - $last > $timeout) {
            // Het han -> dang xuat sach
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
            session_start();
            flash('error', 'Phien lam viec da het han, vui long dang nhap lai.');
            redirect('/login');
        }

        // Cap nhat moc hoat dong cuoi
        $_SESSION['last_activity'] = time();
    }
}

if (!function_exists('csrf_token')) {
    /** Lay (hoac tao) CSRF token cho phien hien tai. */
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    /** In hidden input chua CSRF token de nhung vao form POST. */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /** Kiem tra CSRF token cua request POST (so sanh hash_equals). */
    function verify_csrf(): bool
    {
        $token = $_POST['_csrf'] ?? '';
        return !empty($_SESSION['_csrf']) && is_string($token)
            && hash_equals($_SESSION['_csrf'], $token);
    }
}

if (!function_exists('log_app')) {
    /**
     * Ghi log vao storage/logs/app.log (loi DB, login failed, ...).
     */
    function log_app(string $level, string $message): void
    {
        $file = config('log_file');
        $line = sprintf("[%s] %s: %s%s", date('Y-m-d H:i:s'), strtoupper($level), $message, PHP_EOL);
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
