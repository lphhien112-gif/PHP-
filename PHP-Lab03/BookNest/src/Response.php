<?php

namespace BookNest;

/**
 * Response Helper Class
 * 
 * Chuẩn hoá mọi response: HTML, JSON, Redirect, 404, 405
 * Đảm bảo đúng Content-Type, status code, và headers
 */
class Response
{
    /**
     * Trả về response HTML (200 OK)
     * Content-Type: text/html; charset=UTF-8
     */
    public static function html(string $content, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');
        echo $content;
    }

    /**
     * Trả về response JSON (200 OK hoặc custom status)
     * Content-Type: application/json; charset=UTF-8
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Redirect (302 Found)
     * Header Location chỉ đến URL đích
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
    }

    /**
     * 404 Not Found
     * Trả HTML với thông báo lỗi
     */
    public static function notFound(string $path = ''): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=UTF-8');

        $content = self::renderView('errors/404', ['path' => $path]);
        echo $content;
    }

    /**
     * 405 Method Not Allowed
     * Trả HTML + header Allow liệt kê method hợp lệ
     */
    public static function methodNotAllowed(array $allowedMethods = []): void
    {
        http_response_code(405);
        header('Content-Type: text/html; charset=UTF-8');

        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }

        $content = self::renderView('errors/405', [
            'allowedMethods' => $allowedMethods,
        ]);
        echo $content;
    }

    /**
     * Render view file và trả về nội dung dưới dạng string
     * Dùng output buffering để capture HTML
     */
    public static function renderView(string $viewName, array $data = []): string
    {
        // Extract data thành biến riêng lẻ (VD: $data['title'] → $title)
        extract($data);

        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';

        if (!file_exists($viewPath)) {
            return '<h1>View not found: ' . htmlspecialchars($viewName) . '</h1>';
        }

        ob_start();
        require $viewPath;
        return ob_get_clean();
    }
}
