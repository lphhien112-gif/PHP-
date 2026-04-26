<?php

namespace EventHub\Support;

/**
 * Logger - Ghi log lỗi và thông tin hệ thống
 * EventHub - Mini Event Booking App
 */
class Logger
{
    private string $logPath;

    public function __construct(string $logPath)
    {
        $this->logPath = $logPath;

        // Tạo thư mục log nếu chưa có
        $dir = dirname($logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $timestamp   = date('Y-m-d H:i:s');
        $contextJson = empty($context) ? '' : ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        $line        = "[{$timestamp}] [{$level}] {$message}{$contextJson}" . PHP_EOL;
        file_put_contents($this->logPath, $line, FILE_APPEND | LOCK_EX);
    }
}
