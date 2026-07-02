<?php

namespace App\Repositories;

use App\Core\Database;
use App\Core\DuplicateRecordException;
use PDO;
use PDOException;

/**
 * EduCRM - CourseRepository (Module C)
 *
 * Chua TAT CA SQL cua bang courses. KHONG doc $_POST / $_GET.
 * Prepared statement cho moi tham so; whitelist cot sort truoc ORDER BY;
 * moi truy van bo qua ban ghi da xoa mem (deleted_at IS NULL) tru trash/force.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class CourseRepository
{
    private ?PDO $db;

    /** Whitelist cot duoc phep sort - chong SQL injection qua ?sort= */
    private const SORTABLE = ['id', 'name', 'category', 'level', 'price', 'is_active', 'created_at'];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db; // lazy connect
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    /** Dem so khoa hoc khop tim kiem / filter. */
    public function countFiltered(string $q, string $category, string $active): int
    {
        [$where, $params] = $this->buildWhere($q, $category, $active);
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM courses ' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Danh sach khoa hoc: search + filter + sort + phan trang. */
    public function paginate(
        string $q,
        string $category,
        string $active,
        string $sort,
        string $dir,
        int $limit,
        int $offset
    ): array {
        $sort = in_array($sort, self::SORTABLE, true) ? $sort : 'created_at';
        $dir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        [$where, $params] = $this->buildWhere($q, $category, $active);

        $sql = "SELECT id, name, slug, category, level, price, duration_weeks,
                       image, description, is_active, created_at
                FROM courses
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

    /** WHERE + params dung chung cho count + paginate + export. */
    private function buildWhere(string $q, string $category, string $active): array
    {
        $clauses = ['deleted_at IS NULL']; // mac dinh chi ban con song
        $params  = [];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $clauses[] = '(name LIKE :q_name OR slug LIKE :q_slug OR description LIKE :q_desc)';
            $params[':q_name'] = $like;
            $params[':q_slug'] = $like;
            $params[':q_desc'] = $like;
        }
        if ($category !== '') {
            $clauses[] = 'category = :category';
            $params[':category'] = $category;
        }
        if ($active === '1' || $active === '0') {
            $clauses[] = 'is_active = :active';
            $params[':active'] = $active;
        }

        return ['WHERE ' . implode(' AND ', $clauses), $params];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM courses WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /** Tim khoa hoc ke ca da xoa mem (cho trash / restore / force-delete). */
    public function findWithTrashed(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM courses WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO courses (name, slug, category, level, price, duration_weeks, image, description, outcomes, is_active)
                VALUES (:name, :slug, :category, :level, :price, :duration_weeks, :image, :description, :outcomes, :is_active)';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($this->bindParams($data));
            return (int) $this->db()->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateNameException((string) $data['name']);
            }
            throw $e;
        }
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE courses
                SET name = :name, slug = :slug, category = :category, level = :level,
                    price = :price, duration_weeks = :duration_weeks, image = :image,
                    description = :description, outcomes = :outcomes, is_active = :is_active
                WHERE id = :id';
        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($this->bindParams($data) + [':id' => $id]);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                throw $this->duplicateNameException((string) $data['name']);
            }
            throw $e;
        }
    }

    /** Map du lieu sach -> placeholder (dung chung create/update). */
    private function bindParams(array $data): array
    {
        return [
            ':name'           => $data['name'],
            ':slug'           => $data['slug'],
            ':category'       => $data['category'],
            ':level'          => $data['level'],
            ':price'          => $data['price'],
            ':duration_weeks' => $data['duration_weeks'],
            ':image'          => $data['image'] !== '' ? $data['image'] : null,
            ':description'    => $data['description'] !== '' ? $data['description'] : null,
            ':outcomes'       => $data['outcomes'] !== '' ? $data['outcomes'] : null,
            ':is_active'      => $data['is_active'],
        ];
    }

    /** Bat/tat trang thai hien thi (chi doi khi ban con song). */
    public function toggleActive(int $id): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE courses SET is_active = 1 - is_active WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db()->prepare('UPDATE courses SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function restore(int $id): void
    {
        $stmt = $this->db()->prepare('UPDATE courses SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function forceDelete(int $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** Danh sach khoa hoc trong thung rac, moi nhat truoc. */
    public function trash(): array
    {
        return $this->db()->query(
            'SELECT id, name, category, level, price, image, deleted_at
             FROM courses WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC'
        )->fetchAll();
    }

    /** F4: toan bo khoa hoc khop filter (khong phan trang) de stream CSV. */
    public function exportFiltered(string $q, string $category, string $active): array
    {
        [$where, $params] = $this->buildWhere($q, $category, $active);
        $sql = "SELECT id, name, category, level, price, duration_weeks, is_active, created_at
                FROM courses $where ORDER BY created_at DESC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function total(): int
    {
        return (int) $this->db()->query('SELECT COUNT(*) FROM courses WHERE deleted_at IS NULL')->fetchColumn();
    }

    public function totalActive(): int
    {
        return (int) $this->db()->query(
            'SELECT COUNT(*) FROM courses WHERE deleted_at IS NULL AND is_active = 1'
        )->fetchColumn();
    }

    /**
     * Muc do su dung cua mot khoa hoc (theo ten) - cho trang chi tiet:
     * so lead quan tam, so phieu da tao, doanh thu da thu (order 'paid').
     * leads.course / orders.course tham chieu courses.name (dang chuoi).
     */
    public function usageStats(string $name): array
    {
        $leads = $this->db()->prepare('SELECT COUNT(*) FROM leads WHERE course = :n AND deleted_at IS NULL');
        $leads->bindValue(':n', $name, PDO::PARAM_STR);
        $leads->execute();

        $orders = $this->db()->prepare('SELECT COUNT(*) FROM orders WHERE course = :n AND deleted_at IS NULL');
        $orders->bindValue(':n', $name, PDO::PARAM_STR);
        $orders->execute();

        $rev = $this->db()->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM orders
             WHERE course = :n AND status = 'paid' AND deleted_at IS NULL"
        );
        $rev->bindValue(':n', $name, PDO::PARAM_STR);
        $rev->execute();

        return [
            'leads'   => (int) $leads->fetchColumn(),
            'orders'  => (int) $orders->fetchColumn(),
            'revenue' => (float) $rev->fetchColumn(),
        ];
    }

    /**
     * Dung thong bao trung ten phu hop (giong M1 o leads):
     *  - ten dang o ban DA XOA MEM -> goi khoi phuc
     *  - nguoc lai -> trung voi ban dang hoat dong
     */
    private function duplicateNameException(string $name): DuplicateRecordException
    {
        if ($name !== '' && $this->softDeletedHasName($name)) {
            return new DuplicateRecordException(
                'name',
                'Ten khoa hoc nay dang nam trong thung rac - hay khoi phuc thay vi tao moi.'
            );
        }
        return new DuplicateRecordException('name', 'Ten khoa hoc nay da ton tai.');
    }

    public function softDeletedHasName(string $name): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT 1 FROM courses WHERE name = :name AND deleted_at IS NOT NULL LIMIT 1'
        );
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    /** Phat hien loi trung khoa (SQLSTATE 23000 / errno 1062). */
    private function isDuplicate(PDOException $e): bool
    {
        return $e->getCode() === '23000'
            || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
    }
}
