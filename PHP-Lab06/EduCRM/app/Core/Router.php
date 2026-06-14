<?php

namespace App\Core;

/**
 * EduCRM - Router
 *
 * Map METHOD + PATH -> Controller@Action (callable).
 * Phan biet ro:
 *   - 404 Not Found        : path khong ton tai
 *   - 405 Method Not Allowed : path ton tai nhung sai method (+ header Allow)
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class Router
{
    /** @var array<int,array{method:string,path:string,handler:callable}> */
    private array $routes = [];

    /** Handler cho 404 (callable) */
    private $notFoundHandler = null;

    /** Handler cho 405 (callable(array $allowed)) */
    private $methodNotAllowedHandler = null;

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $this->normalize($path),
            'handler' => $handler,
        ];
    }

    public function setNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function setMethodNotAllowedHandler(callable $handler): void
    {
        $this->methodNotAllowedHandler = $handler;
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    /**
     * Dispatch request den handler phu hop.
     */
    public function dispatch(string $requestMethod, string $requestPath): void
    {
        $requestMethod = strtoupper($requestMethod);
        $requestPath   = $this->normalize($requestPath);

        // Buoc 1: khop chinh xac METHOD + PATH
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                call_user_func($route['handler']);
                return;
            }
        }

        // Buoc 2: path ton tai nhung khac method -> 405
        $allowed = [];
        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath) {
                $allowed[] = $route['method'];
            }
        }
        if (!empty($allowed)) {
            $allowed = array_values(array_unique($allowed));
            header('Allow: ' . implode(', ', $allowed));
            if ($this->methodNotAllowedHandler) {
                call_user_func($this->methodNotAllowedHandler, $allowed);
            }
            return;
        }

        // Buoc 3: path khong ton tai -> 404
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        }
    }
}
