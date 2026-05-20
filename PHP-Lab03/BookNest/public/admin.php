<?php
/**
 * BookNest — Admin Front Controller
 * Server 2: php -S localhost:8001 -t public public/admin.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

require_once __DIR__ . '/../vendor/autoload.php';

use BookNest\Router;
use BookNest\Controllers\AdminController;
use BookNest\Controllers\BookController;
use BookNest\Controllers\HealthController;
use BookNest\Controllers\AuthController;

$router = new Router();

$adminController  = new AdminController();
$bookController   = new BookController();
$healthController = new HealthController();
$authController   = new AuthController();

// ─── Admin Dashboard ───
$router->get('/', [$adminController, 'dashboard']);

// ─── Admin Book Management ───
$router->get('/books',        [$adminController, 'books']);
$router->get('/books/create', [$adminController, 'createBook']);
$router->post('/books',       [$adminController, 'storeBook']);

// ─── Health Check (JSON) ───
$router->get('/health', [$healthController, 'check']);

// ─── Admin Auth ───
$router->get('/login',  [$adminController, 'loginForm']);
$router->post('/login', [$adminController, 'login']);
$router->get('/logout', [$adminController, 'logout']);

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($method, $path);
