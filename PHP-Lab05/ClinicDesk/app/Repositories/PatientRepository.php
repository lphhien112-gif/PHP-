<?php

namespace ClinicDesk\Repositories;

use ClinicDesk\Core\Database;
use PDO;

/**
 * PatientRepository - Toan bo SQL cho module patients nam o day.
 *
 * Nguyen tac:
 *   - Khong noi chuoi $_GET/$_POST vao SQL.
 *   - INSERT/SELECT/UPDATE/DELETE deu dung prepare() + bindValue() + execute().
 *   - ORDER BY chi nhan cot/huong tu WHITELIST (khong lay thang tu URL).
 *   - LIMIT/OFFSET bind bang PARAM_INT.
 */
class PatientRepository
{
    /**
     * Whitelist cot duoc phep sort. Key = ten public (tu URL), value = ten cot that.
     * Bat ky sort khong nam trong day -> dung mac dinh 'created_at'.
     */
    private const SORTABLE = [
        'id'         => 'id',
        'name'       => 'name',
        'email'      => 'email',
        'created_at' => 'created_at',
    ];

    private const DEFAULT_SORT = 'created_at';
    private const DEFAULT_DIR  = 'DESC';

    private function db(): PDO
    {
        return Database::connection();
    }

    /**
     * Chuan hoa cot sort qua whitelist. Tra ten cot that (an toan de noi vao SQL).
     */
    public function resolveSort(?string $sort): string
    {
        $sort = (string)$sort;
        return self::SORTABLE[$sort] ?? self::SORTABLE[self::DEFAULT_SORT];
    }

    /**
     * Chuan hoa huong sort. Chi cho phep ASC/DESC.
     */
    public function resolveDirection(?string $dir): string
    {
        $dir = strtoupper(trim((string)$dir));
        return in_array($dir, ['ASC', 'DESC'], true) ? $dir : self::DEFAULT_DIR;
    }

    /** Cot sort hop le (de view danh dau active). */
    public function sortKey(?string $sort): string
    {
        $sort = (string)$sort;
        return isset(self::SORTABLE[$sort]) ? $sort : self::DEFAULT_SORT;
    }

    /**
     * Dem so ban ghi khop dieu kien search (dung cho pagination).
     */
    public function countAll(string $q = ''): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM patients';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE name LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3';
            $like = '%' . $q . '%';
            $params = [':q1' => $like, ':q2' => $like, ':q3' => $like];
        }
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lay danh sach co search + sort (whitelist) + pagination.
     * Chi SELECT cot can hien thi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function paginate(string $q, string $sort, string $direction, int $limit, int $offset): array
    {
        $sortColumn = $this->resolveSort($sort);          // qua whitelist
        $dir        = $this->resolveDirection($direction); // ASC/DESC

        $sql = 'SELECT id, name, email, phone, gender, address, created_at
                FROM patients';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE name LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3';
            $like = '%' . $q . '%';
            $params = [':q1' => $like, ':q2' => $like, ':q3' => $like];
        }

        // $sortColumn va $dir da qua whitelist nen an toan khi noi vao SQL.
        $sql .= " ORDER BY {$sortColumn} {$dir}, id {$dir} LIMIT :limit OFFSET :offset";

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
     * Tim 1 benh nhan theo id (dung cho form edit).
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM patients WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Kiem tra email da ton tai chua (loai tru $excludeId khi update).
     * Day la kiem tra "mem" o tang PHP; UNIQUE constraint moi la chot chan that.
     */
    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT id FROM patients WHERE email = :email AND id <> :id LIMIT 1'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    /**
     * INSERT benh nhan moi. Tra id vua tao.
     * Co the nem PDOException SQLSTATE 23000 neu trung email (UNIQUE).
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO patients (name, email, phone, gender, address)
                VALUES (:name, :email, :phone, :gender, :address)';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $data['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $data['address'] !== '' ? $data['address'] : null);
        $stmt->execute();
        return (int)$this->db()->lastInsertId();
    }

    /**
     * UPDATE benh nhan. Tra so dong bi anh huong.
     */
    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE patients
                SET name = :name, email = :email, phone = :phone,
                    gender = :gender, address = :address
                WHERE id = :id';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $data['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $data['address'] !== '' ? $data['address'] : null);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * DELETE benh nhan theo id (goi tu POST). Tra so dong bi xoa.
     */
    public function delete(int $id): int
    {
        $stmt = $this->db()->prepare('DELETE FROM patients WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** Danh sach cot sort hop le (cho view header). */
    public function sortableColumns(): array
    {
        return array_keys(self::SORTABLE);
    }
}
