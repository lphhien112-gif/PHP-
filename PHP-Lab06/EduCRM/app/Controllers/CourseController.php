<?php

namespace App\Controllers;

use App\Services\CourseService;
use App\Core\Csv;

/**
 * EduCRM - CourseController (Module C)
 *
 * THIN controller cho CRUD khoa hoc. Doc request -> CourseService -> render/redirect.
 * KHONG co SQL. Phan quyen enforce SERVER-SIDE:
 *   - Xem danh sach: moi user da dang nhap.
 *   - Them/sua/xoa mem/khoi phuc/toggle: 'manage_courses' (admin + manager).
 *   - Xoa vinh vien: 'force_delete' (chi admin). Export CSV: 'export'.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class CourseController
{
    private CourseService $courses;

    public function __construct()
    {
        $this->courses = new CourseService();
    }

    /** GET /courses - grid + search + filter + sort + pagination. */
    public function index(): void
    {
        require_login();
        $data = $this->courses->list($_GET);
        $data['title'] = 'Quan ly Khoa hoc';
        echo render('courses/index', $data);
        clear_old();
    }

    /** GET /courses/view?id=.. - trang chi tiet (doc mo ta). Moi user da dang nhap. */
    public function view(): void
    {
        require_login();
        $id     = (int) ($_GET['id'] ?? 0);
        $detail = $this->courses->detail($id);
        if (!$detail) {
            flash('error', 'Khong tim thay khoa hoc.');
            redirect('/courses');
        }
        echo render('courses/view', [
            'title'  => $detail['course']['name'],
            'course' => $detail['course'],
            'usage'  => $detail['usage'],
        ]);
        clear_old();
    }

    /** GET /courses/create - form them khoa hoc. */
    public function create(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        echo render('courses/create', ['title' => 'Them khoa hoc']);
        clear_old();
    }

    /** POST /courses/store - validate + create + duplicate + PRG. */
    public function store(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF). Vui long thu lai.');
            redirect('/courses/create');
        }

        $result = $this->courses->create($_POST);
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Khong the tao khoa hoc. Vui long kiem tra lai.');
            redirect('/courses/create');
        }

        flash('success', 'Da them khoa hoc moi thanh cong.');
        redirect('/courses');
    }

    /** GET /courses/edit?id=.. - form sua co du lieu cu. */
    public function edit(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        $id     = (int) ($_GET['id'] ?? 0);
        $course = $this->courses->find($id);
        if (!$course) {
            flash('error', 'Khong tim thay khoa hoc.');
            redirect('/courses');
        }
        echo render('courses/edit', ['title' => 'Sua khoa hoc', 'course' => $course]);
        clear_old();
    }

    /** POST /courses/update - validate + update + PRG. */
    public function update(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/courses');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if (!$this->courses->find($id)) {
            flash('error', 'Khong tim thay khoa hoc.');
            redirect('/courses');
        }

        $result = $this->courses->update($id, $_POST);
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Cap nhat that bai. Vui long kiem tra lai.');
            redirect('/courses/edit?id=' . $id);
        }

        flash('success', 'Cap nhat khoa hoc thanh cong.');
        redirect('/courses');
    }

    /** POST /courses/toggle - bat/tat hien thi (manage_courses). */
    public function toggle(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/courses');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->courses->find($id)) {
            $this->courses->toggleActive($id);
            flash('success', 'Da doi trang thai hien thi khoa hoc.');
        } else {
            flash('error', 'Khong tim thay khoa hoc.');
        }
        redirect('/courses');
    }

    /** POST /courses/delete - xoa MEM (manage_courses). */
    public function delete(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/courses');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $this->courses->delete($id);
        flash('success', 'Da chuyen khoa hoc vao thung rac.');
        redirect('/courses');
    }

    /** GET /courses/trash - danh sach da xoa mem (manage_courses). */
    public function trash(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        echo render('courses/trash', [
            'title' => 'Thung rac Khoa hoc',
            'rows'  => $this->courses->trash(),
        ]);
        clear_old();
    }

    /** POST /courses/restore - khoi phuc (manage_courses). */
    public function restore(): void
    {
        require_login();
        require_can('manage_courses', '/courses');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/courses/trash');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->courses->findWithTrashed($id)) {
            $this->courses->restore($id);
            flash('success', 'Da khoi phuc khoa hoc.');
        } else {
            flash('error', 'Khong tim thay khoa hoc can khoi phuc.');
        }
        redirect('/courses/trash');
    }

    /** POST /courses/force-delete - xoa VINH VIEN (chi admin). */
    public function forceDelete(): void
    {
        require_login();
        require_can('force_delete', '/courses/trash');
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/courses/trash');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $this->courses->forceDelete($id);
        flash('success', 'Da xoa vinh vien khoa hoc.');
        redirect('/courses/trash');
    }

    /** GET /courses/export - F4: stream CSV theo dung filter hien tai. */
    public function export(): void
    {
        require_login();
        require_can('export', '/courses');
        $rows = $this->courses->exportRows($_GET);

        $headers = ['ID', 'Ten khoa hoc', 'Nhom', 'Trinh do', 'Hoc phi', 'So tuan', 'Hien thi', 'Ngay tao'];
        $data = array_map(fn($r) => [
            $r['id'], $r['name'], $r['category'], $r['level'],
            $r['price'], $r['duration_weeks'],
            ((int) $r['is_active'] === 1 ? 'Hien' : 'An'), $r['created_at'],
        ], $rows);

        Csv::stream('courses-' . date('Ymd-His') . '.csv', $headers, $data);
    }
}
