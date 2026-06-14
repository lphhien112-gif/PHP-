<?php

namespace App\Controllers;

use App\Core\Database;

/**
 * EduCRM - HealthController
 *
 * GET /health -> tra JSON trang thai app + database. Dung de monitoring.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class HealthController
{
    public function index(): void
    {
        $dbOk = Database::ping();

        $payload = [
            'status'      => $dbOk ? 'ok' : 'degraded',
            'app'         => config('name', 'EduCRM'),
            'domain'      => config('tagline', 'Training Center CRM'),
            'database'    => $dbOk ? 'up' : 'down',
            'php_version' => PHP_VERSION,
            'timestamp'   => date('c'),
        ];

        http_response_code($dbOk ? 200 : 503);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
