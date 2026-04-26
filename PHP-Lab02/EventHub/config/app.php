<?php

/**
 * Config - Đọc biến môi trường từ file .env
 * EventHub - Mini Event Booking App
 */

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env từ thư mục gốc của project
loadEnv(dirname(__DIR__) . '/.env');

/**
 * Lấy giá trị biến môi trường
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

return [
    'app' => [
        'name'  => env('APP_NAME', 'EventHub'),
        'env'   => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url'   => env('APP_URL', 'http://localhost:8000'),
    ],
    'log' => [
        'channel' => env('LOG_CHANNEL', 'file'),
        'path'    => dirname(__DIR__) . '/' . env('LOG_PATH', 'storage/logs/app.log'),
    ],
];
