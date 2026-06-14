<?php

namespace ClinicDesk\Controllers;

use ClinicDesk\Core\Database;
use ClinicDesk\Core\Response;

/**
 * HealthController - GET /health : JSON kiem tra DB.
 *
 * Tra status=ok va database=connected neu PDO chay duoc SELECT 1,
 * nguoc lai status=error va database=disconnected (van 200/503 tuy ket noi).
 */
class HealthController
{
    public function index(): void
    {
        $cfg = require __DIR__ . '/../../config/app.php';
        $connected = Database::isConnected();

        $payload = [
            'status'      => $connected ? 'ok' : 'error',
            'app'         => $cfg['name'],
            'module'      => 'Mini Clinic Appointment DB App',
            'database'    => $connected ? 'connected' : 'disconnected',
            'db_name'     => Database::config()['dbname'],
            'php_version' => PHP_VERSION,
            'timestamp'   => date('Y-m-d H:i:s'),
        ];

        Response::json($payload, $connected ? 200 : 503);
    }
}
