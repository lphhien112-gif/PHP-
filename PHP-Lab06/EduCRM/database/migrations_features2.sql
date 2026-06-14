-- =====================================================================
-- EduCRM - Migration cho cac tinh nang moi DOT 2 (F6-F11)
-- Ap dung cho DB DANG CHAY (giu du lieu cu).
-- Chay: mysql -u root training_center_crm < database/migrations_features2.sql
--
-- An toan chay lai nhieu lan (idempotent) nho IF NOT EXISTS / INSERT IGNORE.
-- Tuong thich MariaDB (ADD COLUMN/INDEX IF NOT EXISTS, MODIFY ENUM).
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- =====================================================================
USE training_center_crm;

-- =====================================================================
-- F11: Mo rong vai tro users.role -> admin / manager / staff
-- =====================================================================
ALTER TABLE users
  MODIFY COLUMN role ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff';

-- =====================================================================
-- F6 (a): Lockout dang nhap - bang login_attempts (theo username + IP)
-- Moi lan dang nhap sai ghi 1 dong. Service dem so dong trong cua so 10 phut.
-- =====================================================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username    VARCHAR(50)  NOT NULL,
    ip          VARCHAR(45)  NOT NULL,
    attempted_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_la_user_ip (username, ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- F6 (b): Remember-me an toan - bang remember_tokens
-- Luu HASH (SHA-256) cua token, KHONG luu token tho, KHONG luu password.
-- Cookie dang "selector:token"; verify bang selector roi hash_equals token_hash.
-- =====================================================================
CREATE TABLE IF NOT EXISTS remember_tokens (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED NOT NULL,
    selector    CHAR(24)  NOT NULL,
    token_hash  CHAR(64)  NOT NULL,             -- SHA-256 hex cua token tho
    expires_at  DATETIME  NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rt_selector (selector),
    KEY idx_rt_user (user_id),
    CONSTRAINT fk_rt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- F7: API token (read-only JSON API) - bang api_tokens
-- Luu HASH (SHA-256) cua token bearer, KHONG luu token tho.
-- =====================================================================
CREATE TABLE IF NOT EXISTS api_tokens (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    token_hash  CHAR(64)  NOT NULL,             -- SHA-256 hex cua bearer token
    label       VARCHAR(80) NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_api_token_hash (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- Seed 1 demo API token (idempotent) -----
-- Token tho (demo, ghi trong README): EDUCRM-DEMO-API-TOKEN-2026
-- token_hash = SHA2('EDUCRM-DEMO-API-TOKEN-2026', 256)
INSERT INTO api_tokens (token_hash, label)
SELECT SHA2('EDUCRM-DEMO-API-TOKEN-2026', 256), 'demo-readonly'
WHERE NOT EXISTS (
    SELECT 1 FROM api_tokens WHERE token_hash = SHA2('EDUCRM-DEMO-API-TOKEN-2026', 256)
);

-- =====================================================================
-- F11: Seed tai khoan demo "manager" (idempotent)
-- manager / manager123 (hash bcrypt - khop seed.sql)
-- =====================================================================
INSERT INTO users (username, password_hash, full_name, role)
SELECT 'manager', '$2y$10$nxNOfoIJS/aZ39pbCQSLV.4/1m/Az5wbuVTkoAM3oo3X.s2F3TzJu', 'Truong nhom kinh doanh', 'manager'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'manager');
