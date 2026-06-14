<?php

namespace App\Controllers;

use App\Services\LeadService;
use App\Services\OrderService;
use App\Core\Csv;

/**
 * EduCRM - LeadController (Module A)
 *
 * THIN controller cho CRUD lead. Doc request -> goi LeadService -> render/redirect.
 * KHONG co SQL. Tat ca validate/duplicate/pagination o Service.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class LeadController
{
    private LeadService $leads;

    public function __construct()
    {
        $this->leads = new LeadService();
    }

    /** GET /leads - list + search + pagination + sort. */
    public function index(): void
    {
        require_login();
        $data = $this->leads->list($_GET);
        $data['title'] = 'Quan ly Lead';
        echo render('leads/index', $data);
        clear_old();
    }

    /** GET /leads/create - form them lead. */
    public function create(): void
    {
        require_login();
        echo render('leads/create', ['title' => 'Them Lead moi']);
        clear_old();
    }

    /** POST /leads/store - validate + create + duplicate handling + PRG. */
    public function store(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF). Vui long thu lai.');
            redirect('/leads/create');
        }

        $result = $this->leads->create($_POST, $_POST['status'] ?? 'new');
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Khong the tao lead. Vui long kiem tra lai.');
            redirect('/leads/create');
        }

        flash('success', 'Da them lead moi thanh cong.');
        redirect('/leads');
    }

    /** GET /leads/edit?id=.. - form sua co du lieu cu. */
    public function edit(): void
    {
        require_login();
        $id   = (int) ($_GET['id'] ?? 0);
        $lead = $this->leads->find($id);
        if (!$lead) {
            flash('error', 'Khong tim thay lead.');
            redirect('/leads');
        }
        echo render('leads/edit', ['title' => 'Sua Lead', 'lead' => $lead]);
        clear_old();
    }

    /** POST /leads/update - validate + update + PRG. */
    public function update(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/leads');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if (!$this->leads->find($id)) {
            flash('error', 'Khong tim thay lead.');
            redirect('/leads');
        }

        $result = $this->leads->update($id, $_POST);
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Cap nhat that bai. Vui long kiem tra lai.');
            redirect('/leads/edit?id=' . $id);
        }

        flash('success', 'Cap nhat lead thanh cong.');
        redirect('/leads');
    }

    /** POST /leads/delete - F2: xoa MEM (chi admin). */
    public function delete(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/leads');
        }
        // F11: soft-delete - admin + manager (server-side enforce).
        require_can('soft_delete', '/leads');
        $id = (int) ($_POST['id'] ?? 0);
        $this->leads->delete($id);
        flash('success', 'Da chuyen lead vao thung rac.');
        redirect('/leads');
    }

    /**
     * POST /leads/bulk - F9: hanh dong hang loat (xoa mem / doi status).
     * Nhan id[] + bulk_action. CSRF + role-gated (server-side).
     */
    public function bulk(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/leads');
        }

        $ids    = (array) ($_POST['id'] ?? []);
        $action = (string) ($_POST['bulk_action'] ?? '');

        if (empty($ids)) {
            flash('error', 'Chua chon dong nao.');
            redirect('/leads');
        }

        if ($action === 'delete') {
            require_can('soft_delete', '/leads'); // F11
            $n = $this->leads->bulkDelete($ids);
            flash('success', 'Da chuyen ' . $n . ' lead vao thung rac.');
        } elseif ($action === 'status') {
            require_can('status_change', '/leads'); // F11: admin + manager
            $status = (string) ($_POST['bulk_status'] ?? '');
            $n = $this->leads->bulkStatus($ids, $status);
            if ($n > 0) {
                flash('success', 'Da doi trang thai ' . $n . ' lead -> ' . $status . '.');
            } else {
                flash('error', 'Trang thai khong hop le hoac khong co dong nao thay doi.');
            }
        } else {
            flash('error', 'Hanh dong hang loat khong hop le.');
        }
        redirect('/leads');
    }

    /** GET /leads/trash - F2: danh sach lead da xoa mem (admin + manager). */
    public function trash(): void
    {
        require_login();
        require_can('trash', '/leads'); // F11: admin + manager
        echo render('leads/trash', [
            'title' => 'Thung rac Lead',
            'rows'  => $this->leads->trash(),
        ]);
        clear_old();
    }

    /** POST /leads/restore - F2: khoi phuc lead (chi admin). */
    public function restore(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/leads/trash');
        }
        require_can('restore', '/leads'); // F11: admin + manager
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->leads->findWithTrashed($id)) {
            $this->leads->restore($id);
            flash('success', 'Da khoi phuc lead.');
        } else {
            flash('error', 'Khong tim thay lead can khoi phuc.');
        }
        redirect('/leads/trash');
    }

    /** POST /leads/force-delete - F2: xoa VINH VIEN (chi admin). */
    public function forceDelete(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF).');
            redirect('/leads/trash');
        }
        require_can('force_delete', '/leads'); // F11: chi admin
        $id = (int) ($_POST['id'] ?? 0);
        $this->leads->forceDelete($id);
        flash('success', 'Da xoa vinh vien lead.');
        redirect('/leads/trash');
    }

    /** GET /leads/convert?id=.. - F1: form tao phieu thu prefilled tu lead. */
    public function convert(): void
    {
        require_login();
        $id   = (int) ($_GET['id'] ?? 0);
        $lead = $this->leads->find($id);
        if (!$lead) {
            flash('error', 'Khong tim thay lead.');
            redirect('/leads');
        }
        if (in_array($lead['status'], ['converted', 'lost'], true)) {
            flash('error', 'Lead nay da chuyen doi hoac da mat, khong the tao phieu.');
            redirect('/leads');
        }

        $orders = new OrderService();
        echo render('leads/convert', [
            'title'       => 'Tao phieu thu tu Lead',
            'lead'        => $lead,
            'suggestCode' => $orders->suggestCode(),
        ]);
        clear_old();
    }

    /** GET /leads/export - F4: stream CSV theo dung filter hien tai. */
    public function export(): void
    {
        require_login();
        require_can('export', '/leads'); // F11: admin + manager
        $rows = $this->leads->exportRows($_GET);

        $headers = ['ID', 'Ho ten', 'Email', 'SDT', 'Khoa hoc', 'Nguon', 'Trang thai', 'Ghi chu', 'Ngay tao'];
        $data = array_map(fn($r) => [
            $r['id'], $r['full_name'], $r['email'], $r['phone'],
            $r['course'], $r['source'], $r['status'], $r['note'], $r['created_at'],
        ], $rows);

        Csv::stream('leads-' . date('Ymd-His') . '.csv', $headers, $data);
    }
}
