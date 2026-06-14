<?php

namespace WorkshopHub;

/**
 * WorkshopHub — Router
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * Map (HTTP method + path) -> Controller@Action.
 * Phan biet ro rang:
 *   - 404 Not Found        : path khong ton tai trong bang route.
 *   - 405 Method Not Allowed: path ton tai nhung sai method (+ header Allow).
 */
class Router
{
    /**
     * Danh sach route da dang ky.
     * Format: ['method' => 'GET', 'path' => '/registrations', 'handler' => callable]
     *
     * @var array<int, array{method:string, path:string, handler:callable}>
     */
    private array $routes = [];

    /** Dang ky route GET. */
    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /** Dang ky route POST. */
    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /** So route da dang ky (phuc vu health/debug). */
    public function count(): int
    {
        return count($this->routes);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $this->normalize($path),
            'handler' => $handler,
        ];
    }

    /** Chuan hoa path: bo dau / thua o cuoi, luon bat dau bang /. */
    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        return $path;
    }

    /**
     * Dispatch request den handler phu hop.
     *
     * Buoc 1: khop ca method lan path  -> goi handler.
     * Buoc 2: khop path, sai method     -> 405 + header Allow.
     * Buoc 3: khong khop path nao        -> 404.
     */
    public function dispatch(string $requestMethod, string $requestPath): void
    {
        $requestMethod = strtoupper($requestMethod);
        $requestPath   = $this->normalize($requestPath);

        // Buoc 1: khop chinh xac method + path.
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                call_user_func($route['handler']);
                return;
            }
        }

        // Buoc 2: path ton tai nhung method khong hop le -> 405.
        $allowed = [];
        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath) {
                $allowed[] = $route['method'];
            }
        }
        if (!empty($allowed)) {
            Response::methodNotAllowed(array_values(array_unique($allowed)));
            return;
        }

        // Buoc 3: path khong ton tai -> 404.
        Response::notFound($requestPath);
    }
}
