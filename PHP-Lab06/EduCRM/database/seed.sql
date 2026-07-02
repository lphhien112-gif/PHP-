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
TRUNCATE TABLE courses;
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

-- ===== SEED courses (Module C) - 8 khoa hoc gan dung 8 file anh =====
INSERT INTO courses (name, slug, category, level, price, duration_weeks, image, description, outcomes, is_active) VALUES
  ('IELTS Foundation', 'ielts-foundation', 'Ngoai ngu', 'beginner', 3500000, 10, 'ielts_foundation.png',
   'Khoa nen tang danh cho nguoi moi tiep can IELTS hoac con mat goc tieng Anh. Ban duoc cung co ngu phap loi, mo rong tu vung theo chu de va lam quen dinh dang 4 ky nang Nghe - Noi - Doc - Viet. Lo trinh huong toi band 4.5-5.5, tao ban dap vung chac truoc khi len trinh do cao hon.',
   'Nam vung 12 chu diem ngu phap cot loi cua IELTS\nXay dung 800+ tu vung hoc thuat theo chu de\nLam quen cau truc va cach tinh diem 4 ky nang\nTu tin lam bai thi thu dat band 4.5-5.5', 1),
  ('IELTS Advanced', 'ielts-advanced', 'Ngoai ngu', 'advanced', 4200000, 12, 'ielts_advanced.png',
   'Danh cho hoc vien da co nen tang, muon but pha len band 6.5+. Khoa tap trung chien luoc lam bai cho tung dang cau hoi, ky thuat Writing Task 1 & 2 va phan xa Speaking. Bao gom chua bai chi tiet va thi thu dinh ky sat de that.',
   'Chien luoc xu ly tung dang bai Reading & Listening\nViet Task 1 & Task 2 mach lac, du y, dung tu hoc thuat\nPhan xa Speaking tu nhien theo band 6.5-7.5\nThi thu dinh ky kem feedback ca nhan hoa', 1),
  ('Lap trinh Python', 'lap-trinh-python', 'Lap trinh', 'beginner', 5000000, 10, 'python_programming.png',
   'Nhap mon lap trinh voi Python - ngon ngu de hoc nhung manh me. Ban di tu bien, kieu du lieu, vong lap den ham va lap trinh huong doi tuong, ket hop nhieu bai tap thuc hanh. Phu hop nguoi chua tung viet dong code nao.',
   'Hieu bien, kieu du lieu, dieu kien va vong lap\nViet ham va to chuc chuong trinh ro rang\nNam khai niem OOP co ban (class, object)\nHoan thanh 1 mini project cuoi khoa', 1),
  ('Lap trinh Web', 'lap-trinh-web', 'Lap trinh', 'intermediate', 6500000, 12, 'web_programming.png',
   'Xay dung ung dung web hoan chinh tu giao dien den xu ly phia may chu. Khoa day HTML/CSS/JavaScript va PHP theo mo hinh MVC, ket noi CSDL MySQL kem cac nguyen tac bao mat can ban. Ket thuc khoa ban tu lam duoc mot he thong CRUD that.',
   'Dung giao dien voi HTML/CSS/JavaScript\nLap trinh PHP theo mo hinh MVC\nKet noi va thao tac CSDL MySQL an toan\nHoan thien 1 du an web CRUD trien khai duoc', 1),
  ('Data Science', 'data-science', 'Lap trinh', 'advanced', 7800000, 14, 'data_science.png',
   'Buoc vao the gioi khoa hoc du lieu: thu thap, lam sach, phan tich va truc quan hoa du lieu, tien toi machine learning co ban. Hoc qua bo du lieu that voi Python (pandas, matplotlib) va cac tinh huong kinh doanh thuc te.',
   'Lam sach va xu ly du lieu voi pandas\nTruc quan hoa du lieu de ke chuyen bang so lieu\nXay dung mo hinh machine learning co ban\nTrinh bay ket qua phan tich cho nguoi khong chuyen', 1),
  ('Tieng Nhat N5', 'tieng-nhat-n5', 'Ngoai ngu', 'beginner', 4000000, 12, 'japanese_n5.png',
   'Khoa tieng Nhat so cap huong toi trinh do JLPT N5. Ban hoc bang chu Hiragana - Katakana, 100+ Kanji co ban, mau cau giao tiep hang ngay va luyen nghe co ban. Phu hop nguoi bat dau tu con so 0.',
   'Doc viet thanh thao Hiragana va Katakana\nNho 100+ Kanji va 600+ tu vung N5\nGiao tiep cac tinh huong co ban hang ngay\nSan sang thi chung chi JLPT N5', 1),
  ('Thiet ke do hoa', 'thiet-ke-do-hoa', 'Thiet ke', 'beginner', 5500000, 10, 'graphic_design.png',
   'Khoa thiet ke do hoa tu nen tang: nguyen ly bo cuc, mau sac, typography va thuc hanh tren Photoshop & Illustrator. Ban se tao poster, banner va bo nhan dien thuong hieu don gian, tung buoc xay dung portfolio dau tay.',
   'Nam nguyen ly bo cuc, mau sac va typography\nThanh thao thao tac co ban Photoshop & Illustrator\nThiet ke poster, banner, an pham mang xa hoi\nHoan thien portfolio 3-5 san pham', 1),
  ('Digital Marketing', 'digital-marketing', 'Marketing', 'intermediate', 6000000, 8, 'digital_marketing.png',
   'Tong quan marketing so tu chien luoc den thuc thi: SEO, quang cao Facebook/Google Ads, content va do luong hieu qua. Khoa thien thuc chien voi cac chien dich mau va cong cu phan tich pho bien.',
   'Xay dung ke hoach marketing so co ban\nChay va toi uu quang cao Facebook/Google\nSan xuat content va lam SEO on-page\nDo luong hieu qua bang cac chi so chinh', 1);

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

-- ===== SEED du lieu GAN DAY (ngay dong theo NOW()) =====
-- Muc dich: bieu do "Lead theo ngay" (14 ngay) va "Doanh thu" (thang nay/thang
-- truoc) luon co du lieu bat ke hom nay la ngay nao -> khong trong nhu loi.
-- Lead rai deu trong 14 ngay gan nhat, nhieu nguon khac nhau (cho "Lead theo nguon").
INSERT INTO leads (full_name, email, phone, course, source, status, note, created_at) VALUES
  ('Vu Gan Day 01', 'recent01@educrm.vn', '0921000001', 'IELTS Foundation', 'website',  'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 0 DAY)),
  ('Vu Gan Day 02', 'recent02@educrm.vn', '0921000002', 'Lap trinh Python', 'facebook', 'contacted', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  ('Vu Gan Day 03', 'recent03@educrm.vn', '0921000003', 'Digital Marketing', 'zalo',     'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  ('Vu Gan Day 04', 'recent04@educrm.vn', '0921000004', 'Lap trinh Web',     'website',  'qualified', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 3 DAY)),
  ('Vu Gan Day 05', 'recent05@educrm.vn', '0921000005', 'Data Science',      'referral', 'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  ('Vu Gan Day 06', 'recent06@educrm.vn', '0921000006', 'Tieng Nhat N5',     'facebook', 'contacted', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 5 DAY)),
  ('Vu Gan Day 07', 'recent07@educrm.vn', '0921000007', 'IELTS Advanced',    'hotline',  'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 7 DAY)),
  ('Vu Gan Day 08', 'recent08@educrm.vn', '0921000008', 'Thiet ke do hoa',   'website',  'converted', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 8 DAY)),
  ('Vu Gan Day 09', 'recent09@educrm.vn', '0921000009', 'Lap trinh Python',  'zalo',     'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ('Vu Gan Day 10', 'recent10@educrm.vn', '0921000010', 'Digital Marketing', 'referral', 'contacted', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 11 DAY)),
  ('Vu Gan Day 11', 'recent11@educrm.vn', '0921000011', 'IELTS Foundation',  'facebook', 'new',       'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ('Vu Gan Day 12', 'recent12@educrm.vn', '0921000012', 'Lap trinh Web',     'website',  'qualified', 'Lead moi trong tuan', DATE_SUB(NOW(), INTERVAL 13 DAY));

-- Phieu da thu (paid) thang nay & thang truoc -> bieu do doanh thu + delta MoM.
INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note, created_at) VALUES
  ('ORD-2026-9001', 1,  'IELTS Foundation', 3500000, 'paid', CURRENT_DATE, 'Doanh thu thang nay',   CURRENT_DATE),
  ('ORD-2026-9002', 6,  'Tieng Nhat N5',    9000000, 'paid', CURRENT_DATE, 'Doanh thu thang nay',   CURRENT_DATE),
  ('ORD-2026-9003', 9,  'Lap trinh Web',    6500000, 'paid', CURRENT_DATE, 'Doanh thu thang nay',   CURRENT_DATE),
  ('ORD-2026-9004', 2,  'IELTS Advanced',   4200000, 'paid', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH), 'Doanh thu thang truoc', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH)),
  ('ORD-2026-9005', 14, 'Data Science',     7800000, 'paid', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH), 'Doanh thu thang truoc', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH));

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
