<?php

/**
 * EduCRM - Training Center CRM
 * config/database.php - Thong tin ket noi MySQL
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */

// Doc tu bien moi truong (.env) - production KHONG hardcode credentials.
// Neu khong co .env -> dung mac dinh localhost (XAMPP: root, khong mat khau).
return [
    'host'    => env('DB_HOST', '127.0.0.1'),
    'port'    => (int) env('DB_PORT', 3306),
    'name'    => env('DB_NAME', 'training_center_crm'),
    'user'    => env('DB_USER', 'root'),
    'pass'    => (string) env('DB_PASS', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
];
