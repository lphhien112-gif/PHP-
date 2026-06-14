<?php

/**
 * ClinicDesk - database/seed_data.php (lam them - bonus)
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 *
 * Sinh tu dong 100-300 ban ghi de test pagination va EXPLAIN tren bang lon hon.
 * Cach chay:
 *   php database/seed_data.php           (mac dinh 150 patients, 250 appointments)
 *   php database/seed_data.php 200 300
 *
 * Script chay schema.sql + seed.sql truoc khong bat buoc; no chi INSERT them.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ClinicDesk\Core\Database;

$numPatients     = (int)($argv[1] ?? 150);
$numAppointments = (int)($argv[2] ?? 250);

$pdo = Database::connection();

$first = ['Nguyen','Tran','Le','Pham','Hoang','Vo','Dang','Bui','Do','Ngo','Duong','Ly','Phan','Truong','Mai','Cao','Dinh','Ha','Vu','Trinh'];
$mid   = ['Van','Thi','Huu','Ngoc','Minh','Quoc','Thanh','Hong','Gia','Bao'];
$last  = ['An','Binh','Cuong','Dung','Em','Phuong','Giang','Hoa','Hung','Khanh','Long','Mai','Nam','Oanh','Phuc','Quyen','Son','Trang','Tuan','Uyen','Vinh','Xuan'];
$depts = ['Noi tong quat','Tai Mui Hong','Da lieu','San phu khoa','Rang Ham Mat','Mat','Tim mach','Co xuong khop','Noi tiet','Tieu hoa'];
$statuses = ['pending','confirmed','completed','cancelled'];
$genders  = ['male','female','other'];

echo "Sinh $numPatients patients...\n";
$insP = $pdo->prepare(
    'INSERT INTO patients (name, email, phone, gender, address, created_at)
     VALUES (:n, :e, :p, :g, :a, :c)'
);
$ok = 0;
for ($i = 0; $i < $numPatients; $i++) {
    $name = $first[array_rand($first)] . ' ' . $mid[array_rand($mid)] . ' ' . $last[array_rand($last)];
    $email = 'gen' . $i . '_' . strtolower(bin2hex(random_bytes(3))) . '@example.com';
    $phone = '09' . str_pad((string)random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    $created = date('Y-m-d H:i:s', strtotime('-' . random_int(0, 120) . ' days'));
    try {
        $insP->execute([
            ':n' => $name, ':e' => $email, ':p' => $phone,
            ':g' => $genders[array_rand($genders)],
            ':a' => 'So ' . random_int(1, 300) . ', TP.HCM', ':c' => $created,
        ]);
        $ok++;
    } catch (\PDOException $e) {
        // bo qua trung email hiem gap
    }
}
echo "  -> Da them $ok patients.\n";

echo "Sinh $numAppointments appointments...\n";
$insA = $pdo->prepare(
    'INSERT INTO appointments
        (appointment_code, patient_name, patient_email, department, appointment_date, status, note, created_at)
     VALUES (:code, :pn, :pe, :d, :ad, :s, :note, :c)'
);
$ok = 0;
for ($i = 0; $i < $numAppointments; $i++) {
    $code = 'GEN-' . date('Y') . '-' . str_pad((string)($i + 1), 5, '0', STR_PAD_LEFT);
    $name = $first[array_rand($first)] . ' ' . $mid[array_rand($mid)] . ' ' . $last[array_rand($last)];
    $email = 'gen' . $i . '@example.com';
    $adate = date('Y-m-d H:i:s', strtotime('+' . random_int(0, 60) . ' days ' . random_int(8, 16) . ' hours'));
    $created = date('Y-m-d H:i:s', strtotime('-' . random_int(0, 90) . ' days'));
    try {
        $insA->execute([
            ':code' => $code, ':pn' => $name, ':pe' => $email,
            ':d' => $depts[array_rand($depts)], ':ad' => $adate,
            ':s' => $statuses[array_rand($statuses)],
            ':note' => 'Ghi chu tu dong #' . $i, ':c' => $created,
        ]);
        $ok++;
    } catch (\PDOException $e) {
        // bo qua trung code hiem gap
    }
}
echo "  -> Da them $ok appointments.\n";
echo "Hoan tat seed_data.php\n";
