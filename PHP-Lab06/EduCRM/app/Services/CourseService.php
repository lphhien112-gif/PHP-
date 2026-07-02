<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Core\DuplicateRecordException;

/**
 * EduCRM - CourseService (Module C)
 *
 * Business rule cua khoa hoc: validate server-side, chuan hoa (slug + kieu so),
 * pagination + sort whitelist, filter theo nhom/trang thai, duplicate handling,
 * ghi audit. Controller mong; Repository chi SQL.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class CourseService
{
    private CourseRepository $repo;
    private AuditService $audit;

    private const SORTABLE = ['id', 'name', 'category', 'level', 'price', 'is_active', 'created_at'];

    public function __construct(?CourseRepository $repo = null, ?AuditService $audit = null)
    {
        $this->repo  = $repo ?? new CourseRepository();
        $this->audit = $audit ?? new AuditService();
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    /** Chi tiet khoa hoc + muc do su dung (cho trang doc /courses/view). */
    public function detail(int $id): ?array
    {
        $course = $this->repo->find($id);
        if (!$course) {
            return null;
        }
        return ['course' => $course, 'usage' => $this->repo->usageStats($course['name'])];
    }

    public function findWithTrashed(int $id): ?array
    {
        return $this->repo->findWithTrashed($id);
    }

    public function trash(): array
    {
        return $this->repo->trash();
    }

    public function stats(): array
    {
        return ['total' => $this->repo->total(), 'active' => $this->repo->totalActive()];
    }

    /** Chuan hoa filter active tu query: '' | '1' | '0'. */
    private function normActive($v): string
    {
        $v = (string) $v;
        return ($v === '1' || $v === '0') ? $v : '';
    }

    public function list(array $query): array
    {
        $q        = trim((string) ($query['q'] ?? ''));
        $category = (string) ($query['category'] ?? '');
        $active   = $this->normActive($query['active'] ?? '');
        $sort     = (string) ($query['sort'] ?? 'created_at');
        $dir      = (string) ($query['direction'] ?? 'desc');
        $page     = (int) ($query['page'] ?? 1);

        $options = config('per_page_options', [10, 20, 50]);
        $perPage = (int) ($query['per_page'] ?? config('per_page', 10));
        if (!in_array($perPage, $options, true)) {
            $perPage = (int) config('per_page', 10);
        }

        if (!in_array($sort, self::SORTABLE, true)) {
            $sort = 'created_at';
        }
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        // Filter category chi chap nhan gia tri trong whitelist.
        if ($category !== '' && !in_array($category, config('course_categories', []), true)) {
            $category = '';
        }

        $total      = $this->repo->countFiltered($q, $category, $active);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $rows   = $this->repo->paginate($q, $category, $active, $sort, $dir, $perPage, $offset);

        return [
            'rows'           => $rows,
            'total'          => $total,
            'page'           => $page,
            'perPage'        => $perPage,
            'perPageOptions' => $options,
            'totalPages'     => $totalPages,
            'q'              => $q,
            'category'       => $category,
            'active'         => $active,
            'sort'           => $sort,
            'direction'      => $dir,
            'categories'     => config('course_categories', []),
        ];
    }

    /** F4: du lieu CSV theo dung filter hien tai. */
    public function exportRows(array $query): array
    {
        $q        = trim((string) ($query['q'] ?? ''));
        $category = (string) ($query['category'] ?? '');
        if ($category !== '' && !in_array($category, config('course_categories', []), true)) {
            $category = '';
        }
        return $this->repo->exportFiltered($q, $category, $this->normActive($query['active'] ?? ''));
    }

    /**
     * Validate du lieu khoa hoc (create / update). Tra mang loi theo field.
     */
    public function validate(array $input): array
    {
        $errors = [];

        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            $errors['name'] = 'Vui long nhap ten khoa hoc.';
        } elseif (mb_strlen($name) > 80) {
            $errors['name'] = 'Ten khoa hoc toi da 80 ky tu.';
        }

        $category = trim((string) ($input['category'] ?? ''));
        if (!in_array($category, config('course_categories', []), true)) {
            $errors['category'] = 'Vui long chon nhom khoa hoc hop le.';
        }

        $level = trim((string) ($input['level'] ?? ''));
        if (!in_array($level, config('course_levels', []), true)) {
            $errors['level'] = 'Trinh do khong hop le.';
        }

        $priceRaw = (string) ($input['price'] ?? '');
        if ($priceRaw === '' || !is_numeric($priceRaw)) {
            $errors['price'] = 'Hoc phi phai la so.';
        } elseif ((float) $priceRaw < 0) {
            $errors['price'] = 'Hoc phi khong duoc am.';
        } elseif ((float) $priceRaw > 999999999) {
            $errors['price'] = 'Hoc phi vuot qua gioi han cho phep.';
        }

        $weeksRaw = (string) ($input['duration_weeks'] ?? '');
        if ($weeksRaw === '' || !ctype_digit($weeksRaw)) {
            $errors['duration_weeks'] = 'Thoi luong phai la so nguyen (tuan).';
        } elseif ((int) $weeksRaw < 1 || (int) $weeksRaw > 260) {
            $errors['duration_weeks'] = 'Thoi luong tu 1 den 260 tuan.';
        }

        // image tuy chon; neu co phai la 1 file co san trong thu muc courses/
        $image = trim((string) ($input['image'] ?? ''));
        if ($image !== '' && !in_array($image, course_images(), true)) {
            $errors['image'] = 'Anh khong hop le (chon tu danh sach co san).';
        }

        $desc = (string) ($input['description'] ?? '');
        if (mb_strlen($desc) > 500) {
            $errors['description'] = 'Mo ta toi da 500 ky tu.';
        }

        return $errors;
    }

    /** Chuan hoa input -> mang du lieu sach cho Repository. */
    private function sanitize(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $slug = trim((string) ($input['slug'] ?? ''));
        if ($slug === '') {
            $slug = $this->slugify($name);
        }
        return [
            'name'           => $name,
            'slug'           => $slug,
            'category'       => trim((string) ($input['category'] ?? '')),
            'level'          => trim((string) ($input['level'] ?? 'beginner')),
            'price'          => (float) ($input['price'] ?? 0),
            'duration_weeks' => (int) ($input['duration_weeks'] ?? 0),
            'image'          => trim((string) ($input['image'] ?? '')),
            'description'    => trim((string) ($input['description'] ?? '')),
            'is_active'      => isset($input['is_active']) && (string) $input['is_active'] !== '0' ? 1 : 0,
        ];
    }

    /** Tao slug URL-friendly tu ten (ASCII, khong dau theo quy uoc du an). */
    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        $slug = trim($text, '-');
        return $slug !== '' ? mb_substr($slug, 0, 100) : 'khoa-hoc';
    }

    public function create(array $input): array
    {
        $errors = $this->validate($input);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $data = $this->sanitize($input);
        try {
            $id = $this->repo->create($data);
            $this->audit->log('create', 'course', $id, 'Tao khoa hoc #' . $id . ' ' . $data['name']);
            return ['ok' => true, 'id' => $id];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    public function update(int $id, array $input): array
    {
        $errors = $this->validate($input);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $data = $this->sanitize($input);
        try {
            $this->repo->update($id, $data);
            $this->audit->log('update', 'course', $id, 'Cap nhat khoa hoc #' . $id);
            return ['ok' => true];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    public function toggleActive(int $id): void
    {
        $this->repo->toggleActive($id);
        $this->audit->log('update', 'course', $id, 'Doi trang thai hien thi khoa hoc #' . $id);
    }

    public function delete(int $id): void
    {
        $this->repo->softDelete($id);
        $this->audit->log('delete', 'course', $id, 'Xoa mem khoa hoc #' . $id);
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);
        $this->audit->log('restore', 'course', $id, 'Khoi phuc khoa hoc #' . $id);
    }

    public function forceDelete(int $id): void
    {
        $this->repo->forceDelete($id);
        $this->audit->log('delete', 'course', $id, 'Xoa vinh vien khoa hoc #' . $id);
    }
}
