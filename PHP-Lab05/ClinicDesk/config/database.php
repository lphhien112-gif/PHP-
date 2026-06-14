<?php

/**
 * ClinicDesk - Mini Clinic Appointment DB App (PHP Lab05)
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 *
 * config/database.php - Thong tin ket noi MySQL/MariaDB.
 *
 * Tach thong tin ket noi ra khoi Controller/View. Lop Database (app/Core/Database.php)
 * doc file nay de tao PDO. DSN co charset=utf8mb4; options bao gom:
 *   - ERRMODE_EXCEPTION       -> nem PDOException khi co loi
 *   - DEFAULT_FETCH_MODE      -> FETCH_ASSOC (chi tra mang keys)
 *   - ATTR_EMULATE_PREPARES   -> false (dung prepared that su o tang MySQL)
 */

return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => getenv('DB_PORT') ?: '3306',
    'dbname'  => getenv('DB_NAME') ?: 'clinic_appointment_db',
    'user'    => getenv('DB_USER') ?: 'root',
    // XAMPP mac dinh: root khong co mat khau.
    'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset' => 'utf8mb4',

    // Cac options PDO chuan theo yeu cau Lab05.
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
