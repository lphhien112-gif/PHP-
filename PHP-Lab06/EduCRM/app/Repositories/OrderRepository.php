<?php

namespace App\Repositories;

use App\Core\Database;
use App\Core\DuplicateRecordException;
use PDO;
use PDOException;

/**
 * EduCRM - OrderRepository (Module B)
 *
 * Chua TAT CA SQL cua bang orders. KHONG doc $_POST / $_GET.
 * Demo transaction o create() (order la mot don vi nghiep vu).
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class OrderRepository
{
    private ?PDO $db;

    /** Whitelist cot sort - chong injection qua ?sort= */
    private const SORTABLE = ['id', 'order_code', 'course', 'amount', 'status', 'created_at'];

    public function __construct(?PDO $db = null)
    {
        // Lazy connect: chi mo ket noi khi co query (loi DB nem trong dispatch).
        $this->db = $db;
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    public function countFiltered(string $q, string $status, array $dates = []): int
    {
        [$where, $params] = $this->buildWhere($q, $status, $dates);
        // JOIN leads vi WHERE co the loc theo l.full_name (tim theo ten hoc vien).
        $sql = 'SELECT COUNT(*) FROM orders o JOIN leads l ON l.id = o.lead_id ' . $where;
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function paginate(
        string $q,
        string $status,
        string $sort,
        string $dir,
        int $limit,
        int $offset,
        array $dates = []
    ): array {
        $sort = in_array($sort, self::SORTABLE, true) ? $sort : 'created_at';
        $dir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        [$where, $params] = $this->buildWhere($q, $status, $dates);

        // JOIN leads de hien ten hoc vien gan voi phieu.
        $sql = "SELECT o.id, o.order_code, o.lead_id, o.course, o.amount,
                       o.status, o.paid_at, o.note, o.created_at,
                       l.full_name AS lead_name
                FROM orders o
                JOIN leads l ON l.id = o.lead_id
                $where
                ORDER BY o.$sort $dir
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function buildWhere(string $q, string $status, array $dates = []): array
    {
        // F2: mac dinh chi lay phieu CHUA xoa mem.
        $clauses = ['o.deleted_at IS NULL'];
        $params  = [];

        if ($q !== '') {
            // Moi cot mot placeholder rieng (native prepares khong reuse :q duoc).
            $like = '%' . $q . '%';
            $clauses[] = '(o.order_code LIKE :q_code OR o.course LIKE :q_course OR l.full_name LIKE :q_name)';
            $params[':q_code']   = $like;
            $params[':q_course'] = $like;
            $params[':q_name']   = $like;
        }
        if ($status !== '') {
            $clauses[] = 'o.status = :status';
            $params[':status'] = $status;
        }
        // F8: loc theo khoang ngay tao phieu, van bound.
        $from = (string) ($dates['created_from'] ?? '');
        $to   = (string) ($dates['created_to'] ?? '');
        if ($from !== '') {
            $clauses[] = 'o.created_at >= :created_from';
            $params[':created_from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $clauses[] = 'o.created_at <= :created_to';
            $params[':created_to'] = $to . ' 23:59:59';
        }

        $where = 'WHERE ' . implode(' AND ', $clauses);
        return [$where, $params];
    }

    public function find(int $id): ?array
    {
        // F2: chi tim phieu con song.
        $stmt = $this->db()->prepare(
            'SELECT o.*, l.full_name AS lead_name
             FROM orders o JOIN leads l ON l.id = o.lead_id
             WHERE o.id = :id AND o.deleted_at IS NULL LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * F10: lay phieu + thong tin hoc vien day du de in hoa don.
     */
    public function findForInvoice(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT o.id, o.order_code, o.course, o.amount, o.status, o.paid_at,
                    o.note, o.created_at,
                    l.full_name AS lead_name, l.email AS lead_email,
                    l.phone AS lead_phone
             FROM orders o JOIN leads l ON l.id = o.lead_id
             WHERE o.id = :id AND o.deleted_at IS NULL LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Tao order moi trong TRANSACTION. Bat trung order_code -> Duplicate.
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note)
                VALUES (:order_code, :lead_id, :course, :amount, :status, :paid_at, :note)';
        try {
            $this->db()->beginTransaction();
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':order_code' => $data['order_code'],
                ':lead_id'    => $data['lead_id'],
                ':course'     => $data['course'],
                ':amount'     => $data['amount'],
                ':status'     => $data['status'],
                ':paid_at'    => $data['paid_at'] ?: null,
                ':note'       => $data['note'],
            ]);
            $id = (int) $this->db()->lastInsertId();
            $this->db()->commit();
            return $id;
        } catch (PDOException $e) {
            if ($this->db()->inTransaction()) {
                $this->db()->rollBack();
            }
            if ($this->isDuplicate($e)) {
                throw $this->duplicateCodeException((string) $data['order_code']);
            }
            throw $e;
        }
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE orders
                SET order_code = :order_code, lead_id = :lead_id, course = :course,
                    amount = :amount, status = :status, paid_at = :paid_at, note = :note
                WHERE id = :id';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':order_code' => $data['order_code'],
                ':lead_id'    => $data['lead_id'],
                ':course'     => $data['course'],
                ':amount'     => $data['amount'],
                ':status'     => $data['status'],
                ':paid_at'    => $data['paid_at'] ?: null,
                ':note'       => $data['note'],
                ':id'         => $id,
            ]);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateCodeException((string) $data['order_code']);
            }
            throw $e;
        }
    }

    /**
     * F1: INSERT order KHONG mo transaction rieng (de tham gia transaction
     * do LeadService::convertToOrder() quan ly). Bat trung order_code -> Duplicate.
     */
    public function insert(array $data): int
    {
        $sql = 'INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note)
                VALUES (:order_code, :lead_id, :course, :amount, :status, :paid_at, :note)';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':order_code' => $data['order_code'],
                ':lead_id'    => $data['lead_id'],
                ':course'     => $data['course'],
                ':amount'     => $data['amount'],
                ':status'     => $data['status'],
                ':paid_at'    => $data['paid_at'] ?: null,
                ':note'       => $data['note'],
            ]);
            return (int) $this->db()->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateCodeException((string) $data['order_code']);
            }
            throw $e;
        }
    }

    /**
     * M1: thong bao trung ma phieu phu hop voi soft-delete.
     */
    private function duplicateCodeException(string $code): DuplicateRecordException
    {
        if ($code !== '' && $this->softDeletedHasCode($code)) {
            return new DuplicateRecordException(
                'order_code',
                'Ma phieu nay dang nam trong thung rac - hay khoi phuc thay vi tao moi.'
            );
        }
        return new DuplicateRecordException('order_code', 'Ma phieu nay da ton tai.');
    }

    /** M1: co phieu DA XOA MEM dang giu ma nay khong? */
    public function softDeletedHasCode(string $code): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT 1 FROM orders WHERE order_code = :c AND deleted_at IS NOT NULL LIMIT 1'
        );
        $stmt->bindValue(':c', $code, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    /** F2: xoa MEM phieu. */
    public function softDelete(int $id): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE orders SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** F2: khoi phuc phieu da xoa mem. */
    public function restore(int $id): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE orders SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** F2: xoa VINH VIEN (chi admin). */
    public function forceDelete(int $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * F9: xoa MEM hang loat theo danh sach id. Placeholder dong (bound). Tra so dong.
     */
    public function bulkSoftDelete(array $ids): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
        if (!$ids) {
            return 0;
        }
        $place = [];
        foreach ($ids as $i => $id) {
            $place[] = ':id' . $i;
        }
        $sql = 'UPDATE orders SET deleted_at = NOW()
                WHERE deleted_at IS NULL AND id IN (' . implode(',', $place) . ')';
        $stmt = $this->db()->prepare($sql);
        foreach ($ids as $i => $id) {
            $stmt->bindValue(':id' . $i, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** F9: doi trang thai phieu hang loat (status da whitelist o Service). */
    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
        if (!$ids) {
            return 0;
        }
        $place = [];
        foreach ($ids as $i => $id) {
            $place[] = ':id' . $i;
        }
        $sql = 'UPDATE orders SET status = :status
                WHERE deleted_at IS NULL AND id IN (' . implode(',', $place) . ')';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        foreach ($ids as $i => $id) {
            $stmt->bindValue(':id' . $i, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** Tim phieu ke ca da xoa mem (cho trash / restore / force-delete). */
    public function findWithTrashed(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** F2: danh sach phieu trong thung rac. */
    public function trash(): array
    {
        return $this->db()->query(
            'SELECT o.id, o.order_code, o.course, o.amount, o.status, o.deleted_at,
                    l.full_name AS lead_name
             FROM orders o JOIN leads l ON l.id = o.lead_id
             WHERE o.deleted_at IS NOT NULL ORDER BY o.deleted_at DESC'
        )->fetchAll();
    }

    /** F4: toan bo phieu khop filter (khong phan trang) de stream CSV. */
    public function exportFiltered(string $q, string $status, array $dates = []): array
    {
        [$where, $params] = $this->buildWhere($q, $status, $dates);
        $sql = "SELECT o.id, o.order_code, l.full_name AS lead_name, o.course,
                       o.amount, o.status, o.paid_at, o.note, o.created_at
                FROM orders o JOIN leads l ON l.id = o.lead_id
                $where ORDER BY o.created_at DESC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function existsCode(string $code): bool
    {
        $stmt = $this->db()->prepare('SELECT 1 FROM orders WHERE order_code = :c LIMIT 1');
        $stmt->bindValue(':c', $code, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function total(): int
    {
        return (int) $this->db()->query(
            'SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL'
        )->fetchColumn();
    }

    /** Tong doanh thu (don da thanh toan, chua xoa mem). */
    public function paidRevenue(): float
    {
        return (float) $this->db()->query(
            "SELECT COALESCE(SUM(amount),0) FROM orders WHERE status = 'paid' AND deleted_at IS NULL"
        )->fetchColumn();
    }

    public function countByStatus(): array
    {
        $rows = $this->db()->query(
            'SELECT status, COUNT(*) AS total FROM orders WHERE deleted_at IS NULL GROUP BY status'
        )->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['total'];
        }
        return $out;
    }

    private function isDuplicate(PDOException $e): bool
    {
        return $e->getCode() === '23000'
            || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
    }
}
