<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - RememberTokenRepository (F6b - remember-me an toan)
 *
 * Chua TAT CA SQL cua bang remember_tokens. KHONG doc $_POST/$_GET.
 * Luu HASH (SHA-256) cua token tho - KHONG luu token tho, KHONG luu password.
 * Co che selector:token -> tra cuu theo selector (UNIQUE) roi hash_equals hash.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class RememberTokenRepository
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

    /** Luu 1 token moi (hash da tinh o Service). */
    public function create(int $userId, string $selector, string $tokenHash, string $expiresAt): void
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
             VALUES (:uid, :sel, :hash, :exp)'
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':sel', $selector, PDO::PARAM_STR);
        $stmt->bindValue(':hash', $tokenHash, PDO::PARAM_STR);
        $stmt->bindValue(':exp', $expiresAt, PDO::PARAM_STR);
        $stmt->execute();
    }

    /** Tim 1 token con han theo selector. Tra mang hoac null. */
    public function findValidBySelector(string $selector): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT id, user_id, selector, token_hash, expires_at
             FROM remember_tokens
             WHERE selector = :sel AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->bindValue(':sel', $selector, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Xoa 1 token theo selector (rotate / revoke). */
    public function deleteBySelector(string $selector): void
    {
        $stmt = $this->db()->prepare('DELETE FROM remember_tokens WHERE selector = :sel');
        $stmt->bindValue(':sel', $selector, PDO::PARAM_STR);
        $stmt->execute();
    }

    /** Xoa TAT CA token cua mot user (revoke khi logout / doi mat khau). */
    public function deleteAllForUser(int $userId): void
    {
        $stmt = $this->db()->prepare('DELETE FROM remember_tokens WHERE user_id = :uid');
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** Don dep token het han (goi co hoi khi co dip). */
    public function purgeExpired(): void
    {
        $this->db()->query('DELETE FROM remember_tokens WHERE expires_at <= NOW()');
    }
}
