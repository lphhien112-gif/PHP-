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
  ('admin', '$2y$10$N5zlWzKH86FntlFZcguuT.A0H3gRV4CesaJ/xSD5NhoXlkdSuM8aS', 'Quản trị viên', 'admin'),
  ('manager', '$2y$10$nxNOfoIJS/aZ39pbCQSLV.4/1m/Az5wbuVTkoAM3oo3X.s2F3TzJu', 'Trưởng nhóm kinh doanh', 'manager'),
  ('staff', '$2y$10$xA5shlhspFieGqGcxHUyK.6ug99F4TFl0KmjKMlHbzo2tESuCXwJO', 'Nhân viên tư vấn', 'staff');

-- ===== SEED api_tokens (F7) - demo bearer token read-only =====
-- Token tho (ghi trong README): EDUCRM-DEMO-API-TOKEN-2026
INSERT INTO api_tokens (token_hash, label) VALUES
  (SHA2('EDUCRM-DEMO-API-TOKEN-2026', 256), 'demo-readonly');

-- ===== SEED courses (Module C) - 8 khoa hoc gan dung 8 file anh =====
INSERT INTO courses (name, slug, category, level, price, duration_weeks, image, description, outcomes, is_active) VALUES
  ('IELTS Foundation', 'ielts-foundation', 'Ngoại ngữ', 'beginner', 3500000, 10, 'ielts_foundation.png',
   'Khóa nền tảng dành cho người mới tiếp cận IELTS hoặc còn mất gốc tiếng Anh. Bạn được củng cố ngữ pháp lõi, mở rộng từ vựng theo chủ đề và làm quen định dạng 4 kỹ năng Nghe - Nói - Đọc - Viết. Lộ trình hướng tới band 4.5-5.5, tạo bàn đạp vững chắc trước khi lên trình độ cao hơn.',
   'Nắm vững 12 chủ điểm ngữ pháp cốt lõi của IELTS\nXây dựng 800+ từ vựng học thuật theo chủ đề\nLàm quen cấu trúc và cách tính điểm 4 kỹ năng\nTự tin làm bài thi thử đạt band 4.5-5.5', 1),
  ('IELTS Advanced', 'ielts-advanced', 'Ngoại ngữ', 'advanced', 4200000, 12, 'ielts_advanced.png',
   'Dành cho học viên đã có nền tảng, muốn bứt phá lên band 6.5+. Khóa tập trung chiến lược làm bài cho từng dạng câu hỏi, kỹ thuật Writing Task 1 & 2 và phản xạ Speaking. Bao gồm chữa bài chi tiết và thi thử định kỳ sát đề thật.',
   'Chiến lược xử lý từng dạng bài Reading & Listening\nViết Task 1 & Task 2 mạch lạc, đủ ý, dùng từ học thuật\nPhản xạ Speaking tự nhiên theo band 6.5-7.5\nThi thử định kỳ kèm feedback cá nhân hóa', 1),
  ('Lập trình Python', 'lap-trinh-python', 'Lập trình', 'beginner', 5000000, 10, 'python_programming.png',
   'Nhập môn lập trình với Python - ngôn ngữ dễ học nhưng mạnh mẽ. Bạn đi từ biến, kiểu dữ liệu, vòng lặp đến hàm và lập trình hướng đối tượng, kết hợp nhiều bài tập thực hành. Phù hợp người chưa từng viết dòng code nào.',
   'Hiểu biến, kiểu dữ liệu, điều kiện và vòng lặp\nViết hàm và tổ chức chương trình rõ ràng\nNắm khái niệm OOP cơ bản (class, object)\nHoàn thành 1 mini project cuối khóa', 1),
  ('Lập trình Web', 'lap-trinh-web', 'Lập trình', 'intermediate', 6500000, 12, 'web_programming.png',
   'Xây dựng ứng dụng web hoàn chỉnh từ giao diện đến xử lý phía máy chủ. Khóa dạy HTML/CSS/JavaScript và PHP theo mô hình MVC, kết nối CSDL MySQL kèm các nguyên tắc bảo mật căn bản. Kết thúc khóa bạn tự làm được một hệ thống CRUD thật.',
   'Dựng giao diện với HTML/CSS/JavaScript\nLập trình PHP theo mô hình MVC\nKết nối và thao tác CSDL MySQL an toàn\nHoàn thiện 1 dự án web CRUD triển khai được', 1),
  ('Data Science', 'data-science', 'Lập trình', 'advanced', 7800000, 14, 'data_science.png',
   'Bước vào thế giới khoa học dữ liệu: thu thập, làm sạch, phân tích và trực quan hóa dữ liệu, tiến tới machine learning cơ bản. Học qua bộ dữ liệu thật với Python (pandas, matplotlib) và các tình huống kinh doanh thực tế.',
   'Làm sạch và xử lý dữ liệu với pandas\nTrực quan hóa dữ liệu để kể chuyện bằng số liệu\nXây dựng mô hình machine learning cơ bản\nTrình bày kết quả phân tích cho người không chuyên', 1),
  ('Tiếng Nhật N5', 'tieng-nhat-n5', 'Ngoại ngữ', 'beginner', 4000000, 12, 'japanese_n5.png',
   'Khóa tiếng Nhật sơ cấp hướng tới trình độ JLPT N5. Bạn học bảng chữ Hiragana - Katakana, 100+ Kanji cơ bản, mẫu câu giao tiếp hàng ngày và luyện nghe cơ bản. Phù hợp người bắt đầu từ con số 0.',
   'Đọc viết thành thạo Hiragana và Katakana\nNhớ 100+ Kanji và 600+ từ vựng N5\nGiao tiếp các tình huống cơ bản hàng ngày\nSẵn sàng thi chứng chỉ JLPT N5', 1),
  ('Thiết kế đồ họa', 'thiet-ke-do-hoa', 'Thiết kế', 'beginner', 5500000, 10, 'graphic_design.png',
   'Khóa thiết kế đồ họa từ nền tảng: nguyên lý bố cục, màu sắc, typography và thực hành trên Photoshop & Illustrator. Bạn sẽ tạo poster, banner và bộ nhận diện thương hiệu đơn giản, từng bước xây dựng portfolio đầu tay.',
   'Nắm nguyên lý bố cục, màu sắc và typography\nThành thạo thao tác cơ bản Photoshop & Illustrator\nThiết kế poster, banner, ấn phẩm mạng xã hội\nHoàn thiện portfolio 3-5 sản phẩm', 1),
  ('Digital Marketing', 'digital-marketing', 'Marketing', 'intermediate', 6000000, 8, 'digital_marketing.png',
   'Tổng quan marketing số từ chiến lược đến thực thi: SEO, quảng cáo Facebook/Google Ads, content và đo lường hiệu quả. Khóa thiên thực chiến với các chiến dịch mẫu và công cụ phân tích phổ biến.',
   'Xây dựng kế hoạch marketing số cơ bản\nChạy và tối ưu quảng cáo Facebook/Google\nSản xuất content và làm SEO on-page\nĐo lường hiệu quả bằng các chỉ số chính', 1);

-- ===== SEED leads (25 dong) =====
INSERT INTO leads (full_name, email, phone, course, source, status, note, created_at) VALUES
  ('Nguyễn Văn An', 'nguyenvanan1@gmail.com', '0910000000', 'IELTS Foundation', 'website', 'new', 'Lead quan tâm khóa IELTS Foundation', '2026-01-01 01:15:00'),
  ('Trần Thị Bích', 'tranthibich2@gmail.com', '0910000373', 'IELTS Advanced', 'facebook', 'contacted', 'Lead quan tâm khóa IELTS Advanced', '2026-02-02 02:15:00'),
  ('Lê Hoàng Cường', 'lehoangcuong3@gmail.com', '0910000746', 'Lập trình Python', 'zalo', 'qualified', 'Lead quan tâm khóa Lập trình Python', '2026-03-03 03:15:00'),
  ('Phạm Thu Dung', 'phamthudung4@gmail.com', '0910001119', 'Lập trình Web', 'referral', 'converted', 'Lead quan tâm khóa Lập trình Web', '2026-04-04 04:15:00'),
  ('Hoàng Minh Đức', 'hoangminhduc5@gmail.com', '0910001492', 'Data Science', 'hotline', 'lost', 'Lead quan tâm khóa Data Science', '2026-05-05 05:15:00'),
  ('Võ Thị Hoa', 'vothihoa6@gmail.com', '0910001865', 'Tiếng Nhật N5', 'website', 'new', 'Lead quan tâm khóa Tiếng Nhật N5', '2026-01-06 06:15:00'),
  ('Đặng Văn Khoa', 'dangvankhoa7@gmail.com', '0910002238', 'Thiết kế đồ họa', 'facebook', 'contacted', 'Lead quan tâm khóa Thiết kế đồ họa', '2026-02-07 07:15:00'),
  ('Bùi Thị Lan', 'buithilan8@gmail.com', '0910002611', 'Digital Marketing', 'zalo', 'qualified', 'Lead quan tâm khóa Digital Marketing', '2026-03-08 08:15:00'),
  ('Đỗ Quang Minh', 'doquangminh9@gmail.com', '0910002984', 'IELTS Foundation', 'referral', 'converted', 'Lead quan tâm khóa IELTS Foundation', '2026-04-09 09:15:00'),
  ('Ngô Thị Nga', 'ngothinga10@gmail.com', '0910003357', 'IELTS Advanced', 'hotline', 'lost', 'Lead quan tâm khóa IELTS Advanced', '2026-05-10 01:15:00'),
  ('Phan Văn Phúc', 'phanvanphuc11@gmail.com', '0910003730', 'Lập trình Python', 'website', 'new', 'Lead quan tâm khóa Lập trình Python', '2026-01-11 02:15:00'),
  ('Trương Thị Quyên', 'truongthiquyen12@gmail.com', '0910004103', 'Lập trình Web', 'facebook', 'contacted', 'Lead quan tâm khóa Lập trình Web', '2026-02-12 03:15:00'),
  ('Lý Văn Sơn', 'lyvanson13@gmail.com', '0910004476', 'Data Science', 'zalo', 'qualified', 'Lead quan tâm khóa Data Science', '2026-03-13 04:15:00'),
  ('Mai Thị Trang', 'maithitrang14@gmail.com', '0910004849', 'Tiếng Nhật N5', 'referral', 'converted', 'Lead quan tâm khóa Tiếng Nhật N5', '2026-04-14 05:15:00'),
  ('Cao Văn Tuấn', 'caovantuan15@gmail.com', '0910005222', 'Thiết kế đồ họa', 'hotline', 'lost', 'Lead quan tâm khóa Thiết kế đồ họa', '2026-05-15 06:15:00'),
  ('Vũ Thị Uyên', 'vuthiuyen16@gmail.com', '0910005595', 'Digital Marketing', 'website', 'new', 'Lead quan tâm khóa Digital Marketing', '2026-01-16 07:15:00'),
  ('Hồ Văn Vinh', 'hovanvinh17@gmail.com', '0910005968', 'IELTS Foundation', 'facebook', 'contacted', 'Lead quan tâm khóa IELTS Foundation', '2026-02-17 08:15:00'),
  ('Đinh Thị Xuân', 'dinhthixuan18@gmail.com', '0910006341', 'IELTS Advanced', 'zalo', 'qualified', 'Lead quan tâm khóa IELTS Advanced', '2026-03-18 09:15:00'),
  ('Lương Văn Yên', 'luongvanyen19@gmail.com', '0910006714', 'Lập trình Python', 'referral', 'converted', 'Lead quan tâm khóa Lập trình Python', '2026-04-19 01:15:00'),
  ('Tạ Thị Ánh', 'tathianh20@gmail.com', '0910007087', 'Lập trình Web', 'hotline', 'lost', 'Lead quan tâm khóa Lập trình Web', '2026-05-20 02:15:00'),
  ('Chu Văn Bảo', 'chuvanbao21@gmail.com', '0910007460', 'Data Science', 'website', 'new', 'Lead quan tâm khóa Data Science', '2026-01-21 03:15:00'),
  ('Đoàn Thị Cẩm', 'doanthicam22@gmail.com', '0910007833', 'Tiếng Nhật N5', 'facebook', 'contacted', 'Lead quan tâm khóa Tiếng Nhật N5', '2026-02-22 04:15:00'),
  ('Hà Văn Đạt', 'havandat23@gmail.com', '0910008206', 'Thiết kế đồ họa', 'zalo', 'qualified', 'Lead quan tâm khóa Thiết kế đồ họa', '2026-03-23 05:15:00'),
  ('Kiều Thị Én', 'kieuthien24@gmail.com', '0910008579', 'Digital Marketing', 'referral', 'converted', 'Lead quan tâm khóa Digital Marketing', '2026-04-24 06:15:00'),
  ('Lâm Văn Phong', 'lamvanphong25@gmail.com', '0910008952', 'IELTS Foundation', 'hotline', 'lost', 'Lead quan tâm khóa IELTS Foundation', '2026-05-25 07:15:00');

-- ===== SEED orders (25 dong) =====
INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note, created_at) VALUES
  ('ORD-2026-0001', 1, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phiếu học phí IELTS Foundation', '2026-01-01 10:30:00'),
  ('ORD-2026-0002', 2, 'IELTS Advanced', 4200000, 'paid', '2026-02-02', 'Phiếu học phí IELTS Advanced', '2026-02-02 11:30:00'),
  ('ORD-2026-0003', 3, 'Lập trình Python', 5000000, 'refunded', NULL, 'Phiếu học phí Lập trình Python', '2026-03-03 12:30:00'),
  ('ORD-2026-0004', 4, 'Lập trình Web', 6500000, 'cancelled', NULL, 'Phiếu học phí Lập trình Web', '2026-04-04 13:30:00'),
  ('ORD-2026-0005', 5, 'Data Science', 7800000, 'pending', NULL, 'Phiếu học phí Data Science', '2026-05-05 10:30:00'),
  ('ORD-2026-0006', 6, 'Tiếng Nhật N5', 9000000, 'paid', '2026-01-06', 'Phiếu học phí Tiếng Nhật N5', '2026-01-06 11:30:00'),
  ('ORD-2026-0007', 7, 'Thiết kế đồ họa', 12000000, 'refunded', NULL, 'Phiếu học phí Thiết kế đồ họa', '2026-02-07 12:30:00'),
  ('ORD-2026-0008', 8, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phiếu học phí Digital Marketing', '2026-03-08 13:30:00'),
  ('ORD-2026-0009', 9, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phiếu học phí IELTS Foundation', '2026-04-09 10:30:00'),
  ('ORD-2026-0010', 10, 'IELTS Advanced', 4200000, 'paid', '2026-05-10', 'Phiếu học phí IELTS Advanced', '2026-05-10 11:30:00'),
  ('ORD-2026-0011', 11, 'Lập trình Python', 5000000, 'refunded', NULL, 'Phiếu học phí Lập trình Python', '2026-01-11 12:30:00'),
  ('ORD-2026-0012', 12, 'Lập trình Web', 6500000, 'cancelled', NULL, 'Phiếu học phí Lập trình Web', '2026-02-12 13:30:00'),
  ('ORD-2026-0013', 13, 'Data Science', 7800000, 'pending', NULL, 'Phiếu học phí Data Science', '2026-03-13 10:30:00'),
  ('ORD-2026-0014', 14, 'Tiếng Nhật N5', 9000000, 'paid', '2026-04-14', 'Phiếu học phí Tiếng Nhật N5', '2026-04-14 11:30:00'),
  ('ORD-2026-0015', 15, 'Thiết kế đồ họa', 12000000, 'refunded', NULL, 'Phiếu học phí Thiết kế đồ họa', '2026-05-15 12:30:00'),
  ('ORD-2026-0016', 16, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phiếu học phí Digital Marketing', '2026-01-16 13:30:00'),
  ('ORD-2026-0017', 17, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phiếu học phí IELTS Foundation', '2026-02-17 10:30:00'),
  ('ORD-2026-0018', 18, 'IELTS Advanced', 4200000, 'paid', '2026-03-18', 'Phiếu học phí IELTS Advanced', '2026-03-18 11:30:00'),
  ('ORD-2026-0019', 19, 'Lập trình Python', 5000000, 'refunded', NULL, 'Phiếu học phí Lập trình Python', '2026-04-19 12:30:00'),
  ('ORD-2026-0020', 20, 'Lập trình Web', 6500000, 'cancelled', NULL, 'Phiếu học phí Lập trình Web', '2026-05-20 13:30:00'),
  ('ORD-2026-0021', 21, 'Data Science', 7800000, 'pending', NULL, 'Phiếu học phí Data Science', '2026-01-21 10:30:00'),
  ('ORD-2026-0022', 22, 'Tiếng Nhật N5', 9000000, 'paid', '2026-02-22', 'Phiếu học phí Tiếng Nhật N5', '2026-02-22 11:30:00'),
  ('ORD-2026-0023', 23, 'Thiết kế đồ họa', 12000000, 'refunded', NULL, 'Phiếu học phí Thiết kế đồ họa', '2026-03-23 12:30:00'),
  ('ORD-2026-0024', 24, 'Digital Marketing', 15000000, 'cancelled', NULL, 'Phiếu học phí Digital Marketing', '2026-04-24 13:30:00'),
  ('ORD-2026-0025', 25, 'IELTS Foundation', 3500000, 'pending', NULL, 'Phiếu học phí IELTS Foundation', '2026-05-25 10:30:00');

-- ===== SEED du lieu GAN DAY (ngay dong theo NOW()) =====
-- Muc dich: bieu do "Lead theo ngay" (14 ngay) va "Doanh thu" (thang nay/thang
-- truoc) luon co du lieu bat ke hom nay la ngay nao -> khong trong nhu loi.
-- Lead rai deu trong 14 ngay gan nhat, nhieu nguon khac nhau (cho "Lead theo nguon").
INSERT INTO leads (full_name, email, phone, course, source, status, note, created_at) VALUES
  ('Vũ Gần Đây 01', 'recent01@educrm.vn', '0921000001', 'IELTS Foundation', 'website',  'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 0 DAY)),
  ('Vũ Gần Đây 02', 'recent02@educrm.vn', '0921000002', 'Lập trình Python', 'facebook', 'contacted', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  ('Vũ Gần Đây 03', 'recent03@educrm.vn', '0921000003', 'Digital Marketing', 'zalo',     'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  ('Vũ Gần Đây 04', 'recent04@educrm.vn', '0921000004', 'Lập trình Web',     'website',  'qualified', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 3 DAY)),
  ('Vũ Gần Đây 05', 'recent05@educrm.vn', '0921000005', 'Data Science',      'referral', 'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  ('Vũ Gần Đây 06', 'recent06@educrm.vn', '0921000006', 'Tiếng Nhật N5',     'facebook', 'contacted', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 5 DAY)),
  ('Vũ Gần Đây 07', 'recent07@educrm.vn', '0921000007', 'IELTS Advanced',    'hotline',  'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 7 DAY)),
  ('Vũ Gần Đây 08', 'recent08@educrm.vn', '0921000008', 'Thiết kế đồ họa',   'website',  'converted', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 8 DAY)),
  ('Vũ Gần Đây 09', 'recent09@educrm.vn', '0921000009', 'Lập trình Python',  'zalo',     'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ('Vũ Gần Đây 10', 'recent10@educrm.vn', '0921000010', 'Digital Marketing', 'referral', 'contacted', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 11 DAY)),
  ('Vũ Gần Đây 11', 'recent11@educrm.vn', '0921000011', 'IELTS Foundation',  'facebook', 'new',       'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ('Vũ Gần Đây 12', 'recent12@educrm.vn', '0921000012', 'Lập trình Web',     'website',  'qualified', 'Lead mới trong tuần', DATE_SUB(NOW(), INTERVAL 13 DAY));

-- Phieu da thu (paid) thang nay & thang truoc -> bieu do doanh thu + delta MoM.
INSERT INTO orders (order_code, lead_id, course, amount, status, paid_at, note, created_at) VALUES
  ('ORD-2026-9001', 1,  'IELTS Foundation', 3500000, 'paid', CURRENT_DATE, 'Doanh thu tháng này',   CURRENT_DATE),
  ('ORD-2026-9002', 6,  'Tiếng Nhật N5',    9000000, 'paid', CURRENT_DATE, 'Doanh thu tháng này',   CURRENT_DATE),
  ('ORD-2026-9003', 9,  'Lập trình Web',    6500000, 'paid', CURRENT_DATE, 'Doanh thu tháng này',   CURRENT_DATE),
  ('ORD-2026-9004', 2,  'IELTS Advanced',   4200000, 'paid', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH), 'Doanh thu tháng trước', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH)),
  ('ORD-2026-9005', 14, 'Data Science',     7800000, 'paid', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH), 'Doanh thu tháng trước', LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH));

-- ===== SEED activity_logs (F3 - vai dong demo nhat ky) =====
INSERT INTO activity_logs (user_id, action, entity, entity_id, summary, ip, created_at) VALUES
  (1, 'login',   'auth',  NULL, 'admin đăng nhập hệ thống',                 '127.0.0.1', '2026-06-01 08:00:00'),
  (1, 'create',  'lead',  1,    'Tạo lead #1 Nguyễn Văn An',                '127.0.0.1', '2026-06-01 08:05:00'),
  (2, 'login',   'auth',  NULL, 'staff đăng nhập hệ thống',                 '127.0.0.1', '2026-06-01 09:00:00'),
  (2, 'update',  'lead',  3,    'Cập nhật lead #3 -> qualified',            '127.0.0.1', '2026-06-01 09:10:00'),
  (1, 'create',  'order', 2,    'Tạo phiếu ORD-2026-0002',                  '127.0.0.1', '2026-06-02 10:30:00'),
  (1, 'convert', 'lead',  4,    'Chuyển lead #4 thành phiếu ORD-2026-0004', '127.0.0.1', '2026-06-02 10:35:00'),
  (1, 'delete',  'lead',  20,   'Xóa mềm lead #20',                         '127.0.0.1', '2026-06-03 14:00:00'),
  (1, 'restore', 'lead',  20,   'Khôi phục lead #20',                       '127.0.0.1', '2026-06-03 14:05:00'),
  (NULL, 'login_failed', 'auth', NULL, 'Đăng nhập thất bại cho username=hacker', '127.0.0.1', '2026-06-04 22:00:00'),
  (1, 'logout',  'auth',  NULL, 'admin đăng xuất',                          '127.0.0.1', '2026-06-04 18:00:00');
