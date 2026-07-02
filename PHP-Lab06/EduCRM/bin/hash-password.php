<?php

/**
 * EduCRM - Sinh bcrypt hash cho mat khau (dung khi tao tai khoan that).
 * Cach dung (CLI):
 *   C:/xampp/php/php.exe bin/hash-password.php 'MatKhauCanHash'
 * In ra hash de dan vao cot users.password_hash
 * (xem database/production_hardening.sql).
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */

declare(strict_types=1);

// Chi cho chay tu dong lenh (chan truy cap qua web).
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$password = $argv[1] ?? '';
if ($password === '') {
    fwrite(STDERR, "Cach dung: php bin/hash-password.php 'MatKhauCanHash'\n");
    exit(1);
}

echo password_hash($password, PASSWORD_BCRYPT), PHP_EOL;
