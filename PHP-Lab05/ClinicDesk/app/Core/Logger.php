<?php

namespace ClinicDesk\Core;

/**
 * Logger - Ghi log loi vao storage/logs/app.log.
 *
 * Quan trong: tren production khong hien thi SQLSTATE/loi ky thuat cho user.
 * Thay vao do log chi tiet (message + trace) ra file, con user chi thay safe message.
 */
class Logger
{
    private static ?string $path = null;

    private static function path(): string
    {
        if (self::$path === null) {
            $cfg = require __DIR__ . '/../../config/app.php';
            self::$path = $cfg['log_path'];
        }
        return self::$path;
    }

    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    private static function write(string $level, string $message): void
    {
        $dir = dirname(self::path());
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $line = sprintf("[%s] %s: %s%s", date('Y-m-d H:i:s'), $level, $message, PHP_EOL);
        @file_put_contents(self::path(), $line, FILE_APPEND | LOCK_EX);
    }
}
