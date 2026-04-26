<?php

namespace EventHub\Helpers;

/**
 * ResponseHelper - Trả về HTTP response chuẩn
 * EventHub - Mini Event Booking App
 */
class ResponseHelper
{
    /**
     * Gửi JSON response với status code
     */
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Gửi HTML response
     */
    public static function html(string $content, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');
        echo $content;
        exit;
    }

    /**
     * Trả về lỗi JSON chuẩn
     */
    public static function error(string $message, int $statusCode, array $extra = []): void
    {
        $body = array_merge([
            'success' => false,
            'error'   => [
                'code'    => $statusCode,
                'message' => $message,
            ],
        ], $extra);
        self::json($body, $statusCode);
    }

    /**
     * Trả về response thành công
     */
    public static function success(mixed $data, string $message = 'OK', int $statusCode = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * HEAD response - không có body
     */
    public static function head(int $statusCode = 200, array $headers = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }
        exit;
    }

    /**
     * OPTIONS response - liệt kê methods được phép
     */
    public static function options(array $allowedMethods): void
    {
        http_response_code(204);
        header('Allow: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        exit;
    }
}
