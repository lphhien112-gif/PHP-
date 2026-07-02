-- =====================================================================
-- EduCRM - Training Center CRM
-- Database schema: training_center_crm
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
-- =====================================================================
-- Tao database voi charset utf8mb4 de luu tieng Viet va emoji an toan.
-- Chay: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS training_center_crm
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE training_center_crm;

-- Xoa bang theo thu tu phu thuoc khoa ngoai (activity_logs -> orders -> leads -> users)
DROP TABLE IF EXISTS api_tokens;
DROP TABLE IF EXISTS remember_tokens;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
-- Bang users: nhan vien tu van / quan tri dang nhap he thong
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,            -- bcrypt tu password_hash()
    full_name     VARCHAR(120) NOT NULL,
    role          ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff', -- F11: them manager
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang courses (Module C): danh muc khoa hoc co hinh anh
-- Ghi chu: leads.course / orders.course tham chieu courses.name (dang chuoi).
-- ---------------------------------------------------------------------
CREATE TABLE courses (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(80)  NOT NULL,
    slug           VARCHAR(100) NOT NULL,
    category       VARCHAR(40)  NOT NULL DEFAULT 'Khac',
    level          ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    price          DECIMAL(12,2) NOT NULL DEFAULT 0,     -- hoc phi (VND)
    duration_weeks INT UNSIGNED NOT NULL DEFAULT 8,      -- thoi luong (tuan)
    image          VARCHAR(120) NULL,                    -- file trong /assets/img/courses/
    description    VARCHAR(500) NULL,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,      -- 1 = hien tren form lead/order
    deleted_at     TIMESTAMP NULL DEFAULT NULL,          -- soft delete
    name_active    VARCHAR(80) GENERATED ALWAYS AS (IF(deleted_at IS NULL, name, NULL)) STORED,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_name_active (name_active),     -- chong trung ten (ban con song)
    KEY idx_courses_category (category),
    KEY idx_courses_active (is_active),
    KEY idx_courses_deleted (deleted_at),
    KEY idx_courses_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang leads (Module A): lead tu van / hoc vien tiem nang
-- ---------------------------------------------------------------------
CREATE TABLE leads (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name     VARCHAR(120) NOT NULL,
    email         VARCHAR(150) NOT NULL,            -- duy nhat: chong trung lead
    phone         VARCHAR(20)  NOT NULL,
    course        VARCHAR(80)  NOT NULL,            -- khoa hoc quan tam (whitelist)
    source        VARCHAR(40)  NOT NULL DEFAULT 'website', -- nguon: website/facebook/...
    status        ENUM('new','contacted','qualified','converted','lost')
                  NOT NULL DEFAULT 'new',
    note          VARCHAR(500) NULL,
    deleted_at    TIMESTAMP NULL DEFAULT NULL,           -- F2: soft delete (NULL = con song)
    -- M1: chi rang buoc UNIQUE tren ban ghi CON SONG (deleted_at IS NULL).
    -- Khi xoa mem -> email_active = NULL -> KHONG vi pham UNIQUE nua,
    -- cho phep tao lead moi cung email ma van giu ban cu trong thung rac.
    email_active  VARCHAR(150) GENERATED ALWAYS AS (IF(deleted_at IS NULL, email, NULL)) STORED,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_leads_email_active (email_active), -- chong trung email lead (chi ban con song)
    KEY idx_leads_status (status),                  -- ho tro filter theo status
    KEY idx_leads_course (course),                  -- ho tro filter theo course
    KEY idx_leads_deleted (deleted_at),             -- F2: ho tro loc deleted_at IS NULL
    KEY idx_leads_created_at (created_at)           -- ho tro sort mac dinh moi nhat
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang orders (Module B): phieu thanh toan hoc phi gan voi lead
-- ---------------------------------------------------------------------
CREATE TABLE orders (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_code    VARCHAR(30)  NOT NULL,            -- ma phieu duy nhat (ORD-2026-xxxx)
    lead_id       INT UNSIGNED NOT NULL,            -- khoa ngoai toi leads
    course        VARCHAR(80)  NOT NULL,
    amount        DECIMAL(12,2) NOT NULL DEFAULT 0, -- hoc phi (VND)
    status        ENUM('pending','paid','refunded','cancelled')
                  NOT NULL DEFAULT 'pending',
    paid_at       DATE NULL,
    note          VARCHAR(500) NULL,
    deleted_at    TIMESTAMP NULL DEFAULT NULL,           -- F2: soft delete (NULL = con song)
    -- M1: UNIQUE chi tren ban ghi con song -> xoa mem khong giu cho ma phieu.
    order_code_active VARCHAR(30) GENERATED ALWAYS AS (IF(deleted_at IS NULL, order_code, NULL)) STORED,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_code_active (order_code_active), -- chong trung ma phieu (chi ban con song)
    KEY idx_orders_status (status),                 -- ho tro filter theo status
    KEY idx_orders_lead_id (lead_id),               -- ho tro join / loc theo lead
    KEY idx_orders_deleted (deleted_at),            -- F2: ho tro loc deleted_at IS NULL
    KEY idx_orders_created_at (created_at),
    CONSTRAINT fk_orders_lead
        FOREIGN KEY (lead_id) REFERENCES leads (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang activity_logs (F3): nhat ky hoat dong - ai lam gi luc nao
-- ---------------------------------------------------------------------
CREATE TABLE activity_logs (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NULL,                  -- ai thuc hien (NULL khi login_failed)
    action        ENUM('create','update','delete','restore','convert','login','logout','login_failed')
                  NOT NULL,
    entity        VARCHAR(40) NULL,                   -- 'lead' | 'order' | 'auth'
    entity_id     INT UNSIGNED NULL,
    summary       VARCHAR(255) NULL,                  -- mo ta ngan, KHONG log du lieu nhay cam
    ip            VARCHAR(45) NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_user (user_id),
    KEY idx_audit_action (action),
    KEY idx_audit_entity (entity, entity_id),
    KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang login_attempts (F6a): dem dang nhap sai theo username + IP (lockout)
-- ---------------------------------------------------------------------
CREATE TABLE login_attempts (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL,
    ip            VARCHAR(45)  NOT NULL,
    attempted_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_la_user_ip (username, ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang remember_tokens (F6b): remember-me an toan (luu HASH, khong luu password)
-- Cookie dang "selector:token"; verify selector roi hash_equals token_hash.
-- ---------------------------------------------------------------------
CREATE TABLE remember_tokens (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NOT NULL,
    selector      CHAR(24)  NOT NULL,
    token_hash    CHAR(64)  NOT NULL,               -- SHA-256 hex cua token tho
    expires_at    DATETIME  NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rt_selector (selector),
    KEY idx_rt_user (user_id),
    CONSTRAINT fk_rt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bang api_tokens (F7): bearer token cho JSON API read-only (luu HASH)
-- ---------------------------------------------------------------------
CREATE TABLE api_tokens (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    token_hash    CHAR(64)  NOT NULL,               -- SHA-256 hex cua bearer token
    label         VARCHAR(80) NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_api_token_hash (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
