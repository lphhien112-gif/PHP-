<?php

namespace ClinicDesk\Core;

use PDO;
use PDOException;

/**
 * Database - Quan ly ket noi PDO (Singleton).
 *
 * - Doc config/database.php de lay host/db/user/pass/options.
 * - Tao DSN voi charset=utf8mb4.
 * - Bat ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false.
 * - Chi mo MOT ket noi duy nhat va tai su dung (Singleton).
 */
class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    /**
     * Tra ve ket noi PDO. Lan dau goi se ket noi that, cac lan sau tai su dung.
     *
     * @throws PDOException neu khong ket noi duoc (caller phai bat de log + safe message).
     */
    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = self::config();

        // DSN bao gom charset=utf8mb4 de luu duoc tieng Viet co dau + emoji.
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['host'],
            $cfg['port'],
            $cfg['dbname'],
            $cfg['charset']
        );

        self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $cfg['options']);

        return self::$pdo;
    }

    /**
     * Doc cau hinh DB (cache trong static).
     */
    public static function config(): array
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../../config/database.php';
        }
        return self::$config;
    }

    /**
     * Kiem tra nhanh DB co song khong (dung cho /health).
     * Tra true neu SELECT 1 chay duoc, false neu nem exception.
     */
    public static function isConnected(): bool
    {
        try {
            self::connection()->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            Logger::error('Health check DB that bai: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset ket noi (huu ich cho test).
     */
    public static function reset(): void
    {
        self::$pdo = null;
        self::$config = [];
    }
}
