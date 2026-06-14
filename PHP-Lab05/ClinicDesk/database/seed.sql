-- =====================================================================
-- ClinicDesk - database/seed.sql
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- Du lieu mau: 1 user admin, 22 patients, 24 appointments.
-- Du de phan trang (per_page = 5) co y nghia va de chay EXPLAIN.
-- =====================================================================

USE clinic_appointment_db;

DELETE FROM appointments;
DELETE FROM patients;
DELETE FROM users;
ALTER TABLE appointments AUTO_INCREMENT = 1;
ALTER TABLE patients     AUTO_INCREMENT = 1;
ALTER TABLE users        AUTO_INCREMENT = 1;

-- ---- users ----
-- password_hash la hash cua 'admin123' (bcrypt) - dung de mo rong login sau nay.
INSERT INTO users (username, full_name, role, password_hash) VALUES
('admin', 'Le Pham Hong Hien', 'admin', '$2y$10$e0NRsdT5p7Vq1k0Q3xqv1uX0e3iJ9k7p3a3l7K1nQ9wXk6m2bF7Iu');

-- ---- patients (22 ban ghi) ----
INSERT INTO patients (name, email, phone, gender, address, created_at) VALUES
('Nguyen Van An',    'an.nguyen@example.com',    '0901000001', 'male',   '12 Le Loi, Quan 1, TP.HCM',      '2026-05-01 08:10:00'),
('Tran Thi Binh',    'binh.tran@example.com',    '0901000002', 'female', '34 Hai Ba Trung, Quan 3, TP.HCM','2026-05-02 09:15:00'),
('Le Van Cuong',     'cuong.le@example.com',     '0901000003', 'male',   '56 Nguyen Hue, Quan 1, TP.HCM',  '2026-05-03 10:20:00'),
('Pham Thi Dung',    'dung.pham@example.com',    '0901000004', 'female', '78 Tran Hung Dao, Quan 5, TP.HCM','2026-05-04 11:25:00'),
('Hoang Van Em',     'em.hoang@example.com',     '0901000005', 'male',   '90 Vo Van Tan, Quan 3, TP.HCM',  '2026-05-05 08:30:00'),
('Vo Thi Phuong',    'phuong.vo@example.com',    '0901000006', 'female', '11 Cach Mang Thang 8, Quan 10',  '2026-05-06 14:05:00'),
('Dang Van Giang',   'giang.dang@example.com',   '0901000007', 'male',   '22 Ly Thuong Kiet, Quan 11',     '2026-05-07 15:10:00'),
('Bui Thi Hoa',      'hoa.bui@example.com',      '0901000008', 'female', '33 Su Van Hanh, Quan 10',        '2026-05-08 16:15:00'),
('Do Van Hung',      'hung.do@example.com',      '0901000009', 'male',   '44 Ba Thang Hai, Quan 10',       '2026-05-09 08:40:00'),
('Ngo Thi Khanh',    'khanh.ngo@example.com',    '0901000010', 'female', '55 Nguyen Trai, Quan 5',         '2026-05-10 09:45:00'),
('Duong Van Long',   'long.duong@example.com',   '0901000011', 'male',   '66 An Duong Vuong, Quan 5',      '2026-05-11 10:50:00'),
('Ly Thi Mai',       'mai.ly@example.com',       '0901000012', 'female', '77 Hung Vuong, Quan 6',          '2026-05-12 11:55:00'),
('Phan Van Nam',     'nam.phan@example.com',     '0901000013', 'male',   '88 Kinh Duong Vuong, Quan 6',    '2026-05-13 13:00:00'),
('Truong Thi Oanh',  'oanh.truong@example.com',  '0901000014', 'female', '99 Hau Giang, Quan 6',           '2026-05-14 14:05:00'),
('Mai Van Phuc',     'phuc.mai@example.com',     '0901000015', 'male',   '101 Pham The Hien, Quan 8',      '2026-05-15 15:10:00'),
('Cao Thi Quyen',    'quyen.cao@example.com',    '0901000016', 'female', '102 Duong 3 Thang 2, Quan 11',   '2026-05-16 08:00:00'),
('Dinh Van Son',     'son.dinh@example.com',     '0901000017', 'male',   '103 Lac Long Quan, Quan Tan Binh','2026-05-17 09:05:00'),
('Ha Thi Trang',     'trang.ha@example.com',     '0901000018', 'female', '104 Cong Hoa, Quan Tan Binh',    '2026-05-18 10:10:00'),
('Vu Van Tuan',      'tuan.vu@example.com',      '0901000019', 'male',   '105 Truong Chinh, Quan Tan Phu', '2026-05-19 11:15:00'),
(' Trinh Thi Uyen',  'uyen.trinh@example.com',   '0901000020', 'female', '106 Au Co, Quan Tan Phu',        '2026-05-20 13:20:00'),
('Luong Van Vinh',   'vinh.luong@example.com',   '0901000021', 'male',   '107 Hoa Binh, Quan Tan Phu',     '2026-05-21 14:25:00'),
('Ton Nu Xuan',      'xuan.ton@example.com',     '0901000022', 'female', '108 Tan Ky Tan Quy, Quan Tan Phu','2026-05-22 15:30:00');

-- ---- appointments (24 ban ghi) ----
INSERT INTO appointments (appointment_code, patient_name, patient_email, department, appointment_date, status, note, created_at) VALUES
('APT-2026-0001', 'Nguyen Van An',   'an.nguyen@example.com',   'Noi tong quat',  '2026-06-12 08:00:00', 'confirmed', 'Tai kham huyet ap',       '2026-05-23 08:00:00'),
('APT-2026-0002', 'Tran Thi Binh',   'binh.tran@example.com',   'Tai Mui Hong',   '2026-06-12 09:00:00', 'pending',   'Dau hong keo dai',        '2026-05-23 09:00:00'),
('APT-2026-0003', 'Le Van Cuong',    'cuong.le@example.com',    'Da lieu',        '2026-06-12 10:00:00', 'completed', 'Di ung da',               '2026-05-23 10:00:00'),
('APT-2026-0004', 'Pham Thi Dung',   'dung.pham@example.com',   'San phu khoa',   '2026-06-13 08:30:00', 'confirmed', 'Kham dinh ky',            '2026-05-24 08:30:00'),
('APT-2026-0005', 'Hoang Van Em',    'em.hoang@example.com',    'Rang Ham Mat',   '2026-06-13 09:30:00', 'cancelled', 'Doi lich',                '2026-05-24 09:30:00'),
('APT-2026-0006', 'Vo Thi Phuong',   'phuong.vo@example.com',   'Mat',            '2026-06-13 10:30:00', 'pending',   'Kham can thi',            '2026-05-24 10:30:00'),
('APT-2026-0007', 'Dang Van Giang',  'giang.dang@example.com',  'Noi tong quat',  '2026-06-14 08:00:00', 'confirmed', 'Kham tong quat',          '2026-05-25 08:00:00'),
('APT-2026-0008', 'Bui Thi Hoa',     'hoa.bui@example.com',     'Tim mach',       '2026-06-14 09:00:00', 'pending',   'Hoi hop kho tho',         '2026-05-25 09:00:00'),
('APT-2026-0009', 'Do Van Hung',     'hung.do@example.com',     'Co xuong khop',  '2026-06-14 10:00:00', 'completed', 'Dau lung',                '2026-05-25 10:00:00'),
('APT-2026-0010', 'Ngo Thi Khanh',   'khanh.ngo@example.com',   'Noi tiet',       '2026-06-15 08:30:00', 'confirmed', 'Kiem tra duong huyet',    '2026-05-26 08:30:00'),
('APT-2026-0011', 'Duong Van Long',  'long.duong@example.com',  'Tieu hoa',       '2026-06-15 09:30:00', 'pending',   'Dau da day',              '2026-05-26 09:30:00'),
('APT-2026-0012', 'Ly Thi Mai',      'mai.ly@example.com',      'Da lieu',        '2026-06-15 10:30:00', 'completed', 'Tri mun',                 '2026-05-26 10:30:00'),
('APT-2026-0013', 'Phan Van Nam',    'nam.phan@example.com',    'Tai Mui Hong',   '2026-06-16 08:00:00', 'confirmed', 'Viem xoang',              '2026-05-27 08:00:00'),
('APT-2026-0014', 'Truong Thi Oanh', 'oanh.truong@example.com', 'Mat',            '2026-06-16 09:00:00', 'pending',   'Do mat',                  '2026-05-27 09:00:00'),
('APT-2026-0015', 'Mai Van Phuc',    'phuc.mai@example.com',    'Rang Ham Mat',   '2026-06-16 10:00:00', 'completed', 'Nho rang khon',           '2026-05-27 10:00:00'),
('APT-2026-0016', 'Cao Thi Quyen',   'quyen.cao@example.com',   'San phu khoa',   '2026-06-17 08:30:00', 'confirmed', 'Sieu am thai',            '2026-05-28 08:30:00'),
('APT-2026-0017', 'Dinh Van Son',    'son.dinh@example.com',    'Tim mach',       '2026-06-17 09:30:00', 'cancelled', 'Benh nhan ban',           '2026-05-28 09:30:00'),
('APT-2026-0018', 'Ha Thi Trang',    'trang.ha@example.com',    'Noi tong quat',  '2026-06-17 10:30:00', 'pending',   'Sot nhe',                 '2026-05-28 10:30:00'),
('APT-2026-0019', 'Vu Van Tuan',     'tuan.vu@example.com',     'Co xuong khop',  '2026-06-18 08:00:00', 'confirmed', 'Dau goi',                 '2026-05-29 08:00:00'),
('APT-2026-0020', 'Trinh Thi Uyen',  'uyen.trinh@example.com',  'Noi tiet',       '2026-06-18 09:00:00', 'pending',   'Met moi keo dai',         '2026-05-29 09:00:00'),
('APT-2026-0021', 'Luong Van Vinh',  'vinh.luong@example.com',  'Tieu hoa',       '2026-06-18 10:00:00', 'completed', 'Roi loan tieu hoa',       '2026-05-29 10:00:00'),
('APT-2026-0022', 'Ton Nu Xuan',     'xuan.ton@example.com',    'Da lieu',        '2026-06-19 08:30:00', 'confirmed', 'Kham not ruoi',           '2026-05-30 08:30:00'),
('APT-2026-0023', 'Nguyen Van An',   'an.nguyen@example.com',   'Tim mach',       '2026-06-19 09:30:00', 'pending',   'Kiem tra dien tim',       '2026-05-30 09:30:00'),
('APT-2026-0024', 'Tran Thi Binh',   'binh.tran@example.com',   'Mat',            '2026-06-19 10:30:00', 'confirmed', 'Do thi luc',              '2026-05-30 10:30:00');
