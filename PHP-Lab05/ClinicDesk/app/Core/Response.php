<?php

namespace ClinicDesk\Core;

/**
 * Response - Chuan hoa moi response: HTML, JSON, Redirect, 404, 405, 500.
 * Dam bao dung Content-Type, status code va headers.
 */
class Response
{
    /** HTML response (mac dinh 200). */
    public static function html(string $content, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');
        echo $content;
    }

    /** JSON response. */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /** Redirect (PRG pattern) - mac dinh 302 Found + Location header. */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
    }

    /** 404 Not Found. */
    public static function notFound(string $path = ''): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=UTF-8');
        echo View::render('errors/404', ['path' => $path]);
    }

    /** 405 Method Not Allowed + header Allow. */
    public static function methodNotAllowed(array $allowedMethods = []): void
    {
        http_response_code(405);
        header('Content-Type: text/html; charset=UTF-8');
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        echo View::render('errors/405', ['allowedMethods' => $allowedMethods]);
    }

    /**
     * 500 Internal Server Error.
     * Production: chi hien safe message, KHONG lo SQLSTATE.
     * Debug: hien them chi tiet ky thuat de phat trien.
     */
    public static function serverError(string $detail = ''): void
    {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        $cfg = require __DIR__ . '/../../config/app.php';
        echo View::render('errors/500', [
            'debug'  => (bool)($cfg['debug'] ?? false),
            'detail' => $detail,
        ]);
    }
}
