<?php

/**
 * EduCRM - Training Center CRM
 * config/app.php - Cau hinh ung dung
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 * Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
 */

return [
    'name'        => 'EduCRM',
    'tagline'     => 'Training Center CRM',

    // Moi truong: 'local' (may dev) | 'production' (server that). Doc tu .env.
    'env'         => env('APP_ENV', 'production'),

    // debug=false: production - khong lo SQLSTATE / path / stack trace cho user.
    // Doc tu .env (APP_DEBUG). Mac dinh FALSE (an toan) khi khong co .env.
    'debug'       => (bool) env('APP_DEBUG', false),

    // Phien dang nhmap het han sau bao nhieu giay khong hoat dong (idle timeout).
    'session_idle_timeout' => 1800, // 30 phut

    // Rate limit form cong khai: so request toi da trong khoang thoi gian.
    'public_form_rate_limit'  => 5,    // toi da 5 lan
    'public_form_rate_window' => 300,  // trong 300 giay (5 phut)

    // Phan trang
    'per_page' => 10,
    // F9: cac page-size cho phep (whitelist) + huong sort
    'per_page_options' => [10, 20, 50],

    // F6a: Lockout dang nhap (chong brute-force)
    'login_max_attempts'  => 5,    // toi da 5 lan sai
    'login_attempt_window' => 600, // trong cua so 10 phut (600s)
    'login_lockout_time'  => 900,  // bi khoa 15 phut (900s)

    // F6b: Remember-me token
    'remember_cookie'   => 'eduremember',
    'remember_lifetime' => 1209600, // 14 ngay (giay)

    // F7: API
    'api_per_page'      => 20,   // mac dinh so dong/trang cho JSON API
    'api_max_per_page'  => 100,  // gioi han tren
    'api_rate_limit'    => 60,   // toi da 60 request / cua so
    'api_rate_window'   => 60,   // cua so 60 giay (rate-limit theo IP)

    // Duong dan file log
    'log_file' => __DIR__ . '/../storage/logs/app.log',

    // Danh sach khoa hoc hop le (whitelist) - dung cho form & validate
    'courses' => [
        'IELTS Foundation',
        'IELTS Advanced',
        'Lap trinh Python',
        'Lap trinh Web',
        'Data Science',
        'Tieng Nhat N5',
        'Thiet ke do hoa',
        'Digital Marketing',
    ],

    // Module C - Khoa hoc: whitelist nhom + trinh do (dung cho form & validate)
    'course_categories' => ['Ngoai ngu', 'Lap trinh', 'Thiet ke', 'Marketing', 'Khac'],
    'course_levels'     => ['beginner', 'intermediate', 'advanced'],

    // Nguon lead hop le (whitelist)
    'lead_sources' => ['website', 'facebook', 'zalo', 'referral', 'hotline'],

    // Trang thai lead / order hop le
    'lead_statuses'  => ['new', 'contacted', 'qualified', 'converted', 'lost'],
    'order_statuses' => ['pending', 'paid', 'refunded', 'cancelled'],
];
