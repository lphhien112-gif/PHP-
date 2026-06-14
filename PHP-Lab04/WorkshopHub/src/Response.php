<?php

namespace WorkshopHub;

/**
 * WorkshopHub — Response Helper
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * Chuan hoa moi response: HTML, JSON, Redirect (PRG), 404, 405.
 * Dam bao dung Content-Type, status code va headers.
 */
class Response
{
    /** HTML response. Content-Type: text/html; charset=UTF-8. */
    public static function html(string $content, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');
        echo $content;
    }

    /** JSON response. Content-Type: application/json; charset=UTF-8. */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Redirect (mac dinh 303 See Other — chuan cho PRG sau POST).
     * Trinh duyet se thuc hien GET den $url, tranh re-submit khi refresh.
     */
    public static function redirect(string $url, int $statusCode = 303): void
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
    }

    /** 404 Not Found — render views/errors/404.php. */
    public static function notFound(string $path = ''): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=UTF-8');
        echo self::renderView('errors/404', ['path' => $path]);
    }

    /** 405 Method Not Allowed — them header Allow + render errors/405.php. */
    public static function methodNotAllowed(array $allowedMethods = []): void
    {
        http_response_code(405);
        header('Content-Type: text/html; charset=UTF-8');
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        echo self::renderView('errors/405', ['allowedMethods' => $allowedMethods]);
    }

    /**
     * Render mot view rieng le, tra ve string (dung output buffering).
     */
    public static function renderView(string $viewName, array $data = []): string
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        if (!file_exists($viewPath)) {
            return '<h1>View not found: ' . htmlspecialchars($viewName) . '</h1>';
        }
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    /**
     * Render view noi dung roi bao vao layout chung.
     * $data['title'] dung cho the <title>.
     */
    public static function view(string $viewName, array $data = [], int $statusCode = 200): void
    {
        $content = self::renderView($viewName, $data);
        $page = self::renderView('layout', array_merge($data, ['content' => $content]));
        self::html($page, $statusCode);
    }
}
