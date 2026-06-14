<?php

namespace App\Services;

use App\Repositories\ApiTokenRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OrderRepository;
use App\Repositories\StatsRepository;

/**
 * EduCRM - ApiService (F7 - Read-only JSON API)
 *
 * Business rule cho API:
 *  - Xac thuc Bearer token (so HASH SHA-256 voi bang api_tokens).
 *  - Rate-limit theo IP (token bucket dua tren file, KHONG can session).
 *  - Pagination + filter (q/status) dung lai Repository (prepared/bound SQL).
 * Chi READ - khong co mutation.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class ApiService
{
    private ApiTokenRepository $tokens;
    private LeadRepository $leads;
    private OrderRepository $orders;
    private StatsRepository $stats;

    public function __construct(
        ?ApiTokenRepository $tokens = null,
        ?LeadRepository $leads = null,
        ?OrderRepository $orders = null,
        ?StatsRepository $stats = null
    ) {
        $this->tokens = $tokens ?? new ApiTokenRepository();
        $this->leads  = $leads ?? new LeadRepository();
        $this->orders = $orders ?? new OrderRepository();
        $this->stats  = $stats ?? new StatsRepository();
    }

    /**
     * Doc bearer token tu header Authorization. Tra chuoi token tho hoac null.
     */
    public function bearerToken(): ?string
    {
        $header = '';
        // php -S dua header Authorization vao HTTP_AUTHORIZATION (qua getallheaders).
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) {
                    $header = (string) $v;
                    break;
                }
            }
        }
        if ($header === '') {
            $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        }
        if ($header === '' || stripos($header, 'Bearer ') !== 0) {
            return null;
        }
        $token = trim(substr($header, 7));
        return $token !== '' ? $token : null;
    }

    /** Token co hop le khong (so HASH). */
    public function isValidToken(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }
        return $this->tokens->existsByHash(hash('sha256', $token));
    }

    /** IP client (an toan). */
    private function clientIp(): string
    {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }

    /**
     * Rate-limit theo IP (file-based sliding bucket). Tra:
     *   ['ok'=>bool, 'remaining'=>int, 'retry_after'=>int]
     */
    public function rateLimit(): array
    {
        $limit  = (int) config('api_rate_limit', 60);
        $window = (int) config('api_rate_window', 60);
        $dir    = dirname((string) config('log_file')) . '/ratelimit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $file = $dir . '/api_' . md5($this->clientIp()) . '.json';

        $now  = time();
        $hits = [];
        if (is_file($file)) {
            $data = json_decode((string) @file_get_contents($file), true);
            if (is_array($data)) {
                // Giu lai cac hit con trong cua so.
                foreach ($data as $ts) {
                    if (is_int($ts) && $ts > $now - $window) {
                        $hits[] = $ts;
                    }
                }
            }
        }

        if (count($hits) >= $limit) {
            $retry = $window - ($now - min($hits));
            return ['ok' => false, 'remaining' => 0, 'retry_after' => max(1, $retry)];
        }

        $hits[] = $now;
        @file_put_contents($file, json_encode($hits), LOCK_EX);
        return ['ok' => true, 'remaining' => $limit - count($hits), 'retry_after' => 0];
    }

    /** Chuan hoa tham so phan trang chung cho API. */
    private function paging(array $query): array
    {
        $perPage = (int) ($query['per_page'] ?? config('api_per_page', 20));
        $max     = (int) config('api_max_per_page', 100);
        if ($perPage < 1) {
            $perPage = (int) config('api_per_page', 20);
        }
        if ($perPage > $max) {
            $perPage = $max;
        }
        $page = max(1, (int) ($query['page'] ?? 1));
        return [$page, $perPage];
    }

    /** GET /api/leads - read-only list + filter + pagination. */
    public function leads(array $query): array
    {
        $q      = trim((string) ($query['q'] ?? ''));
        $status = (string) ($query['status'] ?? '');
        if ($status !== '' && !in_array($status, config('lead_statuses', []), true)) {
            $status = '';
        }
        [$page, $perPage] = $this->paging($query);

        $total      = $this->leads->countFiltered($q, $status);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $rows   = $this->leads->paginate($q, $status, 'created_at', 'desc', $perPage, $offset);

        $data = array_map(fn($r) => [
            'id'         => (int) $r['id'],
            'full_name'  => $r['full_name'],
            'email'      => $r['email'],
            'phone'      => $r['phone'],
            'course'     => $r['course'],
            'source'     => $r['source'],
            'status'     => $r['status'],
            'created_at' => $r['created_at'],
        ], $rows);

        return $this->paginated($data, $total, $page, $perPage, $totalPages, ['q' => $q, 'status' => $status]);
    }

    /** GET /api/orders - read-only list + filter + pagination. */
    public function orders(array $query): array
    {
        $q      = trim((string) ($query['q'] ?? ''));
        $status = (string) ($query['status'] ?? '');
        if ($status !== '' && !in_array($status, config('order_statuses', []), true)) {
            $status = '';
        }
        [$page, $perPage] = $this->paging($query);

        $total      = $this->orders->countFiltered($q, $status);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $rows   = $this->orders->paginate($q, $status, 'created_at', 'desc', $perPage, $offset);

        $data = array_map(fn($r) => [
            'id'         => (int) $r['id'],
            'order_code' => $r['order_code'],
            'lead_id'    => (int) $r['lead_id'],
            'lead_name'  => $r['lead_name'],
            'course'     => $r['course'],
            'amount'     => (float) $r['amount'],
            'status'     => $r['status'],
            'paid_at'    => $r['paid_at'],
            'created_at' => $r['created_at'],
        ], $rows);

        return $this->paginated($data, $total, $page, $perPage, $totalPages, ['q' => $q, 'status' => $status]);
    }

    /** GET /api/stats - tong hop so lieu (read-only). */
    public function stats(): array
    {
        return [
            'leads' => [
                'total'     => $this->leads->total(),
                'by_status' => $this->leads->countByStatus(),
            ],
            'orders' => [
                'total'     => $this->orders->total(),
                'by_status' => $this->orders->countByStatus(),
                'revenue_paid' => $this->orders->paidRevenue(),
            ],
            'funnel'      => $this->stats->conversionFunnel(),
            'top_courses' => $this->stats->topCourses(5),
            'generated_at' => date('c'),
        ];
    }

    /** Khung tra ve chuan cho danh sach co phan trang. */
    private function paginated(array $data, int $total, int $page, int $perPage, int $totalPages, array $filters): array
    {
        return [
            'data' => $data,
            'meta' => [
                'total'       => $total,
                'page'        => $page,
                'per_page'    => $perPage,
                'total_pages' => $totalPages,
                'filters'     => $filters,
            ],
        ];
    }
}
