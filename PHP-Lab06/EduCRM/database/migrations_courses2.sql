-- =====================================================================
-- EduCRM - Migration Module C (dot 2): lam giau mo ta khoa hoc
-- - description: VARCHAR(500) -> TEXT (du cho doan van dai)
-- - them cot outcomes (TEXT): "Ban se hoc duoc gi" - moi dong 1 y
-- Idempotent. Chay: mysql -u root -h 127.0.0.1 training_center_crm < database/migrations_courses2.sql
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- =====================================================================
USE training_center_crm;

ALTER TABLE courses MODIFY COLUMN description TEXT NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS outcomes TEXT NULL AFTER description;

-- Lam giau noi dung cho 8 khoa hoc goc. Guard `outcomes IS NULL` de KHONG
-- de len ban ghi ma nguoi dung da chinh sua.
UPDATE courses SET
  description = 'Khoa nen tang danh cho nguoi moi tiep can IELTS hoac con mat goc tieng Anh. Ban duoc cung co ngu phap loi, mo rong tu vung theo chu de va lam quen dinh dang 4 ky nang Nghe - Noi - Doc - Viet. Lo trinh huong toi band 4.5-5.5, tao ban dap vung chac truoc khi len trinh do cao hon.',
  outcomes = 'Nam vung 12 chu diem ngu phap cot loi cua IELTS\nXay dung 800+ tu vung hoc thuat theo chu de\nLam quen cau truc va cach tinh diem 4 ky nang\nTu tin lam bai thi thu dat band 4.5-5.5'
WHERE slug = 'ielts-foundation' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Danh cho hoc vien da co nen tang, muon but pha len band 6.5+. Khoa tap trung chien luoc lam bai cho tung dang cau hoi, ky thuat Writing Task 1 & 2 va phan xa Speaking. Bao gom chua bai chi tiet va thi thu dinh ky sat de that.',
  outcomes = 'Chien luoc xu ly tung dang bai Reading & Listening\nViet Task 1 & Task 2 mach lac, du y, dung tu hoc thuat\nPhan xa Speaking tu nhien theo band 6.5-7.5\nThi thu dinh ky kem feedback ca nhan hoa'
WHERE slug = 'ielts-advanced' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Nhap mon lap trinh voi Python - ngon ngu de hoc nhung manh me. Ban di tu bien, kieu du lieu, vong lap den ham va lap trinh huong doi tuong, ket hop nhieu bai tap thuc hanh. Phu hop nguoi chua tung viet dong code nao.',
  outcomes = 'Hieu bien, kieu du lieu, dieu kien va vong lap\nViet ham va to chuc chuong trinh ro rang\nNam khai niem OOP co ban (class, object)\nHoan thanh 1 mini project cuoi khoa'
WHERE slug = 'lap-trinh-python' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Xay dung ung dung web hoan chinh tu giao dien den xu ly phia may chu. Khoa day HTML/CSS/JavaScript va PHP theo mo hinh MVC, ket noi CSDL MySQL kem cac nguyen tac bao mat can ban. Ket thuc khoa ban tu lam duoc mot he thong CRUD that.',
  outcomes = 'Dung giao dien voi HTML/CSS/JavaScript\nLap trinh PHP theo mo hinh MVC\nKet noi va thao tac CSDL MySQL an toan\nHoan thien 1 du an web CRUD trien khai duoc'
WHERE slug = 'lap-trinh-web' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Buoc vao the gioi khoa hoc du lieu: thu thap, lam sach, phan tich va truc quan hoa du lieu, tien toi machine learning co ban. Hoc qua bo du lieu that voi Python (pandas, matplotlib) va cac tinh huong kinh doanh thuc te.',
  outcomes = 'Lam sach va xu ly du lieu voi pandas\nTruc quan hoa du lieu de ke chuyen bang so lieu\nXay dung mo hinh machine learning co ban\nTrinh bay ket qua phan tich cho nguoi khong chuyen'
WHERE slug = 'data-science' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Khoa tieng Nhat so cap huong toi trinh do JLPT N5. Ban hoc bang chu Hiragana - Katakana, 100+ Kanji co ban, mau cau giao tiep hang ngay va luyen nghe co ban. Phu hop nguoi bat dau tu con so 0.',
  outcomes = 'Doc viet thanh thao Hiragana va Katakana\nNho 100+ Kanji va 600+ tu vung N5\nGiao tiep cac tinh huong co ban hang ngay\nSan sang thi chung chi JLPT N5'
WHERE slug = 'tieng-nhat-n5' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Khoa thiet ke do hoa tu nen tang: nguyen ly bo cuc, mau sac, typography va thuc hanh tren Photoshop & Illustrator. Ban se tao poster, banner va bo nhan dien thuong hieu don gian, tung buoc xay dung portfolio dau tay.',
  outcomes = 'Nam nguyen ly bo cuc, mau sac va typography\nThanh thao thao tac co ban Photoshop & Illustrator\nThiet ke poster, banner, an pham mang xa hoi\nHoan thien portfolio 3-5 san pham'
WHERE slug = 'thiet-ke-do-hoa' AND outcomes IS NULL;

UPDATE courses SET
  description = 'Tong quan marketing so tu chien luoc den thuc thi: SEO, quang cao Facebook/Google Ads, content va do luong hieu qua. Khoa thien thuc chien voi cac chien dich mau va cong cu phan tich pho bien.',
  outcomes = 'Xay dung ke hoach marketing so co ban\nChay va toi uu quang cao Facebook/Google\nSan xuat content va lam SEO on-page\nDo luong hieu qua bang cac chi so chinh'
WHERE slug = 'digital-marketing' AND outcomes IS NULL;
