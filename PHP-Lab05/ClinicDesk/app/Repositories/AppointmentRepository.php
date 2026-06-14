<?php

namespace ClinicDesk\Repositories;

use ClinicDesk\Core\Database;
use PDO;

/**
 * AppointmentRepository - Toan bo SQL cho module appointments.
 *
 * - search theo appointment_code / patient_name / patient_email.
 * - filter theo status (lam them).
 * - sort qua WHITELIST.
 * - appointment_code UNIQUE -> chan trung ma lich hen.
 */
class AppointmentRepository
{
    private const SORTABLE = [
        'id'               => 'id',
        'appointment_code' => 'appointment_code',
        'patient_name'     => 'patient_name',
        'appointment_date' => 'appointment_date',
        'status'           => 'status',
        'created_at'       => 'created_at',
    ];

    private const DEFAULT_SORT = 'created_at';
    private const DEFAULT_DIR  = 'DESC';

    public const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    public const DEPARTMENTS = [
        'Noi tong quat', 'Tai Mui Hong', 'Da lieu', 'San phu khoa',
        'Rang Ham Mat', 'Mat', 'Tim mach', 'Co xuong khop',
        'Noi tiet', 'Tieu hoa',
    ];

    private function db(): PDO
    {
        return Database::connection();
    }

    public function resolveSort(?string $sort): string
    {
        $sort = (string)$sort;
        return self::SORTABLE[$sort] ?? self::SORTABLE[self::DEFAULT_SORT];
    }

    public function resolveDirection(?string $dir): string
    {
        $dir = strtoupper(trim((string)$dir));
        return in_array($dir, ['ASC', 'DESC'], true) ? $dir : self::DEFAULT_DIR;
    }

    public function sortKey(?string $sort): string
    {
        $sort = (string)$sort;
        return isset(self::SORTABLE[$sort]) ? $sort : self::DEFAULT_SORT;
    }

    /**
     * Build menh de WHERE chung cho count va paginate.
     * @return array{0:string, 1:array<string,mixed>}
     */
    private function buildWhere(string $q, string $status): array
    {
        $clauses = [];
        $params  = [];

        if ($q !== '') {
            $clauses[] = '(appointment_code LIKE :q1 OR patient_name LIKE :q2 OR patient_email LIKE :q3)';
            $like = '%' . $q . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
        }
        // Filter theo status chi nhan gia tri hop le (whitelist ENUM).
        if ($status !== '' && in_array($status, self::STATUSES, true)) {
            $clauses[] = 'status = :status';
            $params[':status'] = $status;
        }

        $where = $clauses ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        return [$where, $params];
    }

    public function countAll(string $q = '', string $status = ''): int
    {
        [$where, $params] = $this->buildWhere($q, $status);
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM appointments' . $where);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function paginate(string $q, string $status, string $sort, string $direction, int $limit, int $offset): array
    {
        $sortColumn = $this->resolveSort($sort);
        $dir        = $this->resolveDirection($direction);
        [$where, $params] = $this->buildWhere($q, $status);

        $sql = 'SELECT id, appointment_code, patient_name, patient_email,
                       department, appointment_date, status, note, created_at
                FROM appointments' . $where
             . " ORDER BY {$sortColumn} {$dir}, id {$dir} LIMIT :limit OFFSET :offset";

        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM appointments WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function codeExists(string $code, int $excludeId = 0): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT id FROM appointments WHERE appointment_code = :code AND id <> :id LIMIT 1'
        );
        $stmt->bindValue(':code', $code, PDO::PARAM_STR);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO appointments
                    (appointment_code, patient_name, patient_email, department,
                     appointment_date, status, note)
                VALUES
                    (:code, :pname, :pemail, :dept, :adate, :status, :note)';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':code',   $data['appointment_code'], PDO::PARAM_STR);
        $stmt->bindValue(':pname',  $data['patient_name'], PDO::PARAM_STR);
        $stmt->bindValue(':pemail', $data['patient_email'], PDO::PARAM_STR);
        $stmt->bindValue(':dept',   $data['department'], PDO::PARAM_STR);
        $stmt->bindValue(':adate',  $data['appointment_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':note',   $data['note'] !== '' ? $data['note'] : null);
        $stmt->execute();
        return (int)$this->db()->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE appointments
                SET appointment_code = :code, patient_name = :pname,
                    patient_email = :pemail, department = :dept,
                    appointment_date = :adate, status = :status, note = :note
                WHERE id = :id';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':code',   $data['appointment_code'], PDO::PARAM_STR);
        $stmt->bindValue(':pname',  $data['patient_name'], PDO::PARAM_STR);
        $stmt->bindValue(':pemail', $data['patient_email'], PDO::PARAM_STR);
        $stmt->bindValue(':dept',   $data['department'], PDO::PARAM_STR);
        $stmt->bindValue(':adate',  $data['appointment_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':note',   $data['note'] !== '' ? $data['note'] : null);
        $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $stmt = $this->db()->prepare('DELETE FROM appointments WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function sortableColumns(): array
    {
        return array_keys(self::SORTABLE);
    }
}
