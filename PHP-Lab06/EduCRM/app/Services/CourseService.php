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
            $errors['name'] = 'Vui lòng nhập tên khóa học.';
        } elseif (mb_strlen($name) > 80) {
            $errors['name'] = 'Tên khóa học tối đa 80 ký tự.';
        }

        $category = trim((string) ($input['category'] ?? ''));
        if (!in_array($category, config('course_categories', []), true)) {
            $errors['category'] = 'Vui lòng chọn nhóm khóa học hợp lệ.';
        }

        $level = trim((string) ($input['level'] ?? ''));
        if (!in_array($level, config('course_levels', []), true)) {
            $errors['level'] = 'Trình độ không hợp lệ.';
        }

        $priceRaw = (string) ($input['price'] ?? '');
        if ($priceRaw === '' || !is_numeric($priceRaw)) {
            $errors['price'] = 'Học phí phải là số.';
        } elseif ((float) $priceRaw < 0) {
            $errors['price'] = 'Học phí không được âm.';
        } elseif ((float) $priceRaw > 999999999) {
            $errors['price'] = 'Học phí vượt quá giới hạn cho phép.';
        }

        $weeksRaw = (string) ($input['duration_weeks'] ?? '');
        if ($weeksRaw === '' || !ctype_digit($weeksRaw)) {
            $errors['duration_weeks'] = 'Thời lượng phải là số nguyên (tuần).';
        } elseif ((int) $weeksRaw < 1 || (int) $weeksRaw > 260) {
            $errors['duration_weeks'] = 'Thời lượng từ 1 đến 260 tuần.';
        }

        // image tuy chon; neu co phai la 1 file co san trong thu muc courses/
        $image = trim((string) ($input['image'] ?? ''));
        if ($image !== '' && !in_array($image, course_images(), true)) {
            $errors['image'] = 'Ảnh không hợp lệ (chọn từ danh sách có sẵn).';
        }

        $desc = (string) ($input['description'] ?? '');
        if (mb_strlen($desc) > 2000) {
            $errors['description'] = 'Mô tả tối đa 2000 ký tự.';
        }

        $outcomes = (string) ($input['outcomes'] ?? '');
        if (mb_strlen($outcomes) > 3000) {
            $errors['outcomes'] = 'Mục "Bạn sẽ học được gì" tối đa 3000 ký tự.';
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
            // Chuan hoa xuong dong ve \n de view tach thanh cac gach dau dong.
            'outcomes'       => str_replace(["\r\n", "\r"], "\n", trim((string) ($input['outcomes'] ?? ''))),
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
            $this->audit->log('create', 'course', $id, 'Tạo khóa học #' . $id . ' ' . $data['name']);
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
            $this->audit->log('update', 'course', $id, 'Cập nhật khóa học #' . $id);
            return ['ok' => true];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    public function toggleActive(int $id): void
    {
        $this->repo->toggleActive($id);
        $this->audit->log('update', 'course', $id, 'Đổi trạng thái hiển thị khóa học #' . $id);
    }

    public function delete(int $id): void
    {
        $this->repo->softDelete($id);
        $this->audit->log('delete', 'course', $id, 'Xóa mềm khóa học #' . $id);
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);
        $this->audit->log('restore', 'course', $id, 'Khôi phục khóa học #' . $id);
    }

    public function forceDelete(int $id): void
    {
        $this->repo->forceDelete($id);
        $this->audit->log('delete', 'course', $id, 'Xóa vĩnh viễn khóa học #' . $id);
    }
}
