<?php

namespace ClinicDesk\Core;

/**
 * Router - Map METHOD + PATH -> Controller@Action.
 *
 * Phan biet:
 *   - 404: khong co route nao khop path.
 *   - 405: co route khop path nhung sai method (tra kem header Allow).
 *
 * Moi handler chay trong try/catch o tang dispatch: neu nem exception
 * (vi du PDOException khi DB chet) thi tra 500 safe message va log chi tiet.
 */
class Router
{
    /** @var array<int, array{method:string, path:string, handler:callable}> */
    private array $routes = [];

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
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $requestMethod, string $requestPath): void
    {
        $requestMethod = strtoupper($requestMethod);
        $requestPath = '/' . trim($requestPath, '/');
        if ($requestPath !== '/') {
            $requestPath = rtrim($requestPath, '/');
        }

        // 1) Khop chinh xac method + path.
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                $this->runHandler($route['handler']);
                return;
            }
        }

        // 2) Path ton tai nhung sai method -> 405 + Allow.
        $allowedMethods = [];
        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath) {
                $allowedMethods[] = $route['method'];
            }
        }
        if (!empty($allowedMethods)) {
            Response::methodNotAllowed(array_values(array_unique($allowedMethods)));
            return;
        }

        // 3) Khong co path -> 404.
        Response::notFound($requestPath);
    }

    /**
     * Goi handler trong try/catch. Bat MOI exception (Throwable) de:
     *  - Ghi log chi tiet (storage/logs/app.log).
     *  - Tra 500 safe message cho user (khong lo SQLSTATE tren production).
     */
    private function runHandler(callable $handler): void
    {
        try {
            call_user_func($handler);
        } catch (\Throwable $e) {
            Logger::error(sprintf(
                '%s: %s in %s:%d',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            Response::serverError($e->getMessage());
        }
    }
}
