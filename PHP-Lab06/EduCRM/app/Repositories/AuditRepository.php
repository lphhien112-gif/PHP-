<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - AuditRepository (F3 - Nhat ky hoat dong)
 *
 * Chua TAT CA SQL cua bang activity_logs. KHONG doc $_POST/$_GET.
 * Prepared statement cho moi tham so. Whitelist filter ap o tang Service.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class AuditRepository
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

    /** Ghi 1 dong nhat ky. */
    public function insert(
        ?int $userId,
        string $action,
        ?string $entity,
        ?int $entityId,
        ?string $summary,
        ?string $ip
    ): void {
        $sql = 'INSERT INTO activity_logs (user_id, action, entity, entity_id, summary, ip)
                VALUES (:user_id, :action, :entity, :entity_id, :summary, :ip)';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':action', $action, PDO::PARAM_STR);
        $stmt->bindValue(':entity', $entity, $entity === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':entity_id', $entityId, $entityId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':summary', $summary, $summary === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, $ip === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Dung WHERE dong (van prepared/bound) cho filter user/action/entity.
     */
    private function buildWhere(array $f): array
    {
        $clauses = [];
        $params  = [];

        if (!empty($f['user_id'])) {
            $clauses[] = 'a.user_id = :user_id';
            $params[':user_id'] = (int) $f['user_id'];
        }
        if (!empty($f['action'])) {
            $clauses[] = 'a.action = :action';
            $params[':action'] = (string) $f['action'];
        }
        if (!empty($f['entity'])) {
            $clauses[] = 'a.entity = :entity';
            $params[':entity'] = (string) $f['entity'];
        }

        $where = $clauses ? ('WHERE ' . implode(' AND ', $clauses)) : '';
        return [$where, $params];
    }

    public function countFiltered(array $filters): int
    {
        [$where, $params] = $this->buildWhere($filters);
        $sql = 'SELECT COUNT(*) FROM activity_logs a ' . $where;
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function paginate(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $sql = "SELECT a.id, a.user_id, a.action, a.entity, a.entity_id,
                       a.summary, a.ip, a.created_at, u.username
                FROM activity_logs a
                LEFT JOIN users u ON u.id = a.user_id
                $where
                ORDER BY a.created_at DESC, a.id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Danh sach user (id+username) cho dropdown filter. */
    public function userOptions(): array
    {
        return $this->db()->query(
            'SELECT id, username FROM users ORDER BY username ASC'
        )->fetchAll();
    }
}
