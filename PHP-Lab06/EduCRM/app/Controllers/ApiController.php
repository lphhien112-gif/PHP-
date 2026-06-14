<?php

namespace App\Controllers;

use App\Services\ApiService;

/**
 * EduCRM - ApiController (F7 - Read-only JSON API)
 *
 * THIN controller: xac thuc Bearer token + rate-limit (qua ApiService) ->
 * tra JSON. KHONG co SQL. Khong dung session (stateless auth).
 *
 *  GET /api/leads   - danh sach lead (filter q/status, pagination)
 *  GET /api/orders  - danh sach phieu (filter q/status, pagination)
 *  GET /api/stats   - tong hop so lieu
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class ApiController
{
    private ApiService $api;

    public function __construct()
    {
        $this->api = new ApiService();
    }

    /** Xuat 1 payload JSON + status code, dung script (stateless). */
    private function json(array $payload, int $code = 200, array $headers = []): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        foreach ($headers as $k => $v) {
            header($k . ': ' . $v);
        }
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Cong bao ve chung: rate-limit truoc, roi xac thuc Bearer token.
     * Tra true neu hop le; nguoc lai da xuat JSON loi + exit.
     */
    private function guard(): void
    {
        // 1) Rate-limit theo IP.
        $rl = $this->api->rateLimit();
        if (!$rl['ok']) {
            $this->json([
                'error'   => 'rate_limited',
                'message' => 'Qua nhieu yeu cau. Vui long thu lai sau.',
            ], 429, ['Retry-After' => (string) $rl['retry_after']]);
        }

        // 2) Bearer token.
        $token = $this->api->bearerToken();
        if (!$this->api->isValidToken($token)) {
            $this->json([
                'error'   => 'unauthorized',
                'message' => 'Thieu hoac sai Bearer token. Gui header: Authorization: Bearer <token>.',
            ], 401, ['WWW-Authenticate' => 'Bearer']);
        }
    }

    /** GET /api/leads */
    public function leads(): void
    {
        $this->guard();
        $this->json($this->api->leads($_GET));
    }

    /** GET /api/orders */
    public function orders(): void
    {
        $this->guard();
        $this->json($this->api->orders($_GET));
    }

    /** GET /api/stats */
    public function stats(): void
    {
        $this->guard();
        $this->json(['data' => $this->api->stats()]);
    }
}
