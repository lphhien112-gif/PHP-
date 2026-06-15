<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;
use WorkshopHub\Support\Storage;
use WorkshopHub\Support\Validator;

/**
 * WorkshopHub — RegistrationController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * Mon hoc   : Lap trinh Web     — GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * Resource chinh: "registration".
 *   GET  /registrations         -> danh sach + flash.
 *   GET  /registrations/create  -> form dang ky.
 *   POST /registrations         -> validate + anti-spam + luu JSON + PRG.
 */
class RegistrationController
{
    /** Danh sach chu de workshop hop le (in-list validation). */
    public const TOPICS = [
        'Web Development',
        'Data Science',
        'UI/UX Design',
        'Mobile App',
        'Cyber Security',
    ];

    /** Cac ca tham du hop le (in-list validation). */
    public const SESSIONS = ['Morning', 'Afternoon', 'Evening'];

    /** Khoang cach toi thieu giua 2 lan submit thanh cong (giay). */
    public const RATE_LIMIT_SECONDS = 5;

    private function storage(): Storage
    {
        return new Storage('registrations.json');
    }

    /** GET /registrations — danh sach dang ky + flash success/error. */
    public function index(): void
    {
        $registrations = $this->storage()->all();
        // Hien moi nhat len dau.
        usort($registrations, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        Response::view('registrations/index', [
            'title'         => 'Danh sach dang ky — WorkshopHub',
            'registrations' => $registrations,
        ]);
    }

    /** GET /registrations/create — form dang ky (giu old input + errors). */
    public function create(): void
    {
        $defaultTopic = trim($_GET['topic'] ?? '');

        Response::view('registrations/create', [
            'title'        => 'Dang ky tham du Workshop',
            'topics'       => self::TOPICS,
            'sessions'     => self::SESSIONS,
            'defaultTopic' => $defaultTopic,
        ]);
        // Sau khi render xong moi xoa old input/errors.
        old_flush();
    }

    /**
     * POST /registrations — xu ly submit.
     * Thu tu: honeypot -> rate limit -> validate -> luu -> PRG redirect.
     */
    public function store(): void
    {
        // --- Doc input an toan: luon dung ?? va trim() ---
        $input = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'phone'     => trim($_POST['phone'] ?? ''),
            'topic'     => trim($_POST['topic'] ?? ''),
            'session'   => trim($_POST['session'] ?? ''),
            'notes'     => trim($_POST['notes'] ?? ''),
        ];
        $honeypot = trim($_POST['website'] ?? '');

        // --- ANTI-SPAM 1: Honeypot ---
        // Field "website" an voi nguoi dung. Bot thuong dien het field ->
        // neu co du lieu thi chan nhu hanh vi bot.
        if ($honeypot !== '') {
            flash_set('error', 'Yeu cau bi tu choi (phat hien hanh vi bot).');
            redirect('/registrations');
        }

        // --- ANTI-SPAM 2: Rate limit bang session ---
        // Khong cho gui thanh cong 2 lan trong RATE_LIMIT_SECONDS giay.
        $last = $_SESSION['_last_submit_at'] ?? 0;
        if ((time() - $last) < self::RATE_LIMIT_SECONDS) {
            flash_set('error', 'Ban gui qua nhanh. Vui long cho vai giay roi thu lai.');
            flash_keep_input($input, []);
            redirect('/registrations/create');
        }

        // --- VALIDATION server-side ---
        $v = new Validator($input);
        $v->required('full_name', 'Ho ten')
          ->minLength('full_name', 2, 'Ho ten')
          ->maxLength('full_name', 80, 'Ho ten');
        $v->required('email', 'Email')->email('email', 'Email');
        $v->required('phone', 'So dien thoai')->phone('phone', 'So dien thoai');
        $v->required('topic', 'Chu de')->inList('topic', self::TOPICS, 'Chu de');
        $v->required('session', 'Ca tham du')->inList('session', self::SESSIONS, 'Ca tham du');
        $v->maxLength('notes', 500, 'Ghi chu');

        if ($v->fails()) {
            // Giu lai old input + loi theo field, re-render form (qua redirect GET).
            flash_keep_input($input, $v->errors());
            flash_set('error', 'Vui long kiem tra lai cac thong tin con loi.');
            redirect('/registrations/create');
        }

        // --- Luu JSON ---
        $record = $this->storage()->insert([
            'full_name' => $input['full_name'],
            'email'     => $input['email'],
            'phone'     => $input['phone'],
            'topic'     => $input['topic'],
            'session'   => $input['session'],
            'notes'     => $input['notes'],
            'status'    => 'pending',
        ]);

        // Ghi nhan thoi diem submit thanh cong (cho rate limit).
        $_SESSION['_last_submit_at'] = time();

        // --- PRG: POST thanh cong -> redirect ve GET, KHONG render truc tiep ---
        flash_set('success', 'Dang ky thanh cong! Ma dang ky cua ban la #' . $record['id'] . '.');
        redirect('/registrations');
    }
}
