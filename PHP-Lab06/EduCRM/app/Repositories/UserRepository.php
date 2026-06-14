<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - UserRepository
 *
 * Chua TAT CA SQL lien quan toi bang users. KHONG doc $_POST / $_GET.
 * Chi nhan tham so qua method va dung prepared statement.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class UserRepository
{
    private ?PDO $db;

    public function __construct(?PDO $db = null)
    {
        // Lazy: KHONG ket noi DB ngay trong constructor. Ket noi chi mo khi
        // co query that su -> loi DB se nem ra trong qua trinh dispatch
        // (duoc try/catch o Front Controller bao boc an toan).
        $this->db = $db;
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    /**
     * Tim user theo username (cho login). Tra mang hoac null.
     */
    public function findByUsername(string $username): ?array
    {
        $sql = 'SELECT id, username, password_hash, full_name, role
                FROM users
                WHERE username = :username
                LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Tim user theo id (F6: remember-me auto-login). Tra mang hoac null.
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, username, full_name, role FROM users WHERE id = :id LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
