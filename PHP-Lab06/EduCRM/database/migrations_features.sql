-- =====================================================================
-- EduCRM - Migration cho cac tinh nang moi (F1-F5)
-- Ap dung cho DB DANG CHAY (giu du lieu cu): them deleted_at + activity_logs.
-- Chay: mysql -u root -h 127.0.0.1 training_center_crm < database/migrations_features.sql
--
-- An toan chay lai nhieu lan (idempotent) nho IF NOT EXISTS / DROP IF EXISTS.
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- =====================================================================
USE training_center_crm;

-- ----- F2: Soft delete cho leads -----
ALTER TABLE leads
  ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL AFTER note;
ALTER TABLE leads
  ADD INDEX IF NOT EXISTS idx_leads_deleted (deleted_at);

-- ----- F2: Soft delete cho orders -----
ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL AFTER note;
ALTER TABLE orders
  ADD INDEX IF NOT EXISTS idx_orders_deleted (deleted_at);

-- ----- M1: UNIQUE bo qua ban ghi da xoa mem (soft-delete vs UNIQUE leak) -----
-- Y tuong: them cot sinh email_active = IF(deleted_at IS NULL, email, NULL)
-- roi dat UNIQUE tren cot sinh do. Khi xoa mem, email_active = NULL nen
-- KHONG con vi pham UNIQUE -> co the tao lai lead cung email; ban cu van o
-- thung rac. Idempotent: drop UNIQUE cu (neu con), them cot + UNIQUE moi (neu chua).
ALTER TABLE leads
  DROP INDEX IF EXISTS uq_leads_email;
ALTER TABLE leads
  ADD COLUMN IF NOT EXISTS email_active VARCHAR(150)
      GENERATED ALWAYS AS (IF(deleted_at IS NULL, email, NULL)) STORED AFTER deleted_at;
ALTER TABLE leads
  ADD UNIQUE KEY IF NOT EXISTS uq_leads_email_active (email_active);

ALTER TABLE orders
  DROP INDEX IF EXISTS uq_orders_code;
ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS order_code_active VARCHAR(30)
      GENERATED ALWAYS AS (IF(deleted_at IS NULL, order_code, NULL)) STORED AFTER deleted_at;
ALTER TABLE orders
  ADD UNIQUE KEY IF NOT EXISTS uq_orders_code_active (order_code_active);

-- ----- F3: Bang activity_logs -----
CREATE TABLE IF NOT EXISTS activity_logs (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NULL,
    action        ENUM('create','update','delete','restore','convert','login','logout','login_failed')
                  NOT NULL,
    entity        VARCHAR(40) NULL,
    entity_id     INT UNSIGNED NULL,
    summary       VARCHAR(255) NULL,
    ip            VARCHAR(45) NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_user (user_id),
    KEY idx_audit_action (action),
    KEY idx_audit_entity (entity, entity_id),
    KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- F3: vai dong audit demo (chi them neu bang dang rong) -----
INSERT INTO activity_logs (user_id, action, entity, entity_id, summary, ip, created_at)
SELECT * FROM (
  SELECT 1 AS user_id, 'login' AS action, 'auth' AS entity, NULL AS entity_id,
         'admin dang nhap he thong' AS summary, '127.0.0.1' AS ip,
         '2026-06-01 08:00:00' AS created_at
  UNION ALL SELECT 2,'login','auth',NULL,'staff dang nhap he thong','127.0.0.1','2026-06-01 09:00:00'
  UNION ALL SELECT 1,'create','order',2,'Tao phieu ORD-2026-0002','127.0.0.1','2026-06-02 10:30:00'
  UNION ALL SELECT 1,'convert','lead',4,'Chuyen lead #4 thanh phieu ORD-2026-0004','127.0.0.1','2026-06-02 10:35:00'
  UNION ALL SELECT NULL,'login_failed','auth',NULL,'Dang nhap that bai cho username=hacker','127.0.0.1','2026-06-04 22:00:00'
) AS seed
WHERE (SELECT COUNT(*) FROM activity_logs) = 0;
