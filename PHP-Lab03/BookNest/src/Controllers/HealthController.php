<?php

namespace BookNest\Controllers;

use BookNest\Response;

/**
 * HealthController
 * 
 * Trả về JSON health check cho hệ thống
 */
class HealthController
{
    /**
     * GET /health → JSON health check (200 OK)
     */
    public function check(): void
    {
        $data = [
            'status'      => 'OK',
            'application' => 'BookNest — Mini Bookstore Routing App',
            'version'     => '1.0.0',
            'php_version' => phpversion(),
            'timestamp'   => date('Y-m-d H:i:s'),
            'uptime'      => 'running',
        ];

        Response::json($data, 200);
    }
}
