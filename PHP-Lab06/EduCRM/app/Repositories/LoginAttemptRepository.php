<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - LoginAttemptRepository (F6a - lockout dang nhap)
 *
 * Chua TAT CA SQL cua bang login_attempts. KHONG doc $_POST/$_GET.
 * Service truyen username + ip + cua so thoi gian -> dem so lan sai gan day.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class LoginAttemptRepository
{
    private ?PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db;
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    /** Ghi 1 lan dang nhap sai (theo username + IP). */
    public function record(string $username, string $ip): void
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO login_attempts (username, ip) VALUES (:u, :ip)'
        );
        $stmt->bindValue(':u', $username, PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Dem so lan sai cua username+IP trong $windowSeconds giay gan day.
     */
    public function countRecent(string $username, string $ip, int $windowSeconds): int
    {
        $stmt = $this->db()->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE username = :u AND ip = :ip
               AND attempted_at > (NOW() - INTERVAL :win SECOND)'
        );
        $stmt->bindValue(':u', $username, PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':win', $windowSeconds, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Thoi diem lan sai dau tien trong chuoi gan day (epoch) de tinh thoi
     * gian con lai cua lockout. Tra null neu khong co.
     */
    public function firstAttemptAt(string $username, string $ip, int $windowSeconds): ?int
    {
        $stmt = $this->db()->prepare(
            'SELECT UNIX_TIMESTAMP(MIN(attempted_at)) FROM login_attempts
             WHERE username = :u AND ip = :ip
               AND attempted_at > (NOW() - INTERVAL :win SECOND)'
        );
        $stmt->bindValue(':u', $username, PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':win', $windowSeconds, PDO::PARAM_INT);
        $stmt->execute();
        $v = $stmt->fetchColumn();
        return $v !== null && $v !== false ? (int) $v : null;
    }

    /** Xoa cac lan sai cua username+IP (sau khi dang nhap thanh cong). */
    public function clear(string $username, string $ip): void
    {
        $stmt = $this->db()->prepare(
            'DELETE FROM login_attempts WHERE username = :u AND ip = :ip'
        );
        $stmt->bindValue(':u', $username, PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->execute();
    }
}
