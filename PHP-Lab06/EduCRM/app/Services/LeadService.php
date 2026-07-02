<?php

namespace App\Services;

use App\Repositories\LeadRepository;
use App\Repositories\OrderRepository;
use App\Core\Database;
use App\Core\DuplicateRecordException;
use App\Core\ConvertConflictException;

/**
 * EduCRM - LeadService (Module A)
 *
 * Chua TOAN BO business rule cua lead:
 *   - validate server-side
 *   - chuan hoa & tinh toan phan trang (page clamping)
 *   - whitelist sort / direction
 *   - duplicate handling (bat DuplicateRecordException -> loi theo field)
 * Controller chi goi service; Repository chi lo SQL.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class LeadService
{
    private LeadRepository $repo;
    private AuditService $audit;

    /** Cot duoc phep sort (whitelist o tang Service). */
    private const SORTABLE = ['id', 'full_name', 'email', 'course', 'status', 'created_at'];

    public function __construct(?LeadRepository $repo = null, ?AuditService $audit = null)
    {
        $this->repo  = $repo ?? new LeadRepository();
        $this->audit = $audit ?? new AuditService();
    }

    /** Tim lead ke ca da xoa mem (cho trash/restore/force-delete). */
    public function findWithTrashed(int $id): ?array
    {
        return $this->repo->findWithTrashed($id);
    }

    /** F2: danh sach lead trong thung rac. */
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
        if ($status !== '' && !in_array($status, config('lead_statuses', []), true)) {
            $status = '';
        }
        return $this->repo->exportFiltered($q, $status, $this->dateRange($query));
    }

    /**
     * Xay du lieu cho trang danh sach: search + filter + sort + pagination an toan.
     * $query la mang tham so tho tu $_GET (da do Controller chuyen vao).
     */
    public function list(array $query): array
    {
        $q       = trim((string) ($query['q'] ?? ''));
        $status  = (string) ($query['status'] ?? '');
        $sort    = (string) ($query['sort'] ?? 'created_at');
        $dir     = (string) ($query['direction'] ?? 'desc');
        $page    = (int) ($query['page'] ?? 1);

        // F9: page-size whitelist (10/20/50). Gia tri la -> mac dinh.
        $options = config('per_page_options', [10, 20, 50]);
        $perPage = (int) ($query['per_page'] ?? config('per_page', 10));
        if (!in_array($perPage, $options, true)) {
            $perPage = (int) config('per_page', 10);
        }

        // Whitelist sort/direction: gia tri la -> fallback an toan, KHONG loi SQL.
        if (!in_array($sort, self::SORTABLE, true)) {
            $sort = 'created_at';
        }
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        // Filter status chi chap nhan gia tri trong whitelist.
        if ($status !== '' && !in_array($status, config('lead_statuses', []), true)) {
            $status = '';
        }

        // F8: khoang ngay (validated).
        $dates = $this->dateRange($query);

        // Dem tong + tinh so trang
        $total      = $this->repo->countFiltered($q, $status, $dates);
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Page clamping: am / 0 -> 1 ; qua lon -> totalPages
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

    /**
     * Validate du lieu lead (dung chung cho create / update / public form).
     * Tra mang loi theo field (rong = hop le).
     */
    public function validate(array $input): array
    {
        $errors = [];

        $name = trim((string) ($input['full_name'] ?? ''));
        if ($name === '') {
            $errors['full_name'] = 'Vui long nhap ho ten.';
        } elseif (mb_strlen($name) > 120) {
            $errors['full_name'] = 'Ho ten toi da 120 ky tu.';
        }

        $email = trim((string) ($input['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Vui long nhap email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email khong dung dinh dang.';
        } elseif (mb_strlen($email) > 150) {
            $errors['email'] = 'Email toi da 150 ky tu.';
        }

        $phone = trim((string) ($input['phone'] ?? ''));
        if ($phone === '') {
            $errors['phone'] = 'Vui long nhap so dien thoai.';
        } elseif (!preg_match('/^0\d{9,10}$/', $phone)) {
            $errors['phone'] = 'So dien thoai phai gom 10-11 chu so, bat dau bang 0.';
        }

        $course = trim((string) ($input['course'] ?? ''));
        if (!in_array($course, course_names(), true)) {
            $errors['course'] = 'Vui long chon mot khoa hoc hop le.';
        }

        $source = trim((string) ($input['source'] ?? 'website'));
        if (!in_array($source, config('lead_sources', []), true)) {
            $errors['source'] = 'Nguon lead khong hop le.';
        }

        // status chi bat buoc o form noi bo (create/edit), public form mac dinh 'new'
        if (isset($input['status'])) {
            $status = trim((string) $input['status']);
            if (!in_array($status, config('lead_statuses', []), true)) {
                $errors['status'] = 'Trang thai khong hop le.';
            }
        }

        $note = (string) ($input['note'] ?? '');
        if (mb_strlen($note) > 500) {
            $errors['note'] = 'Ghi chu toi da 500 ky tu.';
        }

        return $errors;
    }

    /**
     * Chuan hoa input -> mang du lieu sach cho Repository.
     */
    private function sanitize(array $input, string $defaultStatus = 'new'): array
    {
        return [
            'full_name' => trim((string) ($input['full_name'] ?? '')),
            'email'     => mb_strtolower(trim((string) ($input['email'] ?? ''))),
            'phone'     => trim((string) ($input['phone'] ?? '')),
            'course'    => trim((string) ($input['course'] ?? '')),
            'source'    => trim((string) ($input['source'] ?? 'website')),
            'status'    => trim((string) ($input['status'] ?? $defaultStatus)),
            'note'      => trim((string) ($input['note'] ?? '')),
        ];
    }

    /**
     * Tao lead. Tra:
     *   ['ok'=>true,'id'=>N] hoac ['ok'=>false,'errors'=>[...]]
     */
    public function create(array $input, string $defaultStatus = 'new'): array
    {
        $errors = $this->validate($input);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $data = $this->sanitize($input, $defaultStatus);
        try {
            $id = $this->repo->create($data);
            $this->audit->log('create', 'lead', $id, 'Tao lead #' . $id . ' ' . $data['full_name']);
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

        $data = $this->sanitize($input, 'new');
        try {
            $this->repo->update($id, $data);
            $this->audit->log('update', 'lead', $id, 'Cap nhat lead #' . $id);
            return ['ok' => true];
        } catch (DuplicateRecordException $e) {
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        }
    }

    /**
     * F9: xoa MEM hang loat. Tra so dong da xoa. Ghi audit tom tat.
     */
    public function bulkDelete(array $ids): int
    {
        $n = $this->repo->bulkSoftDelete($ids);
        if ($n > 0) {
            $this->audit->log('delete', 'lead', null, 'Xoa mem hang loat ' . $n . ' lead');
        }
        return $n;
    }

    /**
     * F9: doi trang thai hang loat. Whitelist status o tang Service. Tra so dong.
     */
    public function bulkStatus(array $ids, string $status): int
    {
        if (!in_array($status, config('lead_statuses', []), true)) {
            return 0;
        }
        $n = $this->repo->bulkUpdateStatus($ids, $status);
        if ($n > 0) {
            $this->audit->log('update', 'lead', null, 'Doi trang thai hang loat ' . $n . ' lead -> ' . $status);
        }
        return $n;
    }

    /** F2: xoa MEM lead (giu lai trong thung rac). */
    public function delete(int $id): void
    {
        $this->repo->softDelete($id);
        $this->audit->log('delete', 'lead', $id, 'Xoa mem lead #' . $id);
    }

    /** F2: khoi phuc lead da xoa mem. */
    public function restore(int $id): void
    {
        $this->repo->restore($id);
        $this->audit->log('restore', 'lead', $id, 'Khoi phuc lead #' . $id);
    }

    /** F2: xoa VINH VIEN (chi goi khi controller da xac thuc role admin). */
    public function forceDelete(int $id): void
    {
        $this->repo->forceDelete($id);
        $this->audit->log('delete', 'lead', $id, 'Xoa vinh vien lead #' . $id);
    }

    /**
     * F1: Chuyen lead thanh phieu thu (order) trong 1 TRANSACTION.
     *   - INSERT order + UPDATE leads.status='converted'
     *   - Rollback ca hai neu trung order_code (DuplicateRecordException).
     * Tra ['ok'=>true,'id'=>orderId] hoac ['ok'=>false,'errors'=>[...]].
     *
     * $validate la callable($input):array tra mang loi (do OrderService cung cap)
     * de tach business-rule order khoi lead.
     */
    public function convertToOrder(int $leadId, array $input, callable $validate, callable $sanitize): array
    {
        $lead = $this->repo->find($leadId);
        if (!$lead) {
            return ['ok' => false, 'errors' => ['lead_id' => 'Khong tim thay lead.']];
        }
        if (in_array($lead['status'], ['converted', 'lost'], true)) {
            return ['ok' => false, 'errors' => ['lead_id' => 'Lead nay da chuyen doi hoac da mat, khong the tao phieu.']];
        }

        $errors = $validate($input);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $data = $sanitize($input);

        $orderRepo = new OrderRepository();
        $db        = Database::connection();

        try {
            $db->beginTransaction();
            $orderId = $orderRepo->insert($data);          // co the nem Duplicate
            // m2: doi trang thai NGUYEN TU - chi thanh cong khi lead chua
            // converted/lost. Neu 0 dong (double-submit) -> nem de rollback,
            // tranh tao 2 phieu cho cung 1 lead.
            if (!$this->repo->markConvertedGuarded($leadId)) {
                throw new ConvertConflictException('Lead nay vua duoc chuyen doi - khong the tao phieu lan nua.');
            }
            $db->commit();

            $this->audit->log(
                'convert',
                'lead',
                $leadId,
                'Chuyen lead #' . $leadId . ' thanh phieu ' . $data['order_code'] . ' (order #' . $orderId . ')'
            );
            return ['ok' => true, 'id' => $orderId];
        } catch (DuplicateRecordException $e) {
            if ($db->inTransaction()) {
                $db->rollBack(); // rollback CA order LAN doi status lead
            }
            return ['ok' => false, 'errors' => [$e->field() => $e->getMessage()]];
        } catch (ConvertConflictException $e) {
            // m2: double-submit / race -> rollback va bao loi than thien.
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['ok' => false, 'errors' => ['lead_id' => $e->getMessage()]];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function stats(): array
    {
        return [
            'total'    => $this->repo->total(),
            'byStatus' => $this->repo->countByStatus(),
        ];
    }
}
