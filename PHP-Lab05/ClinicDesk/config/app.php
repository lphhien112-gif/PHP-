<?php

/**
 * ClinicDesk - Mini Clinic Appointment DB App (PHP Lab05)
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 * Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
 * Truong Dai hoc Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc
 *
 * config/app.php - Cau hinh ung dung.
 *
 * APP_DEBUG dieu khien viec co hien thi loi ky thuat hay khong.
 * Tren production luon de false: nguoi dung chi thay safe message,
 * SQLSTATE va stack trace duoc ghi vao storage/logs/app.log.
 */

return [
    'name'         => 'ClinicDesk',
    'tagline'      => 'Mini Clinic Appointment DB App',
    'version'      => '1.0.0',
    'env'          => getenv('APP_ENV') ?: 'production',
    // Bat APP_DEBUG=1 khi muon xem loi chi tiet luc phat trien.
    'debug'        => (getenv('APP_DEBUG') === '1'),
    'per_page'     => 5,   // So ban ghi moi trang (de phan trang co y nghia voi du lieu seed)
    'log_path'     => __DIR__ . '/../storage/logs/app.log',
    'timezone'     => 'Asia/Ho_Chi_Minh',
];
