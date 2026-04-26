<?php

/**
 * EventHub - Mini Event Booking App
 * Bootstrap / Entry Point
 * 
 * ▸ XAMPP:  Truy cập http://localhost/PHP/PHP-Lab02/EventHub/public/
 * ▸ CLI:   php -S localhost:8000 -t public
 */

declare(strict_types=1);

// Autoload PSR-4
$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    // Fallback thủ công nếu chưa chạy composer
    spl_autoload_register(function (string $class): void {
        $prefix = 'EventHub\\';
        $base   = dirname(__DIR__) . '/src/';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = substr($class, strlen($prefix));
        $file     = $base . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
} else {
    require $autoloadPath;
}

// Load config
$config = require dirname(__DIR__) . '/config/app.php';

use EventHub\Support\Logger;
use EventHub\Controllers\EventController;
use EventHub\Controllers\HomeController;
use EventHub\Helpers\ResponseHelper;

// Khởi tạo logger
$logger = new Logger($config['log']['path']);

// ============================================================
//  XỬ LÝ ĐƯỜNG DẪN — Tương thích XAMPP + PHP built-in server
// ============================================================

// Tính basePath tự động từ SCRIPT_NAME
// Ví dụ SCRIPT_NAME = /PHP/PHP-Lab02/EventHub/public/index.php
//  → basePath = /PHP/PHP-Lab02/EventHub/public
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Lấy URI gốc
$fullUri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Loại bỏ basePath để lấy route thực
if ($basePath !== '' && str_starts_with($fullUri, $basePath)) {
    $requestUri = substr($fullUri, strlen($basePath));
} else {
    $requestUri = $fullUri;
}
$requestUri    = rtrim($requestUri, '/') ?: '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Lưu basePath vào biến global để views/controllers dùng tạo URL
define('BASE_URL', $basePath);

// Log mỗi request đến
$logger->info("Incoming request: {$requestMethod} {$requestUri}", [
    'ip'           => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'none',
    'raw_uri'      => $fullUri,
    'base_path'    => $basePath,
]);

// ============================================================
//  ROUTER
// ============================================================

// Trang chủ
if ($requestUri === '/') {
    (new HomeController())->index();
}

// GET /events hoặc POST /events (sai method)
// GET /events?category=...&status=...
if ($requestUri === '/events') {
    (new EventController($logger))->index();
}

// GET /events/{id}
if (preg_match('#^/events/(\d+)$#', $requestUri, $matches)) {
    (new EventController($logger))->show((int) $matches[1]);
}

// POST /bookings - Đặt vé
if ($requestUri === '/bookings') {
    (new EventController($logger))->book();
}

// 404 - Không tìm thấy đường dẫn
$logger->warning("404 - Không tìm thấy: {$requestMethod} {$requestUri}");
ResponseHelper::error("Đường dẫn '{$requestUri}' không tồn tại trên server.", 404, [
    'available_endpoints' => [
        'GET  /'           => 'Trang chủ EventHub',
        'GET  /events'     => 'Danh sách sự kiện',
        'GET  /events/{id}' => 'Chi tiết sự kiện',
        'POST /bookings'   => 'Đặt vé sự kiện',
    ],
]);
