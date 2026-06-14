<?php

/**
 * WorkshopHub — Front Controller (entry point duy nhat)
 * ==================================================================
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * Truong DH Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc
 * ==================================================================
 * Nhiem vu:
 *   1. Phuc vu file tinh (CSS) cho PHP built-in server.
 *   2. Cau hinh session cookie params TRUOC session_start().
 *   3. Load autoloader (PSR-4) + helper functions.
 *   4. Khoi tao Router, khai bao routes tap trung.
 *   5. Dispatch request den controller/action phu hop.
 */

// 1. PHP built-in server: tra file tinh truc tiep (CSS, anh...).
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// 2. Load Composer autoloader (PSR-4) + helpers.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Support/helpers.php';

use WorkshopHub\Router;
use WorkshopHub\Response;
use WorkshopHub\Support\Session;
use WorkshopHub\Controllers\HomeController;
use WorkshopHub\Controllers\HealthController;
use WorkshopHub\Controllers\RegistrationController;
use WorkshopHub\Controllers\AuthController;
use WorkshopHub\Controllers\DashboardController;

// 3. Cau hinh session cookie flags (HttpOnly, SameSite=Lax, Secure theo env)
//    — BAT BUOC goi truoc session_start() (thuc hien ben trong Session::start()).
Session::start();

// 4. Khoi tao Router va Controllers.
$router = new Router();

$home         = new HomeController();
$health       = new HealthController();
$registration = new RegistrationController();
$auth         = new AuthController();
$dashboard    = new DashboardController();

// 5. Khai bao routes tap trung.
// --- Trang chu ---
$router->get('/', [$home, 'index']);

// --- Resource chinh: registration ---
$router->get('/registrations',        [$registration, 'index']);
$router->get('/registrations/create', [$registration, 'create']);
$router->post('/registrations',       [$registration, 'store']);

// --- Auth ---
$router->get('/login',   [$auth, 'loginForm']);
$router->post('/login',  [$auth, 'handleLogin']);
$router->post('/logout', [$auth, 'logout']);

// --- Khu vuc dang nhap ---
$router->get('/dashboard',    [$dashboard, 'index']);
$router->get('/session-demo', [$dashboard, 'sessionDemo']);

// --- Lam them: health check JSON ---
$router->get('/health', [$health, 'check']);

// 6. Dispatch.
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
