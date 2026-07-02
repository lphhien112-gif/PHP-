<?php

namespace App\Controllers;

use App\Services\OrderService;
use App\Services\LeadService;
use App\Core\Csv;

/**
 * EduCRM - OrderController (Module B)
 *
 * THIN controller cho CRUD order/phieu hoc phi. Doc request -> goi
 * OrderService -> render/redirect. KHONG co SQL.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class OrderController
{
    private OrderService $orders;

    public function __construct()
    {
        $this->orders = new OrderService();
    }

    /** GET /orders - list + search + pagination + sort. */
    public function index(): void
    {
        require_login();
        $data = $this->orders->list($_GET);
        $data['title'] = 'Quan ly Phieu hoc phi';
        echo render('orders/index', $data);
        clear_old();
    }

    /** GET /orders/create - form them phieu (goi y ma + dropdown lead). */
    public function create(): void
    {
        require_login();
        echo render('orders/create', [
            'title'        => 'Tao phieu hoc phi',
            'leadOptions'  => $this->orders->leadOptions(),
            'suggestCode'  => $this->orders->suggestCode(),
        ]);
        clear_old();
    }

    /**
     * POST /orders/store - validate + create (transaction) + duplicate + PRG.
     * F1: neu co from_lead_id -> chuyen lead thanh phieu trong 1 transaction
     *     (INSERT order + leads.status='converted', rollback neu trung ma).
     */
    public function store(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders/create');
        }

        $fromLeadId = (int) ($_POST['from_lead_id'] ?? 0);

        // ----- F1: luong chuyen doi lead -> order -----
        if ($fromLeadId > 0) {
            $leads  = new LeadService();
            $result = $leads->convertToOrder(
                $fromLeadId,
                $_POST,
                fn(array $in) => $this->orders->validateForConvert($in),
                fn(array $in) => $this->orders->sanitizeForConvert($in)
            );
            if (!$result['ok']) {
                set_old($_POST, $result['errors']);
                flash('error', 'Không thể tạo phiếu từ lead. Vui lòng kiểm tra lại.');
                redirect('/leads/convert?id=' . $fromLeadId);
            }
            flash('success', 'Đã chuyển lead thành phiếu thu (lead chuyển sang trạng thái converted).');
            redirect('/orders');
        }

        // ----- Luong tao phieu thong thuong -----
        $result = $this->orders->create($_POST);
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Không thể tạo phiếu. Vui lòng kiểm tra lại.');
            redirect('/orders/create');
        }

        flash('success', 'Đã tạo phiếu học phí thành công.');
        redirect('/orders');
    }

    /** GET /orders/edit?id=.. */
    public function edit(): void
    {
        require_login();
        $id    = (int) ($_GET['id'] ?? 0);
        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy phiếu.');
            redirect('/orders');
        }
        echo render('orders/edit', [
            'title'       => 'Sua phieu hoc phi',
            'order'       => $order,
            'leadOptions' => $this->orders->leadOptions(),
        ]);
        clear_old();
    }

    /** POST /orders/update */
    public function update(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if (!$this->orders->find($id)) {
            flash('error', 'Không tìm thấy phiếu.');
            redirect('/orders');
        }

        $result = $this->orders->update($id, $_POST);
        if (!$result['ok']) {
            set_old($_POST, $result['errors']);
            flash('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại.');
            redirect('/orders/edit?id=' . $id);
        }

        flash('success', 'Cập nhật phiếu thành công.');
        redirect('/orders');
    }

    /** POST /orders/delete - F2: xoa MEM (chi admin). */
    public function delete(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders');
        }
        require_can('soft_delete', '/orders'); // F11: admin + manager
        $id = (int) ($_POST['id'] ?? 0);
        $this->orders->delete($id);
        flash('success', 'Đã chuyển phiếu vào thùng rác.');
        redirect('/orders');
    }

    /**
     * POST /orders/bulk - F9: hanh dong hang loat (xoa mem / doi status).
     * Nhan id[] + bulk_action. CSRF + role-gated (server-side).
     */
    public function bulk(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders');
        }

        $ids    = (array) ($_POST['id'] ?? []);
        $action = (string) ($_POST['bulk_action'] ?? '');

        if (empty($ids)) {
            flash('error', 'Chưa chọn dòng nào.');
            redirect('/orders');
        }

        if ($action === 'delete') {
            require_can('soft_delete', '/orders'); // F11
            $n = $this->orders->bulkDelete($ids);
            flash('success', 'Đã chuyển ' . $n . ' phiếu vào thùng rác.');
        } elseif ($action === 'status') {
            require_can('status_change', '/orders'); // F11: admin + manager
            $status = (string) ($_POST['bulk_status'] ?? '');
            $n = $this->orders->bulkStatus($ids, $status);
            if ($n > 0) {
                flash('success', 'Đã đổi trạng thái ' . $n . ' phiếu -> ' . $status . '.');
            } else {
                flash('error', 'Trạng thái không hợp lệ hoặc không có dòng nào thay đổi.');
            }
        } else {
            flash('error', 'Hành động hàng loạt không hợp lệ.');
        }
        redirect('/orders');
    }

    /** GET /orders/trash - F2: phieu da xoa mem (admin + manager). */
    public function trash(): void
    {
        require_login();
        require_can('trash', '/orders'); // F11: admin + manager
        echo render('orders/trash', [
            'title' => 'Thung rac Phieu hoc phi',
            'rows'  => $this->orders->trash(),
        ]);
        clear_old();
    }

    /** POST /orders/restore - F2: khoi phuc (chi admin). */
    public function restore(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders/trash');
        }
        require_can('restore', '/orders'); // F11: admin + manager
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->orders->findWithTrashed($id)) {
            $this->orders->restore($id);
            flash('success', 'Đã khôi phục phiếu.');
        } else {
            flash('error', 'Không tìm thấy phiếu cần khôi phục.');
        }
        redirect('/orders/trash');
    }

    /** POST /orders/force-delete - F2: xoa VINH VIEN (chi admin). */
    public function forceDelete(): void
    {
        require_login();
        if (!verify_csrf()) {
            flash('error', 'Phiên làm việc không hợp lệ (CSRF).');
            redirect('/orders/trash');
        }
        require_can('force_delete', '/orders'); // F11: chi admin
        $id = (int) ($_POST['id'] ?? 0);
        $this->orders->forceDelete($id);
        flash('success', 'Đã xóa vĩnh viễn phiếu.');
        redirect('/orders/trash');
    }

    /** GET /orders/invoice?id=.. - F10: trang hoa don in duoc (print CSS). */
    public function invoice(): void
    {
        require_login();
        $id    = (int) ($_GET['id'] ?? 0);
        $order = $this->orders->findForInvoice($id);
        if (!$order) {
            flash('error', 'Không tìm thấy phiếu để in hóa đơn.');
            redirect('/orders');
        }
        // Layout rieng (khong sidebar) de in sach. Khong dung layout 'main'.
        echo render('orders/invoice', [
            'title' => 'Hoa don ' . $order['order_code'],
            'order' => $order,
        ], 'layouts/invoice');
    }

    /** GET /orders/export - F4: stream CSV theo dung filter hien tai. */
    public function export(): void
    {
        require_login();
        require_can('export', '/orders'); // F11: admin + manager
        $rows = $this->orders->exportRows($_GET);

        $headers = ['ID', 'Ma phieu', 'Hoc vien', 'Khoa hoc', 'Hoc phi', 'Trang thai', 'Ngay TT', 'Ghi chu', 'Ngay tao'];
        $data = array_map(fn($r) => [
            $r['id'], $r['order_code'], $r['lead_name'], $r['course'],
            $r['amount'], $r['status'], $r['paid_at'], $r['note'], $r['created_at'],
        ], $rows);

        Csv::stream('orders-' . date('Ymd-His') . '.csv', $headers, $data);
    }
}
