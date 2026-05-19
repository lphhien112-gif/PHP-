<?php

/**
 * BookNest — Front Controller
 * 
 * Mọi request đều đi qua file này.
 * Nhiệm vụ:
 * 1. Load autoloader (PSR-4)
 * 2. Khởi tạo Router
 * 3. Khai báo tất cả routes
 * 4. Dispatch request đến controller/action phù hợp
 */

// Cho phép PHP built-in server phục vụ file tĩnh (CSS, JS, images)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Trả file tĩnh trực tiếp
}

// 1. Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use BookNest\Router;
use BookNest\Controllers\HomeController;
use BookNest\Controllers\HealthController;
use BookNest\Controllers\BookController;
use BookNest\Controllers\AuthController;

// 2. Khởi tạo Router
$router = new Router();

// 3. Khởi tạo Controllers
$homeController   = new HomeController();
$healthController = new HealthController();
$bookController   = new BookController();
$authController   = new AuthController();

// 4. Khai báo Routes
// ─── Trang chủ ───
$router->get('/', [$homeController, 'index']);

// ─── Health Check (JSON) ───
$router->get('/health', [$healthController, 'check']);

// ─── Quản lý sách ───
$router->get('/books',        [$bookController, 'index']);
$router->get('/books/create', [$bookController, 'create']);
$router->post('/books',       [$bookController, 'store']);

// ─── Đăng nhập / Đăng xuất ───
$router->get('/login',  [$authController, 'loginForm']);
$router->post('/login', [$authController, 'login']);
$router->get('/logout', [$authController, 'logout']);

// ─── Redirect demo ───
$router->get('/go-home', function () {
    \BookNest\Response::redirect('/');
});

// 5. Dispatch — xử lý request hiện tại
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path);
