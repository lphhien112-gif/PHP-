<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\LeadRepository;
use App\Core\DuplicateRecordException;

/**
 * EduCRM - OrderService (Module B)
 *
 * Business rule cua order/phieu hoc phi: validate, sinh order_code, phan trang,
 * sort whitelist, duplicate handling. Controller mong; Repository chi SQL.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class OrderService
{
    private OrderRepository $repo;
    private LeadRepository $leads;
    private AuditService $audit;

    private const SORTABLE = ['id', 'order_code', 'course', 'amount', 'status', 'created_at'];

    public function __construct(
        ?OrderRepository $repo = null,
        ?LeadRepository $leads = null,
        ?AuditService $audit = null
    ) {
        $this->repo  = $repo ?? new OrderRepository();
        $this->leads = $leads ?? new LeadRepository();
        $this->audit = $audit ?? new AuditService();
    }

    /** Tim phieu ke ca da xoa mem (cho trash/restore/force-delete). */
    public function findWithTrashed(int $id): ?array
    {
        return $this->repo->findWithTrashed($id);
    }

    /** F2: danh sach phieu trong thung rac. */
    public function trash(): array
    {
        return $this->repo->trash();
    }

    /** F8: chuan hoa khoang ngay (YYYY-MM-DD) tu query. Gia tri sai -> bo. */
    private function dateRange(array $query): array
    {
        $valid = function ($v): string {
            $v = trim((string) $v);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
        };
        return [
            'created_from' => $valid($query['created_from'] ?? ''),
            'created_to'   => $valid($query['created_to'] ?? ''),
        ];
    }

    /** F4: lay du lieu CSV theo dung filter hien tai (gom F8 date-range). */
    public function exportRows(array $query): array
    {
        $q      = trim((string) ($query['q'] ?? ''));
        $status = (string) ($query['status'] ?? '');
        if ($status !== '' && !in_array($status, config('order_statuses', []), true)) {
            $status = '';
        }
        return $this->repo->exportFiltered($q, $status, $this->dateRange($query));
    }

    /**
     * F1: cong khai validate + sanitize cho LeadService::convertToOrder()
     * tai su dung dung business-rule order (khong nhan ban logic).
     */
    public function validateForConvert(array $input): array
    {
        return $this->validate($input);
    }

    public function sanitizeForConvert(array $input): array
    {
        return $this->sanitize($input);
    }

    public function list(array $query): array
    {
        $q       = trim((string) ($query['q'] ?? ''));
        $status  = (string) ($query['status'] ?? '');
        $sort    = (string) ($query['sort'] ?? 'created_at');
        $dir     = (string) ($query['direction'] ?? 'desc');
        $page    = (int) ($query['page'] ?? 1);

        // F9: page-size whitelist (10/20/50).
        $options = config('per_page_options', [10, 20, 50]);
        $perPage = (int) ($query['per_page'] ?? config('per_page', 10));
        if (!in_array($perPage, $options, true)) {
            $perPage = (int) config('per_page', 10);
        }

        if (!in_array($sort, self::SORTABLE, true)) {
            $sort = 'created_at';
        }
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        if ($status !== '' && !in_array($status, config('order_statuses', []), true)) {
            $status = '';
        }

        // F8: khoang ngay (validated).
        $dates = $this->dateRange($query);

        $total      = $this->repo->countFiltered($q, $status, $dates);
        $totalPages = max(1, (int) ceil($total / $perPage));

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $rows   = $this->repo->paginate($q, $status, $sort, $dir, $perPage, $offset, $dates);

        return [
            'rows'         => $rows,
            'total'        => $total,
            'page'         => $page,
            'perPage'      => $perPage,
            'perPageOptions' => $options,
            'totalPages'   => $totalPages,
            'q'            => $q,
            'status'       => $status,
            'sort'         => $sort,
            'direction'    => $dir,
            'created_from' => $dates['created_from'],
            'created_to'   => $dates['created_to'],
        ];
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    /** F10: phieu + thong tin hoc vien day du de in hoa don. */
    public function findForInvoice(int $id): ?array
    {
        return $this->repo->findForInvoice($id);
    }

    public function leadOptions(): array
    {
        return $this->leads->options();
    }

    public function validate(array $input, ?int $ignoreId = null): array
    {
        $errors = [];

        $code = trim((string) ($input['order_code'] ?? ''));
        if ($code === '') {
            $errors['order_code'] = 'Vui long nhap ma phieu.';
        } elseif (!preg_match('/^[A-Za-z0-9\-]{4,30}$/', $code)) {
            $errors['order_code'] = 'Ma phieu chi gom chu, so va dau gach, dai 4-30 ky tu.';
        }

        $leadId = (int) ($input['lead_id'] ?? 0);
        if ($leadId <= 0 || !$this->leads->find($leadId)) {
            $errors['lead_id'] = 'Vui long chon hoc vien hop le.';
        }

        $course = trim((string) ($input['course'] ?? ''));
        if (!in_array($course, course_names(), true)) {
            $errors['course'] = 'Vui long chon khoa hoc hop le.';
        }

        $amountRaw = (string) ($input['amount'] ?? '');
        if ($amountRaw === '' || !is_numeric($amountRaw)) {
            $errors['amount'] = 'Hoc phi phai la so.';
        } elseif ((float) $amountRaw < 0) {
            $errors['amount'] = 'Hoc phi khong duoc am.';
        } elseif ((float) $amountRaw > 999999999) {
            $errors['amount'] = 'Hoc phi vuot qua gioi han cho phep.';
        }

        $status = trim((string) ($input['status'] ?? 'pending'));
        if (!in_array($status, config('order_statuses', []), true)) {
            $errors['status'] = 'Trang thai phieu khong hop le.';
        }

        $paid = trim((string) ($input['paid_at'] ?? ''));
        if ($paid !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $paid)) {
            $errors['paid_at'] = 'Ngay thanh toan khong dung dinh dang (YYYY-MM-DD).';
        }

        $note = (string) ($input['note'] ?? '');
        if (mb_strlen($note) > 500) {
            $errors['note'] = 'Ghi chu toi da 500 ky tu.';
        }

        return $errors;
    }

    private function sanitize(array $input): array
    {
        return [
            'order_code' => strtoupper(trim((string) ($input['order_code'] ?? ''))),
            'lead_id'    => (int) ($input['lead_id'] ?? 0),
            'course'     => trim((string) ($input['course'] ?? '')),
            'amount'     => (float) ($input['amount'] ?? 0),
            'status'     => trim((string) ($input['status'] ?? 'pending')),
            'paid_at'    => trim((string) ($input['paid_at'] ?? '')),
            'note'       => trim((string) ($input['note'] ?? '')),
        ];
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
            $this->audit->log('create', 'order', $id, 'Tao phieu ' . $data['order_code']);
            return ['ok' => true, 'id' => $id];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    public function update(int $id, array $input): array
    {
        $errors = $this->validate($input, $id);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $data = $this->sanitize($input);
        try {
            $this->repo->update($id, $data);
            $this->audit->log('update', 'order', $id, 'Cap nhat phieu #' . $id);
            return ['ok' => true];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    /** F9: xoa MEM hang loat. Tra so dong da xoa. Ghi audit tom tat. */
    public function bulkDelete(array $ids): int
    {
        $n = $this->repo->bulkSoftDelete($ids);
        if ($n > 0) {
            $this->audit->log('delete', 'order', null, 'Xoa mem hang loat ' . $n . ' phieu');
        }
        return $n;
    }

    /** F9: doi trang thai hang loat. Whitelist status o tang Service. */
    public function bulkStatus(array $ids, string $status): int
    {
        if (!in_array($status, config('order_statuses', []), true)) {
            return 0;
        }
        $n = $this->repo->bulkUpdateStatus($ids, $status);
        if ($n > 0) {
            $this->audit->log('update', 'order', null, 'Doi trang thai hang loat ' . $n . ' phieu -> ' . $status);
        }
        return $n;
    }

    /** F2: xoa MEM phieu. */
    public function delete(int $id): void
    {
        $this->repo->softDelete($id);
        $this->audit->log('delete', 'order', $id, 'Xoa mem phieu #' . $id);
    }

    /** F2: khoi phuc phieu da xoa mem. */
    public function restore(int $id): void
    {
        $this->repo->restore($id);
        $this->audit->log('restore', 'order', $id, 'Khoi phuc phieu #' . $id);
    }

    /** F2: xoa VINH VIEN (chi goi khi controller da xac thuc role admin). */
    public function forceDelete(int $id): void
    {
        $this->repo->forceDelete($id);
        $this->audit->log('delete', 'order', $id, 'Xoa vinh vien phieu #' . $id);
    }

    /** Sinh goi y ma phieu moi: ORD-2026-XXXX. */
    public function suggestCode(): string
    {
        do {
            $code = 'ORD-' . date('Y') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while ($this->repo->existsCode($code));
        return $code;
    }

    public function stats(): array
    {
        return [
            'total'    => $this->repo->total(),
            'revenue'  => $this->repo->paidRevenue(),
            'byStatus' => $this->repo->countByStatus(),
        ];
    }
}
