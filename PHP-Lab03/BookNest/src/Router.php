<?php

namespace BookNest;

/**
 * Router Class
 * 
 * Map URL + HTTP method → Controller@Action
 * Hỗ trợ phân biệt 404 (path không tồn tại) và 405 (path tồn tại, sai method)
 */
class Router
{
    /**
     * Danh sách routes đã đăng ký
     * Format: [ ['method' => 'GET', 'path' => '/books', 'handler' => callable], ... ]
     */
    private array $routes = [];

    /**
     * Đăng ký route GET
     */
    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Đăng ký route POST
     */
    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Thêm route vào danh sách
     */
    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch request đến handler phù hợp
     * 
     * Logic:
     * 1. Tìm route khớp cả method + path → gọi handler
     * 2. Tìm route khớp path nhưng khác method → trả 405 + header Allow
     * 3. Không tìm thấy path nào → trả 404
     */
    public function dispatch(string $requestMethod, string $requestPath): void
    {
        $requestMethod = strtoupper($requestMethod);
        $requestPath = '/' . trim($requestPath, '/');
        if ($requestPath !== '/') {
            $requestPath = rtrim($requestPath, '/');
        }

        // Bước 1: Tìm route khớp chính xác method + path
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                call_user_func($route['handler']);
                return;
            }
        }

        // Bước 2: Tìm route khớp path nhưng khác method → 405
        $allowedMethods = [];
        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath) {
                $allowedMethods[] = $route['method'];
            }
        }

        if (!empty($allowedMethods)) {
            // Path tồn tại nhưng method không hợp lệ
            Response::methodNotAllowed($allowedMethods);
            return;
        }

        // Bước 3: Path không tồn tại → 404
        Response::notFound($requestPath);
    }
}
