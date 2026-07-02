<?php

/**
 * EduCRM - Training Center CRM
 * public/index.php - Front Controller (entry point duy nhat)
 *
 * Moi request deu di qua file nay:
 *  1. Cho php -S phuc vu file tinh
 *  2. Khoi tao session voi cookie flags an toan (TRUOC session_start)
 *  3. Load autoload PSR-4 + helpers
 *  4. Khai bao routes tap trung
 *  5. Dispatch + xu ly loi an toan (production: khong lo SQLSTATE/path)
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 * Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
 */

declare(strict_types=1);

// ---- 1. Cho PHP built-in server phuc vu file tinh (CSS, JS, anh) ----
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// ---- 2. Load autoload + helpers + config ----
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Core/helpers.php';

// Nap .env (neu co) TRUOC khi doc config. Bien moi truong THAT (Docker/PaaS)
// van duoc uu tien hon file .env.
\App\Core\Env::load(__DIR__ . '/../.env');

$appConfig = require __DIR__ . '/../config/app.php';
$debug     = (bool) ($appConfig['debug'] ?? false);

// ---- 3. Session: cookie flags an toan TRUOC session_start() ----
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) == 443);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,   // Secure khi chay HTTPS (production)
    'httponly' => true,       // JS khong doc duoc cookie -> giam XSS steal
    'samesite' => 'Lax',      // giam CSRF cross-site
]);
session_start();

// ---- 3b. HTTP security headers (portable, khong phu thuoc web server) ----
send_security_headers($isHttps);

use App\Core\Router;
use App\Services\AuthService;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PublicLeadController;
use App\Controllers\LeadController;
use App\Controllers\OrderController;
use App\Controllers\CourseController;
use App\Controllers\AuditController;
use App\Controllers\HealthController;
use App\Controllers\ApiController;

/**
 * Render trang loi (404/405/500) trong layout loi rieng.
 */
function render_error(int $code, string $view, array $data = []): void
{
    http_response_code($code);
    echo render('errors/' . $view, $data, 'layouts/error');
}

/**
 * Gui HTTP security headers (an toan, portable qua moi web server).
 * Bat/tat qua .env APP_SECURITY_HEADERS (mac dinh: bat).
 * CSP cho phep inline script/style (dashboard bars, nut in hoa don) de khong
 * vo layout, nhung chan moi tai nguyen ngoai (default-src 'self').
 */
function send_security_headers(bool $isHttps): void
{
    if (!\App\Core\Env::get('APP_SECURITY_HEADERS', true)) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    header(
        "Content-Security-Policy: default-src 'self'; "
        . "img-src 'self' data:; "
        . "style-src 'self' 'unsafe-inline'; "
        . "script-src 'self' 'unsafe-inline'; "
        . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'"
    );

    // HSTS chi khi da chay HTTPS (tranh khoa nham khi con HTTP)
    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ---- 3b. F6b: thu auto-login bang remember-me cookie (truoc khi dispatch) ----
// Neu chua co session nhung cookie remember hop le -> khoi tao session + rotate token.
(new AuthService())->attemptRememberLogin();

// ---- 4. Khoi tao Router + Controllers ----
$router = new Router();

$auth      = new AuthController();
$dashboard = new DashboardController();
$publicLd  = new PublicLeadController();
$leads     = new LeadController();
$orders    = new OrderController();
$course    = new CourseController();
$audit     = new AuditController();
$health    = new HealthController();
$api       = new ApiController();

// ---- 5. Khai bao Routes (METHOD + PATH -> Controller@Action) ----

// Trang chu: da login -> dashboard, chua login -> form cong khai
$router->get('/', function () {
    redirect(current_user() ? '/dashboard' : '/public-leads/create');
});

// Auth
$router->get('/login',   [$auth, 'loginForm']);
$router->post('/login',  [$auth, 'handleLogin']);
$router->post('/logout', [$auth, 'logout']);

// Dashboard (yeu cau login)
$router->get('/dashboard', [$dashboard, 'index']);

// Form cong khai (anti-spam)
$router->get('/public-leads/create', [$publicLd, 'create']);
$router->post('/public-leads',       [$publicLd, 'store']);

// Module A - Leads
$router->get('/leads',              [$leads, 'index']);
$router->get('/leads/create',       [$leads, 'create']);
$router->post('/leads/store',       [$leads, 'store']);
$router->get('/leads/edit',         [$leads, 'edit']);
$router->post('/leads/update',      [$leads, 'update']);
$router->post('/leads/delete',      [$leads, 'delete']);        // F2: xoa mem
$router->post('/leads/bulk',        [$leads, 'bulk']);          // F9: hang loat
$router->get('/leads/export',       [$leads, 'export']);        // F4: CSV
$router->get('/leads/convert',      [$leads, 'convert']);       // F1: form chuyen doi
$router->get('/leads/trash',        [$leads, 'trash']);         // F2: thung rac (admin)
$router->post('/leads/restore',     [$leads, 'restore']);       // F2: khoi phuc (admin)
$router->post('/leads/force-delete', [$leads, 'forceDelete']);  // F2: xoa vinh vien (admin)

// Module B - Orders
$router->get('/orders',              [$orders, 'index']);
$router->get('/orders/create',       [$orders, 'create']);
$router->post('/orders/store',       [$orders, 'store']);       // F1: nhan from_lead_id
$router->get('/orders/edit',         [$orders, 'edit']);
$router->post('/orders/update',      [$orders, 'update']);
$router->post('/orders/delete',      [$orders, 'delete']);      // F2: xoa mem
$router->post('/orders/bulk',        [$orders, 'bulk']);        // F9: hang loat
$router->get('/orders/export',       [$orders, 'export']);      // F4: CSV
$router->get('/orders/invoice',       [$orders, 'invoice']);     // F10: hoa don in
$router->get('/orders/trash',        [$orders, 'trash']);       // F2: thung rac (admin)
$router->post('/orders/restore',     [$orders, 'restore']);     // F2: khoi phuc (admin)
$router->post('/orders/force-delete', [$orders, 'forceDelete']); // F2: xoa vinh vien (admin)

// Module C - Khoa hoc (view: moi user login; quan ly: manage_courses)
$router->get('/courses',               [$course, 'index']);
$router->get('/courses/create',        [$course, 'create']);
$router->post('/courses/store',        [$course, 'store']);
$router->get('/courses/edit',          [$course, 'edit']);
$router->post('/courses/update',       [$course, 'update']);
$router->post('/courses/toggle',       [$course, 'toggle']);   // bat/tat hien thi
$router->post('/courses/delete',       [$course, 'delete']);   // F2: xoa mem
$router->get('/courses/export',        [$course, 'export']);   // F4: CSV
$router->get('/courses/trash',         [$course, 'trash']);    // F2: thung rac
$router->post('/courses/restore',      [$course, 'restore']);  // F2: khoi phuc
$router->post('/courses/force-delete', [$course, 'forceDelete']); // F2: xoa vinh vien (admin)

// F3 - Audit log (admin only, enforce trong controller)
$router->get('/audit', [$audit, 'index']);

// F7 - Read-only JSON API (Bearer token, stateless, rate-limited)
$router->get('/api/leads',  [$api, 'leads']);
$router->get('/api/orders', [$api, 'orders']);
$router->get('/api/stats',  [$api, 'stats']);

// Health JSON
$router->get('/health', [$health, 'index']);

// 404 / 405 handlers
$router->setNotFoundHandler(function () {
    render_error(404, '404');
});
$router->setMethodNotAllowedHandler(function (array $allowed) {
    render_error(405, '405', ['allowed' => $allowed]);
});

// ---- 6. Dispatch + global safe error handling ----
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $router->dispatch($method, $path);
} catch (\Throwable $ex) {
    // Ghi log chi tiet (server-side) - KHONG bao gio lo ra cho user.
    log_app('error', $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine());

    if ($debug) {
        // Dev: hien chi tiet de debug
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "DEBUG 500: " . $ex->getMessage() . "\n" . $ex->getTraceAsString();
    } else {
        // Production: trang loi an toan, khong SQLSTATE/path/stack trace.
        render_error(500, '500');
    }
}
