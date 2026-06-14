<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * EduCRM - Database (PDO Singleton)
 *
 * Tao ket noi PDO chuan an toan:
 *  - charset = utf8mb4 (luu tieng Viet / emoji)
 *  - ERRMODE_EXCEPTION  (loi nem exception, khong return false am tham)
 *  - FETCH_ASSOC        (lay ket qua dang mang ket hop)
 *  - EMULATE_PREPARES = false (dung prepared statement that o tang MySQL,
 *                              chong SQL Injection trie de)
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Lay (hoac tao moi) ket noi PDO dung chung toan ung dung.
     */
    public static function connection(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $config = require __DIR__ . '/../../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // De exception PDO lan ra ngoai cho Front Controller xu ly an toan.
        self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);

        return self::$instance;
    }

    /**
     * Dung trong /health: kiem tra DB con song khong, tra bool.
     */
    public static function ping(): bool
    {
        try {
            self::connection()->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
