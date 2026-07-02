-- =====================================================================
-- EduCRM - Don du lieu DEMO truoc khi len PRODUCTION
-- Chay SAU khi da co schema (va lo da nap seed demo):
--   mysql -u root -p training_center_crm < database/production_hardening.sql
-- =====================================================================
USE training_center_crm;

-- 1) Xoa API token demo (token tho EDUCRM-DEMO-API-TOKEN-2026 da lo trong README)
DELETE FROM api_tokens WHERE label = 'demo-readonly';

-- 2) Xoa tai khoan demo (admin/manager/staff - mat khau mac dinh da cong khai)
DELETE FROM users WHERE username IN ('admin', 'manager', 'staff');

-- 3) (Tuy chon) Xoa sach du lieu lead/order demo - bo comment neu muon DB trong
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE activity_logs;
-- TRUNCATE TABLE orders;
-- TRUNCATE TABLE leads;
-- SET FOREIGN_KEY_CHECKS = 1;

-- 4) Tao tai khoan admin THAT:
--    B1: sinh hash:  C:/xampp/php/php.exe bin/hash-password.php 'MatKhauManh'
--    B2: dan <HASH> vao lenh duoi (doi username / ho ten that)
-- INSERT INTO users (username, password_hash, full_name, role)
--   VALUES ('admin_that', '<HASH>', 'Ho Ten That', 'admin');

-- 5) Tao API token THAT (neu dung F7):
--    B1: sinh chuoi ngau nhien (vd: openssl rand -hex 24) -> dua cho client
--    B2: chi luu HASH SHA-256 (KHONG luu token tho vao DB)
-- INSERT INTO api_tokens (token_hash, label)
--   VALUES (SHA2('<TOKEN_THO_NGAU_NHIEN>', 256), 'production');
