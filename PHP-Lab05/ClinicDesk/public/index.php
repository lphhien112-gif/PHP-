<?php

/**
 * ClinicDesk - Mini Clinic Appointment DB App (PHP Lab05)
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 * Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
 * Truong Dai hoc Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc
 * GitHub: https://github.com/lphhien112-gif/PHP-
 *
 * public/index.php - Front Controller (entry point duy nhat).
 * Browser -> index.php -> Router -> Controller -> Repository -> PDO -> MySQL -> View/Redirect.
 */

declare(strict_types=1);

// Cho PHP built-in server phuc vu file tinh (CSS) truc tiep.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// 1) Autoloader PSR-4 (vendor da chinh map ClinicDesk\ -> app/).
require_once __DIR__ . '/../vendor/autoload.php';

use ClinicDesk\Core\Router;
use ClinicDesk\Core\Response;
use ClinicDesk\Controllers\HomeController;
use ClinicDesk\Controllers\HealthController;
use ClinicDesk\Controllers\PatientController;
use ClinicDesk\Controllers\AppointmentController;

// 2) Cau hinh chung (timezone + error display theo APP_DEBUG).
$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);
ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
error_reporting(E_ALL);

// 3) Khoi tao Router va Controllers.
$router = new Router();
$home        = new HomeController();
$health      = new HealthController();
$patient     = new PatientController();
$appointment = new AppointmentController();

// 4) Khai bao Routes.
// --- Trang chu + health ---
$router->get('/',        [$home, 'index']);
$router->get('/health',  [$health, 'index']);

// --- Module A: patients ---
$router->get('/patients',         [$patient, 'index']);
$router->get('/patients/create',  [$patient, 'create']);
$router->post('/patients/store',  [$patient, 'store']);
$router->get('/patients/edit',    [$patient, 'edit']);
$router->post('/patients/update', [$patient, 'update']);
$router->post('/patients/delete', [$patient, 'delete']);

// --- Module B: appointments ---
$router->get('/appointments',         [$appointment, 'index']);
$router->get('/appointments/create',  [$appointment, 'create']);
$router->post('/appointments/store',  [$appointment, 'store']);
$router->get('/appointments/edit',    [$appointment, 'edit']);
$router->post('/appointments/update', [$appointment, 'update']);
$router->post('/appointments/delete', [$appointment, 'delete']);

// 5) Dispatch request hien tai.
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($method, $path);
