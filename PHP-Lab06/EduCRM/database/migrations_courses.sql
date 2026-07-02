-- =====================================================================
-- EduCRM - Migration Module C: Khoa hoc (courses)
-- Ap dung cho DB DANG CHAY (giu du lieu cu). Idempotent (chay lai nhieu lan).
-- Chay: mysql -u root -h 127.0.0.1 training_center_crm < database/migrations_courses.sql
-- Sinh vien: Le Pham Hong Hien - MSSV: 22110059
-- =====================================================================
USE training_center_crm;

-- ----- Bang courses (Module C): danh muc khoa hoc co hinh anh -----
CREATE TABLE IF NOT EXISTS courses (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(80)  NOT NULL,            -- ten (khop leads.course / orders.course)
    slug           VARCHAR(100) NOT NULL,            -- dinh danh URL-friendly
    category       VARCHAR(40)  NOT NULL DEFAULT 'Khac',   -- nhom: Ngoai ngu / Lap trinh / ...
    level          ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    price          DECIMAL(12,2) NOT NULL DEFAULT 0, -- hoc phi (VND)
    duration_weeks INT UNSIGNED NOT NULL DEFAULT 8,  -- thoi luong (tuan)
    image          VARCHAR(120) NULL,               -- ten file trong /assets/img/courses/
    description    VARCHAR(500) NULL,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,  -- 1 = hien tren form lead/order
    deleted_at     TIMESTAMP NULL DEFAULT NULL,      -- soft delete (NULL = con song)
    -- UNIQUE chi tren ban ghi con song (giong M1 o leads/orders):
    name_active    VARCHAR(80) GENERATED ALWAYS AS (IF(deleted_at IS NULL, name, NULL)) STORED,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_name_active (name_active),
    KEY idx_courses_category (category),
    KEY idx_courses_active (is_active),
    KEY idx_courses_deleted (deleted_at),
    KEY idx_courses_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- Seed 8 khoa hoc (chi khi bang dang rong) - gan dung 8 file anh trong
--       public/assets/img/courses/ -----
INSERT INTO courses (name, slug, category, level, price, duration_weeks, image, description, is_active)
SELECT * FROM (
      SELECT 'IELTS Foundation' AS name, 'ielts-foundation' AS slug, 'Ngoai ngu' AS category,
             'beginner' AS level, 3500000 AS price, 10 AS duration_weeks,
             'ielts_foundation.png' AS image,
             'Nen tang IELTS cho nguoi moi bat dau, muc tieu band 4.5-5.5.' AS description, 1 AS is_active
UNION ALL SELECT 'IELTS Advanced','ielts-advanced','Ngoai ngu','advanced',4200000,12,'ielts_advanced.png',
             'Luyen chuyen sau 4 ky nang, muc tieu band 6.5+.',1
UNION ALL SELECT 'Lap trinh Python','lap-trinh-python','Lap trinh','beginner',5000000,10,'python_programming.png',
             'Nhap mon lap trinh voi Python: cu phap, ham, OOP co ban.',1
UNION ALL SELECT 'Lap trinh Web','lap-trinh-web','Lap trinh','intermediate',6500000,12,'web_programming.png',
             'HTML/CSS/JS va PHP MVC - xay dung ung dung web hoan chinh.',1
UNION ALL SELECT 'Data Science','data-science','Lap trinh','advanced',7800000,14,'data_science.png',
             'Phan tich du lieu, truc quan hoa va machine learning co ban.',1
UNION ALL SELECT 'Tieng Nhat N5','tieng-nhat-n5','Ngoai ngu','beginner',4000000,12,'japanese_n5.png',
             'Tieng Nhat so cap huong toi trinh do JLPT N5.',1
UNION ALL SELECT 'Thiet ke do hoa','thiet-ke-do-hoa','Thiet ke','beginner',5500000,10,'graphic_design.png',
             'Photoshop/Illustrator va nguyen ly thiet ke thi giac.',1
UNION ALL SELECT 'Digital Marketing','digital-marketing','Marketing','intermediate',6000000,8,'digital_marketing.png',
             'SEO, quang cao va content marketing tu co ban den thuc chien.',1
) AS seed
WHERE (SELECT COUNT(*) FROM courses) = 0;
