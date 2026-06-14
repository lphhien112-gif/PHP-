<?php

namespace App\Services;

use App\Repositories\AuditRepository;

/**
 * EduCRM - AuditService (F3 - Nhat ky hoat dong)
 *
 * Diem vao duy nhat de ghi audit: log($action,$entity,$id,$summary).
 * Tu lay user_id (tu session) + IP (tu request) o tang Service - Repository
 * KHONG doc superglobal. Loi ghi audit KHONG duoc lam vo nghiep vu chinh
 * (boc try/catch + ghi log file).
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class AuditService
{
    private AuditRepository $repo;

    /** Cac action hop le (khop ENUM trong DB). */
    public const ACTIONS = [
        'create', 'update', 'delete', 'restore',
        'convert', 'login', 'logout', 'login_failed',
    ];

    /** Cac entity hop le cho filter. */
    public const ENTITIES = ['lead', 'order', 'auth'];

    public function __construct(?AuditRepository $repo = null)
    {
        $this->repo = $repo ?? new AuditRepository();
    }

    /**
     * Ghi 1 dong nhat ky. $userId tuy chon: mac dinh lay tu session.
     * Truyen $userId tuong minh khi context dac biet (vd login_failed -> null).
     */
    public function log(
        string $action,
        ?string $entity = null,
        ?int $entityId = null,
        ?string $summary = null,
        ?int $userId = -1
    ): void {
        if (!in_array($action, self::ACTIONS, true)) {
            return; // action la -> bo qua an toan
        }

        // -1 = "chua chi dinh" -> tu suy ra tu user dang dang nhap.
        if ($userId === -1) {
            $userId = current_user()['id'] ?? null;
        }

        $ip = $this->clientIp();

        try {
            $this->repo->insert($userId, $action, $entity, $entityId, $summary, $ip);
        } catch (\Throwable $e) {
            // Audit la phu tro: KHONG nem loi pha vo flow chinh, chi ghi log file.
            log_app('warning', 'Audit log failed: ' . $e->getMessage());
        }
    }

    /** Lay IP client (an toan, gioi han 45 ky tu cho IPv6). */
    private function clientIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        return $ip ? substr((string) $ip, 0, 45) : null;
    }

    /**
     * Trang /audit: phan trang + filter user/action/entity (admin-only).
     */
    public function list(array $query): array
    {
        $filters = [
            'user_id' => (int) ($query['user_id'] ?? 0) ?: null,
            'action'  => in_array(($query['action'] ?? ''), self::ACTIONS, true) ? $query['action'] : null,
            'entity'  => in_array(($query['entity'] ?? ''), self::ENTITIES, true) ? $query['entity'] : null,
        ];

        $perPage = (int) config('per_page', 10);
        $page    = max(1, (int) ($query['page'] ?? 1));

        $total      = $this->repo->countFiltered($filters);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        return [
            'rows'        => $this->repo->paginate($filters, $perPage, $offset),
            'total'       => $total,
            'page'        => $page,
            'perPage'     => $perPage,
            'totalPages'  => $totalPages,
            'userOptions' => $this->repo->userOptions(),
            'f_user'      => $filters['user_id'] ?? '',
            'f_action'    => $filters['action'] ?? '',
            'f_entity'    => $filters['entity'] ?? '',
            'actions'     => self::ACTIONS,
            'entities'    => self::ENTITIES,
        ];
    }
}
