<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - ApiTokenRepository (F7 - JSON API bearer token)
 *
 * Chua TAT CA SQL cua bang api_tokens. KHONG doc $_POST/$_GET/header.
 * Luu HASH (SHA-256) cua token; so khop bang hash de tranh luu token tho.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class ApiTokenRepository
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

    /** Co token nao khop hash nay khong? (token hop le). */
    public function existsByHash(string $tokenHash): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT 1 FROM api_tokens WHERE token_hash = :h LIMIT 1'
        );
        $stmt->bindValue(':h', $tokenHash, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }
}
