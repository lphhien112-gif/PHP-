<?php

namespace App\Repositories;

use App\Core\Database;
use App\Core\DuplicateRecordException;
use PDO;
use PDOException;

/**
 * EduCRM - LeadRepository (Module A)
 *
 * Chua TAT CA SQL cua bang leads. KHONG doc $_POST / $_GET.
 * Dung prepared statement cho moi tham so tu nguoi dung.
 * Whitelist cot sort duoc ap dung TRUOC khi ghep vao ORDER BY.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class LeadRepository
{
    private ?PDO $db;

    /** Whitelist cot duoc phep sort - chong SQL injection qua ?sort= */
    private const SORTABLE = ['id', 'full_name', 'email', 'course', 'status', 'created_at'];

    public function __construct(?PDO $db = null)
    {
        // Lazy connect: chi mo ket noi khi co query (loi DB nem trong dispatch).
        $this->db = $db;
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    /**
     * Dem so lead khop dieu kien tim kiem / filter.
     */
    public function countFiltered(string $q, string $status, array $dates = []): int
    {
        [$where, $params] = $this->buildWhere($q, $status, $dates);
        $sql = 'SELECT COUNT(*) FROM leads ' . $where;
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Lay danh sach lead co tim kiem / filter / sort / phan trang.
     * $sort & $dir da duoc Service chuan hoa, nhung van whitelist lai lan nua.
     */
    public function paginate(
        string $q,
        string $status,
        string $sort,
        string $dir,
        int $limit,
        int $offset,
        array $dates = []
    ): array {
        // Lop bao ve thu hai: whitelist cot + direction
        $sort = in_array($sort, self::SORTABLE, true) ? $sort : 'created_at';
        $dir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        [$where, $params] = $this->buildWhere($q, $status, $dates);

        // $sort/$dir lay tu whitelist (an toan), $limit/$offset bind PARAM_INT.
        $sql = "SELECT id, full_name, email, phone, course, source, status, note, created_at
                FROM leads
                $where
                ORDER BY $sort $dir
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

    /**
     * Dung chung cho count + paginate: dung WHERE + params.
     */
    private function buildWhere(string $q, string $status, array $dates = []): array
    {
        // F2: mac dinh chi lay ban ghi CHUA xoa mem (deleted_at IS NULL).
        $clauses = ['deleted_at IS NULL'];
        $params  = [];

        if ($q !== '') {
            // Native prepared statements (EMULATE_PREPARES=false) khong cho dung
            // lai mot placeholder nhieu lan -> moi cot mot placeholder rieng.
            $like = '%' . $q . '%';
            $clauses[] = '(full_name LIKE :q_name OR email LIKE :q_email OR phone LIKE :q_phone)';
            $params[':q_name']  = $like;
            $params[':q_email'] = $like;
            $params[':q_phone'] = $like;
        }
        if ($status !== '') {
            $clauses[] = 'status = :status';
            $params[':status'] = $status;
        }
        // F8: loc theo khoang ngay tao (created_from / created_to), van bound.
        $from = (string) ($dates['created_from'] ?? '');
        $to   = (string) ($dates['created_to'] ?? '');
        if ($from !== '') {
            $clauses[] = 'created_at >= :created_from';
            $params[':created_from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $clauses[] = 'created_at <= :created_to';
            $params[':created_to'] = $to . ' 23:59:59';
        }

        $where = 'WHERE ' . implode(' AND ', $clauses);
        return [$where, $params];
    }

    public function find(int $id): ?array
    {
        // F2: chi tim lead con song (chua xoa mem).
        $stmt = $this->db()->prepare('SELECT * FROM leads WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Tim lead ke ca da xoa mem (cho trash / force-delete). */
    public function findWithTrashed(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM leads WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Them lead moi. Tach SQL command va du lieu nguoi dung qua placeholder.
     * Bat loi UNIQUE (email trung) -> nem DuplicateRecordException.
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO leads (full_name, email, phone, course, source, status, note)
                VALUES (:full_name, :email, :phone, :course, :source, :status, :note)';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':full_name' => $data['full_name'],
                ':email'     => $data['email'],
                ':phone'     => $data['phone'],
                ':course'    => $data['course'],
                ':source'    => $data['source'],
                ':status'    => $data['status'],
                ':note'      => $data['note'],
            ]);
            return (int) $this->db()->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateEmailException((string) $data['email']);
            }
            throw $e;
        }
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE leads
                SET full_name = :full_name, email = :email, phone = :phone,
                    course = :course, source = :source, status = :status, note = :note
                WHERE id = :id';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':full_name' => $data['full_name'],
                ':email'     => $data['email'],
                ':phone'     => $data['phone'],
                ':course'    => $data['course'],
                ':source'    => $data['source'],
                ':status'    => $data['status'],
                ':note'      => $data['note'],
                ':id'        => $id,
            ]);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateEmailException((string) $data['email']);
            }
            throw $e;
        }
    }

    /**
     * M1: dung thong bao trung email phu hop:
     *  - neu email dang nam o ban ghi DA XOA MEM -> goi khoi phuc
     *  - nguoc lai -> trung voi ban ghi dang hoat dong
     */
    private function duplicateEmailException(string $email): DuplicateRecordException
    {
        if ($email !== '' && $this->softDeletedHasEmail($email)) {
            return new DuplicateRecordException(
                'email',
                'Email nay dang nam trong thung rac - hay khoi phuc thay vi tao moi.'
            );
        }
        return new DuplicateRecordException('email', 'Email nay da ton tai trong he thong.');
    }

    /** F2: xoa MEM - chi danh dau deleted_at, KHONG DROP du lieu. */
    public function softDelete(int $id): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE leads SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** F2: khoi phuc ban ghi da xoa mem. */
    public function restore(int $id): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE leads SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** F2: xoa VINH VIEN (chi admin) - that su DROP khoi DB. */
    public function forceDelete(int $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM leads WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * F9: xoa MEM hang loat theo danh sach id. Dung placeholder dong (bound)
     * cho moi id -> van prepared statement. Tra so dong da xoa.
     */
    public function bulkSoftDelete(array $ids): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
        if (!$ids) {
            return 0;
        }
        $place = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $place[] = ':id' . $i;
            $params[':id' . $i] = $id;
        }
        $sql = 'UPDATE leads SET deleted_at = NOW()
                WHERE deleted_at IS NULL AND id IN (' . implode(',', $place) . ')';
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * F9: doi trang thai hang loat (chi cho gia tri status da whitelist o Service).
     * Tra so dong da cap nhat.
     */
    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
        if (!$ids) {
            return 0;
        }
        $place = [];
        $params = [':status' => $status];
        foreach ($ids as $i => $id) {
            $place[] = ':id' . $i;
            $params[':id' . $i] = $id;
        }
        $sql = 'UPDATE leads SET status = :status
                WHERE deleted_at IS NULL AND id IN (' . implode(',', $place) . ')';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        foreach ($ids as $i => $id) {
            $stmt->bindValue(':id' . $i, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** F1: doi trang thai lead (dung khi convert -> 'converted'). */
    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db()->prepare('UPDATE leads SET status = :status WHERE id = :id');
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * F1/m2: doi trang thai sang 'converted' MOT CACH NGUYEN TU (atomic guard).
     * Chi cap nhat khi lead chua converted/lost -> chong double-submit tao 2 phieu.
     * Tra TRUE neu doi duoc dung 1 dong, FALSE neu khong (da bi convert/lost roi).
     */
    public function markConvertedGuarded(int $id): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE leads
             SET status = 'converted', updated_at = NOW()
             WHERE id = :id AND status NOT IN ('converted', 'lost')"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    /**
     * M1: kiem tra co ban ghi DA XOA MEM dang giu email nay khong.
     * Dung de phan biet thong bao "dang trong thung rac" vs "dang ton tai".
     */
    public function softDeletedHasEmail(string $email): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT 1 FROM leads WHERE email = :email AND deleted_at IS NOT NULL LIMIT 1'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    /** F2: danh sach lead trong thung rac (da xoa mem), moi nhat truoc. */
    public function trash(): array
    {
        return $this->db()->query(
            'SELECT id, full_name, email, phone, course, status, deleted_at
             FROM leads WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC'
        )->fetchAll();
    }

    /**
     * F4: lay TOAN BO lead khop filter (khong phan trang) de stream CSV.
     * Dung lai buildWhere() nen cung ton trong deleted_at IS NULL + q + status.
     */
    public function exportFiltered(string $q, string $status, array $dates = []): array
    {
        [$where, $params] = $this->buildWhere($q, $status, $dates);
        $sql = "SELECT id, full_name, email, phone, course, source, status, note, created_at
                FROM leads $where ORDER BY created_at DESC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Dem theo trang thai (dung cho dashboard) - bo qua ban xoa mem. */
    public function countByStatus(): array
    {
        $rows = $this->db()->query(
            'SELECT status, COUNT(*) AS total FROM leads WHERE deleted_at IS NULL GROUP BY status'
        )->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['total'];
        }
        return $out;
    }

    public function total(): int
    {
        return (int) $this->db()->query(
            'SELECT COUNT(*) FROM leads WHERE deleted_at IS NULL'
        )->fetchColumn();
    }

    /** Danh sach lead rut gon (id + ten) cho dropdown form order - bo ban xoa mem. */
    public function options(): array
    {
        return $this->db()->query(
            'SELECT id, full_name, email FROM leads WHERE deleted_at IS NULL ORDER BY full_name ASC'
        )->fetchAll();
    }

    /** Phat hien loi trung khoa (SQLSTATE 23000 / errno 1062). */
    private function isDuplicate(PDOException $e): bool
    {
        return $e->getCode() === '23000'
            || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
    }
}
