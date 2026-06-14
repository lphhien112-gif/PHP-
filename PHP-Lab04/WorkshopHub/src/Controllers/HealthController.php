<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;
use WorkshopHub\Support\Storage;

/**
 * WorkshopHub — HealthController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * ------------------------------------------------------------------
 * GET /health : tra JSON kiem tra suc khoe he thong (lam them).
 */
class HealthController
{
    public function check(): void
    {
        Response::json([
            'status'        => 'ok',
            'app'           => 'WorkshopHub',
            'version'       => '1.0.0',
            'php_version'   => PHP_VERSION,
            'timestamp'     => date('Y-m-d H:i:s'),
            'registrations' => (new Storage('registrations.json'))->count(),
        ]);
    }
}
