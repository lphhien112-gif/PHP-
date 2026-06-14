-- =====================================================================
-- ClinicDesk - Mini Clinic Appointment DB App (PHP Lab05)
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
-- Truong Dai hoc Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc
--
-- database/schema.sql - Tao database va 3 bang: users, patients, appointments.
--   - PRIMARY KEY tren id (AUTO_INCREMENT).
--   - UNIQUE: patients.email, appointments.appointment_code, users.username.
--   - INDEX: phong cho cot hay search/filter/sort.
-- Charset toan bo: utf8mb4 de luu tieng Viet co dau.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS clinic_appointment_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clinic_appointment_db;

-- Drop theo thu tu phu thuoc (xoa appointments truoc neu lam lai).
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
-- Bang users: tai khoan quan tri (de mo rong auth/role sau nay).
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL,
    full_name     VARCHAR(120) NOT NULL,
    role          VARCHAR(20)  NOT NULL DEFAULT 'staff',
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang patients: ho so benh nhan.
--   email UNIQUE -> chan trung email.
--   index phone, index created_at -> phuc vu search/sort.
-- ---------------------------------------------------------------------
CREATE TABLE patients (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    gender     ENUM('male','female','other') NOT NULL DEFAULT 'other',
    address    VARCHAR(255) NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_patients_email (email),
    KEY idx_patients_phone (phone),
    KEY idx_patients_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang appointments: lich hen kham.
--   appointment_code UNIQUE -> chan trung ma lich hen.
--   index (status, created_at) -> filter theo status roi sort moi nhat.
--   index appointment_date, index appointment_code -> search/sort theo ngay/ma.
-- ---------------------------------------------------------------------
CREATE TABLE appointments (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    appointment_code VARCHAR(20)  NOT NULL,
    patient_name     VARCHAR(120) NOT NULL,
    patient_email    VARCHAR(150) NOT NULL,
    department       VARCHAR(80)  NOT NULL,
    appointment_date DATETIME     NOT NULL,
    status           ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    note             VARCHAR(255) NULL,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_appointments_code (appointment_code),
    KEY idx_appointments_status_created (status, created_at),
    KEY idx_appointments_date (appointment_date),
    KEY idx_appointments_code (appointment_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
