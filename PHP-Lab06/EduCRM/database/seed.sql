-- =====================================================================
-- EduCRM - Training Center CRM | Seed data
-- Tai khoan demo: admin/admin123 (role admin), staff/staff123 (role staff)
-- 25 leads + 25 orders de test pagination/search/sort.
-- Chay: mysql -u root training_center_crm < database/seed.sql
-- =====================================================================
USE training_center_crm;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE activity_logs;
TRUNCATE TABLE orders;
TRUNCATE TABLE leads;
TRUNCATE TABLE users;
-- F6/F7: bang token (chi truncate neu da ton tai - bo qua loi neu chua migrate)
DELETE FROM api_tokens;
SET FOREIGN_KEY_CHECKS = 1;

-- ===== SEED users =====
-- admin/admin123, staff/staff123, manager/manager123 (F11 - role mo rong)
INSERT INTO users (username, password_hash, full_name, role) VALUES
  ('admin', '$2y$10$N5zlWzKH86FntlFZcguuT.A0H3gRV4CesaJ/xSD5NhoXlkdSuM8aS', 'Quan tri vien', 'admin'),
  ('manager', '$2y$10$nxNOfoIJS/aZ39pbCQSLV.4/1m/Az5wbuVTkoAM3oo3X.s2F3TzJu', 'Truong nhom kinh doanh', 'manager'),
  ('staff', '$2y$10$xA5shlhspFieGqGcxHUyK.6ug99F4TFl0KmjKMlHbzo2tESuCXwJO', 'Nhan vien tu van', 'staff');

-- ===== SEED api_tokens (F7) - demo bearer token read-only =====
-- Token tho (ghi trong README): EDUCRM-DEMO-API-TOKEN-2026
INSERT INTO api_tokens (token_hash, label) VALUES
  (SHA2('EDUCRM-DEMO-API-TOKEN-2026', 256), 'demo-readonly');

-- ===== SEED leads (25 dong) =====
INSERT INTO leads (full_name, email, phone, course, source, status, note, created_at) VALUES
  ('Nguyen Van An', 'nguyenvanan1@gmail.com', '0910000000', 'IELTS Foundation', 'website', 'new', 'Lead quan tam khoa IELTS Foundation', '2026-01-01 01:15:00'),
  ('Tran Thi Bich', 'tranthibich2@gmail.com', '0910000373', 'IELTS Advanced', 'facebook', 'contacted', 'Lead quan tam khoa IELTS Advanced', '2026-02-02 02:15:00'),
  ('Le Hoang Cuong', 'lehoangcuong3@gmail.com', '0910000746', 'Lap trinh Python', 'zalo', 'qualified', 'Lead quan tam khoa Lap trinh Python', '2026-03-03 03:15:00'),
  ('Pham Thu Dung', 'phamthudung4@gmail.com', '0910001119', 'Lap trinh Web', 'referral', 'converted', 'Lead quan tam khoa Lap trinh Web', '2026-04-04 04:15:00'),
  ('Hoang Minh Duc', 'hoangminhduc5@gmail.com', '0910001492', 'Data Science', 'hotline', 'lost', 'Lead quan tam khoa Data Science', '2026-05-05 05:15:00'),
  ('Vo Thi Hoa', 'vothihoa6@gmail.com', '0910001865', 'Tieng Nhat N5', 'website', 'new', 'Lead quan tam khoa Tieng Nhat N5', '2026-01-06 06:15:00'),
  ('Dang Van Khoa', 'dangvankhoa7@gmail.com', '0910002238', 'Thiet ke do hoa', 'facebook', 'contacted', 'Lead quan tam khoa Thiet ke do hoa', '2026-02-07 07:15:00'),
  ('Bui Thi Lan', 'buithilan8@gmail.com', '0910002611', 'Digital Marketing', 'zalo', 'qualified', 'Lead quan tam khoa Digital Marketing', '2026-03-08 08:15:00'),
  ('Do Quang Minh', 'doquangminh9@gmail.com', '0910002984', 'IELTS Foundation', 'referral', 'converted', 'Lead quan tam khoa IELTS Foundation', '2026-04-09 09:15:00'),
  ('Ngo Thi Nga', 'ngothinga10@gmail.com', '0910003357', 'IELTS Advanced', 'hotline', 'lost', 'Lead quan tam khoa IELTS Advanced', '2026-05-10 01:15:00'),
  ('Phan Van Phuc', 'phanvanphuc11@gmail.com', '0910003730', 'Lap trinh Python', 'website', 'new', 'Lead quan tam khoa Lap trinh Python', '2026-01-11 02:15:00'),
  ('Truong Thi Quyen', 'truongthiquyen12@gmail.com', '0910004103', 'Lap trinh Web', 'facebook', 'contacted', 'Lead quan tam khoa Lap trinh Web', '2026-02-12 03:15:00'),
  ('Ly Van Son', 'lyvanson13@gmail.com', '0910004476', 'Data Science', 'zalo', 'qualified', 'Lead quan tam khoa Data Science', '2026-03-13 04:15:00'),
  ('Mai Thi Trang', 'maithitrang14@gmail.com', '0910004849', 'Tieng Nhat N5', 'referral', 'converted', 'Lead quan tam khoa Tieng Nhat N5', '2026-04-14 05:15:00'),
  ('Cao Van Tuan', 'caovantuan15@gmail.com', '0910005222', 'Thiet ke do hoa', 'hotline', 'lost', 'Lead quan tam khoa Thiet ke do hoa', '2026-05-15 06:15:00'),
  ('Vu Thi Uyen', 'vuthiuyen16@gmail.com', '0910005595', 'Digital Marketing', 'website', 'new', 'Lead quan tam khoa Digital Marketing', '2026-01-16 07:15:00'),
  ('Ho Van Vinh', 'hovanvinh17@gmail.com', '0910005968', 'IELTS Foundation', 'facebook', 'contacted', 'Lead quan tam khoa IELTS Foundation', '2026-02-17 08:15:00'),
  ('Dinh Thi Xuan', 'dinhthixuan18@gmail.com', '0910006341', 'IELTS Advanced', 'zalo', 'qualified', 'Lead quan tam khoa IELTS Advanced', '2026-03-18 09:15:00'),
  ('Luong Van Yen', 'luongvanyen19@gmail.com', '0910006714', 'Lap trinh Python', 'referral', 'converted', 'Lead quan tam khoa Lap trinh Python', '2026-04-19 01:15:00'),
  ('Ta Thi Anh', 'tathianh20@gmail.com', '0910007087', 'Lap trinh Web', 'hotline', 'lost', 'Lead quan tam khoa Lap trinh Web', '2026-05-20 02:15:00'),
  ('Chu Van Bao', 'chuvanbao21@gmail.com', '0910007460', 'Data Science', 'website', 'new', 'Lead quan tam khoa Data Science', '2026-01-21 03:15:00'),
  ('Doan Thi Cam', 'doanthicam22@gmail.com', '0910007833', 'Tieng Nhat N5', 'facebook', 'contacted', 'Lead quan tam khoa Tieng Nhat N5', '2026-02-22 04:15:00'),
  ('Ha Van Dat', 'havandat23@gmail.com', '0910008206', 'Thiet ke do hoa', 'zalo', 'qualified', 'Lead quan tam khoa Thiet ke do hoa', '2026-03-23 05:15:00'),
  ('Kieu Thi En', 'kieuthien24@gmail.com', '0910008579', 'Digital Marketing', 'referral', 'converted', 'Lead quan tam khoa Digital Marketing', '2026-04-24 06:15:00'),
  ('Lam Van Phong', 'lamvanphong25@gmail.com', '0910008952', 'IELTS Foundation', 'hotline', 'lost', 'Lead quan tam khoa IELTS Foundation', '2026-05-25 07:15:00');

-- ===== SEED orders (25 dong) =====
INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note, created_at) VALUES
  ('ORD-2026-0001', 1, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phieu hoc phi IELTS Foundation', '2026-01-01 10:30:00'),
  ('ORD-2026-0002', 2, 'IELTS Advanced', 4200000, 'paid', '2026-02-02', 'Phieu hoc phi IELTS Advanced', '2026-02-02 11:30:00'),
  ('ORD-2026-0003', 3, 'Lap trinh Python', 5000000, 'refunded', NULL, 'Phieu hoc phi Lap trinh Python', '2026-03-03 12:30:00'),
  ('ORD-2026-0004', 4, 'Lap trinh Web', 6500000, 'cancelled', NULL, 'Phieu hoc phi Lap trinh Web', '2026-04-04 13:30:00'),
  ('ORD-2026-0005', 5, 'Data Science', 7800000, 'pending', NULL, 'Phieu hoc phi Data Science', '2026-05-05 10:30:00'),
  ('ORD-2026-0006', 6, 'Tieng Nhat N5', 9000000, 'paid', '2026-01-06', 'Phieu hoc phi Tieng Nhat N5', '2026-01-06 11:30:00'),
  ('ORD-2026-0007', 7, 'Thiet ke do hoa', 12000000, 'refunded', NULL, 'Phieu hoc phi Thiet ke do hoa', '2026-02-07 12:30:00'),
  ('ORD-2026-0008', 8, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phieu hoc phi Digital Marketing', '2026-03-08 13:30:00'),
  ('ORD-2026-0009', 9, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phieu hoc phi IELTS Foundation', '2026-04-09 10:30:00'),
  ('ORD-2026-0010', 10, 'IELTS Advanced', 4200000, 'paid', '2026-05-10', 'Phieu hoc phi IELTS Advanced', '2026-05-10 11:30:00'),
  ('ORD-2026-0011', 11, 'Lap trinh Python', 5000000, 'refunded', NULL, 'Phieu hoc phi Lap trinh Python', '2026-01-11 12:30:00'),
  ('ORD-2026-0012', 12, 'Lap trinh Web', 6500000, 'cancelled', NULL, 'Phieu hoc phi Lap trinh Web', '2026-02-12 13:30:00'),
  ('ORD-2026-0013', 13, 'Data Science', 7800000, 'pending', NULL, 'Phieu hoc phi Data Science', '2026-03-13 10:30:00'),
  ('ORD-2026-0014', 14, 'Tieng Nhat N5', 9000000, 'paid', '2026-04-14', 'Phieu hoc phi Tieng Nhat N5', '2026-04-14 11:30:00'),
  ('ORD-2026-0015', 15, 'Thiet ke do hoa', 12000000, 'refunded', NULL, 'Phieu hoc phi Thiet ke do hoa', '2026-05-15 12:30:00'),
  ('ORD-2026-0016', 16, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phieu hoc phi Digital Marketing', '2026-01-16 13:30:00'),
  ('ORD-2026-0017', 17, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phieu hoc phi IELTS Foundation', '2026-02-17 10:30:00'),
  ('ORD-2026-0018', 18, 'IELTS Advanced', 4200000, 'paid', '2026-03-18', 'Phieu hoc phi IELTS Advanced', '2026-03-18 11:30:00'),
  ('ORD-2026-0019', 19, 'Lap trinh Python', 5000000, 'refunded', NULL, 'Phieu hoc phi Lap trinh Python', '2026-04-19 12:30:00'),
  ('ORD-2026-0020', 20, 'Lap trinh Web', 6500000, 'cancelled', NULL, 'Phieu hoc phi Lap trinh Web', '2026-05-20 13:30:00'),
  ('ORD-2026-0021', 21, 'Data Science', 7800000, 'pending', NULL, 'Phieu hoc phi Data Science', '2026-01-21 10:30:00'),
  ('ORD-2026-0022', 22, 'Tieng Nhat N5', 9000000, 'paid', '2026-02-22', 'Phieu hoc phi Tieng Nhat N5', '2026-02-22 11:30:00'),
  ('ORD-2026-0023', 23, 'Thiet ke do hoa', 12000000, 'refunded', NULL, 'Phieu hoc phi Thiet ke do hoa', '2026-03-23 12:30:00'),
  ('ORD-2026-0024', 24, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phieu hoc phi Digital Marketing', '2026-04-24 13:30:00'),
  ('ORD-2026-0025', 25, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phieu hoc phi IELTS Foundation', '2026-05-25 10:30:00');

-- ===== SEED activity_logs (F3 - vai dong demo nhat ky) =====
INSERT INTO activity_logs (user_id, action, entity, entity_id, summary, ip, created_at) VALUES
  (1, 'login',   'auth',  NULL, 'admin dang nhap he thong',                 '127.0.0.1', '2026-06-01 08:00:00'),
  (1, 'create',  'lead',  1,    'Tao lead #1 Nguyen Van An',                '127.0.0.1', '2026-06-01 08:05:00'),
  (2, 'login',   'auth',  NULL, 'staff dang nhap he thong',                 '127.0.0.1', '2026-06-01 09:00:00'),
  (2, 'update',  'lead',  3,    'Cap nhat lead #3 -> qualified',            '127.0.0.1', '2026-06-01 09:10:00'),
  (1, 'create',  'order', 2,    'Tao phieu ORD-2026-0002',                  '127.0.0.1', '2026-06-02 10:30:00'),
  (1, 'convert', 'lead',  4,    'Chuyen lead #4 thanh phieu ORD-2026-0004', '127.0.0.1', '2026-06-02 10:35:00'),
  (1, 'delete',  'lead',  20,   'Xoa mem lead #20',                         '127.0.0.1', '2026-06-03 14:00:00'),
  (1, 'restore', 'lead',  20,   'Khoi phuc lead #20',                       '127.0.0.1', '2026-06-03 14:05:00'),
  (NULL, 'login_failed', 'auth', NULL, 'Dang nhap that bai cho username=hacker', '127.0.0.1', '2026-06-04 22:00:00'),
  (1, 'logout',  'auth',  NULL, 'admin dang xuat',                          '127.0.0.1', '2026-06-04 18:00:00');
