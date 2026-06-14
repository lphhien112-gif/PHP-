<?php

namespace App\Controllers;

use App\Services\LeadService;

/**
 * EduCRM - PublicLeadController
 *
 * Form cong khai dang ky tu van (khong can dang nhap). Co:
 *   - honeypot field (bay bot)
 *   - rate limit theo session
 *   - validate server-side + old input
 *   - PRG (redirect sau POST thanh cong)
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class PublicLeadController
{
    private LeadService $leads;

    public function __construct()
    {
        $this->leads = new LeadService();
    }

    /** GET /public-leads/create - hien form cong khai. */
    public function create(): void
    {
        echo render('public-leads/create', [
            'title'   => 'Dang ky tu van',
        ], 'layouts/public');
        clear_old();
    }

    /** POST /public-leads - anti-spam + validate + PRG. */
    public function store(): void
    {
        // --- 1. Honeypot: field an "website" phai rong. Bot thuong dien het field. ---
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            log_app('warning', 'Honeypot triggered on public lead form from IP=' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
            // Gia vo thanh cong de bot khong biet bi chan.
            flash('success', 'Cam on ban, chung toi se lien he som!');
            redirect('/public-leads/create');
        }

        // --- 2. Rate limit theo session: chong submit lien tuc ---
        if ($this->isRateLimited()) {
            flash('error', 'Ban gui qua nhanh. Vui long thu lai sau vai phut.');
            set_old($_POST, []);
            redirect('/public-leads/create');
        }

        // --- 3. Validate server-side (trim + required + email + phone + whitelist) ---
        $input = [
            'full_name' => $_POST['full_name'] ?? '',
            'email'     => $_POST['email'] ?? '',
            'phone'     => $_POST['phone'] ?? '',
            'course'    => $_POST['course'] ?? '',
            'source'    => 'website',
            'note'      => $_POST['note'] ?? '',
        ];

        $result = $this->leads->create($input, 'new');

        if (!$result['ok']) {
            set_old($input, $result['errors']);
            flash('error', 'Vui long kiem tra lai thong tin da nhap.');
            redirect('/public-leads/create');
        }

        // --- 4. Thanh cong: ghi moc thoi gian (rate limit) + PRG ---
        $this->recordSubmission();
        flash('success', 'Dang ky thanh cong! Chung toi se lien he tu van trong 24h.');
        redirect('/public-leads/create');
    }

    /**
     * Kiem tra rate limit: toi da N request trong cua so thoi gian (giay).
     */
    private function isRateLimited(): bool
    {
        $limit  = (int) config('public_form_rate_limit', 5);
        $window = (int) config('public_form_rate_window', 300);
        $now    = time();

        $hits = $_SESSION['_pub_hits'] ?? [];
        // Bo cac moc da ra ngoai cua so thoi gian.
        $hits = array_filter($hits, fn ($t) => ($now - $t) < $window);
        $_SESSION['_pub_hits'] = array_values($hits);

        return count($hits) >= $limit;
    }

    private function recordSubmission(): void
    {
        $_SESSION['_pub_hits'][] = time();
    }
}
