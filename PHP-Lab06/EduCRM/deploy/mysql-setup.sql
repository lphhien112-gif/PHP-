-- =====================================================================
-- EduCRM - Tao user MySQL rieng cho PRODUCTION (KHONG dung root)
-- Doi 'DOI_MAT_KHAU_MANH_O_DAY' truoc khi chay.
-- Chay:  mysql -u root -p < deploy/mysql-setup.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS training_center_crm
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- User chi ket noi tu localhost (app cung server). Neu DB o host khac,
-- doi 'localhost' thanh IP/host cua app server.
CREATE USER IF NOT EXISTS 'educrm_app'@'localhost'
    IDENTIFIED BY 'DOI_MAT_KHAU_MANH_O_DAY';

-- Chi cap quyen DU DUNG cho ung dung (khong DROP/ALTER/GRANT/FILE...).
-- Schema/migration chay rieng bang tai khoan admin, KHONG qua user nay.
GRANT SELECT, INSERT, UPDATE, DELETE
    ON training_center_crm.* TO 'educrm_app'@'localhost';

FLUSH PRIVILEGES;

-- Sau do dien vao file .env:
--   DB_USER=educrm_app
--   DB_PASS=DOI_MAT_KHAU_MANH_O_DAY
