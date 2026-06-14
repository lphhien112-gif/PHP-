# Feature Design — Thêm tính năng (EduCRM Lab06 + áp dụng Lab05/Lab04)

> Sinh vien: Le Pham Hong Hien — 22110059. Trọng tâm: **EduCRM (Lab06 Final, 40%)** vì có MVC đầy đủ
> (Controller → Service → Repository → View) nên mở rộng sạch. Mỗi tính năng ghi rõ: giá trị, **map vào
> rubric "Câu 3 - làm thêm"** (để cộng điểm), thay đổi schema, route, trách nhiệm MVC, bảo mật, công sức.
>
> **Nguyên tắc:** không phá chức năng đang chạy; mọi mutation giữ CSRF + prepared statements + PRG;
> Controller mỏng, SQL nằm trong Repository, business rule ở Service.

---

## 0. Hiện trạng (đã có → KHÔNG thiết kế lại)
✅ CSRF mọi form · ✅ role `admin`/`staff` (cơ bản) · ✅ transaction khi tạo order · ✅ rate-limit form public ·
✅ dashboard stats cơ bản (đếm lead/order/doanh thu) · ✅ search + pagination + whitelist sort · ✅ safe error + log.

**Còn thiếu (thiết kế bên dưới):** soft delete, audit log, lead→order conversion, export/import CSV,
analytics nâng cao, login lockout + remember-me, JSON API, advanced filter/date-range, bulk actions,
invoice in.

---

## 1. Catalog tính năng (3 tầng ưu tiên)

### TẦNG A — Flagship CRM (ấn tượng + đúng "chất" CRM)

#### F1. Lead → Order Conversion (chuyển lead thành phiếu thu) ⭐ KHUYÊN LÀM ĐẦU
**Là gì:** ở lead trạng thái `qualified`/`contacted`, nút **"Tạo phiếu thu từ lead"** mở form order đã
**điền sẵn** tên/khoá học/email từ lead. Khi submit, trong **1 transaction**: INSERT order +
UPDATE `leads.status='converted'`. Rollback nếu trùng `order_code`.
**Giá trị:** nối 2 module thành **phễu CRM thật** (lead → khách hàng) — điểm nhấn nghiệp vụ.
**Rubric:** *transaction* + *MVC service* (cộng điểm).
**Schema:** không đổi bảng; (tuỳ chọn) thêm `orders.created_by INT UNSIGNED NULL` (FK users) để biết ai tạo.
**Route:** `GET /leads/convert?id=1` (form prefilled) · `POST /orders/store` (nhận `from_lead_id`, set converted).
**MVC:** `LeadService::convertToOrder($leadId, $data)` mở `beginTransaction()` → `OrderRepository::insert()`
+ `LeadRepository::updateStatus($leadId,'converted')` → commit; catch `DuplicateRecordException` → rollback.
**Bảo mật:** CSRF; chỉ staff/admin; validate lead tồn tại & chưa `converted`/`lost`.
**Công sức:** M.

#### F2. Soft delete + Thùng rác (Restore) ⭐
**Là gì:** xoá = đánh dấu `deleted_at` (không DROP). Trang **/leads/trash**, **/orders/trash** liệt kê bản
ghi đã xoá, có **Khôi phục**; admin có **Xoá vĩnh viễn**.
**Giá trị:** chống mất dữ liệu do click nhầm; chuẩn hệ thống thật.
**Rubric:** *soft delete bằng deleted_at* (cộng điểm).
**Schema:**
```sql
ALTER TABLE leads  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL, ADD KEY idx_leads_deleted (deleted_at);
ALTER TABLE orders ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL, ADD KEY idx_orders_deleted (deleted_at);
```
Mọi list/SELECT thêm `WHERE deleted_at IS NULL`. Trash list: `WHERE deleted_at IS NOT NULL`.
**Route:** `POST /leads/delete` (đổi sang soft) · `GET /leads/trash` · `POST /leads/restore` ·
`POST /leads/force-delete` (admin).
**MVC:** `Repository::softDelete($id)` / `restore($id)` / `forceDelete($id)`; `Service` chặn force-delete nếu
không phải admin.
**Bảo mật:** CSRF; force-delete chỉ admin (role).
**Công sức:** M (đụng nhiều query list — thêm filter `deleted_at IS NULL`).

#### F3. Audit Trail / Nhật ký hoạt động ⭐
**Là gì:** ghi lại **ai làm gì lúc nào**: create/update/delete/restore/convert/login/logout. Trang **/audit**
(chỉ admin) xem được, lọc theo user/action/entity, có phân trang.
**Giá trị:** accountability — rất "doanh nghiệp", ghi điểm kỹ thuật.
**Schema:**
```sql
CREATE TABLE activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action ENUM('create','update','delete','restore','convert','login','logout','login_failed') NOT NULL,
  entity VARCHAR(40) NULL,            -- 'lead' | 'order' | 'auth'
  entity_id INT UNSIGNED NULL,
  summary VARCHAR(255) NULL,          -- mô tả ngắn, KHÔNG log dữ liệu nhạy cảm
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_audit_user (user_id), KEY idx_audit_entity (entity, entity_id), KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
**MVC:** `AuditService::log($action,$entity,$id,$summary)` gọi từ các Service sau khi mutate; `AuditRepository`
+ `AuditController@index`.
**Bảo mật:** admin-only; không log password/PII thô.
**Công sức:** M.

### TẦNG B — Tăng giá trị & trải nghiệm

#### F4. Export / Import CSV
**Export:** `GET /leads/export?...` stream CSV theo đúng filter hiện tại (q/status/date), escape chuẩn,
header `Content-Disposition: attachment`. Tương tự `/orders/export`.
**Import:** `GET /leads/import` (upload) + `POST /leads/import` → đọc CSV, **validate từng dòng**, dedupe theo
`email` (unique), báo *N thêm / M bỏ qua (trùng) / lỗi dòng X*.
**Rubric:** ngoài rubric nhưng rất ấn tượng (data portability).
**Bảo mật:** role; giới hạn kích thước & MIME; validate từng field; prepared insert.
**Công sức:** Export S, Import M.

#### F5. Dashboard Analytics nâng cao
**Là gì:** thêm vào dashboard: **biểu đồ lead/ngày 14 ngày** (SVG/CSS thuần, không cần thư viện JS),
**phễu chuyển đổi** (new→contacted→qualified→converted), **doanh thu 6 tháng**, **Top 5 khoá học**.
**Rubric:** *dashboard thống kê* (cộng điểm).
**MVC:** `StatsRepository` các query `GROUP BY DATE(created_at)`, `GROUP BY status`, `SUM(amount)`;
`DashboardService` gom số liệu; view vẽ thanh bằng `<div>`/inline `<svg>`.
**Bảo mật:** trong vùng đăng nhập.
**Công sức:** M.

#### F6. Login hardening: Lockout + Remember-me an toàn
**Lockout:** đếm đăng nhập sai theo username+IP (bảng `login_attempts` hoặc session); >5 lần/10 phút → khoá
15 phút, flash rõ ràng. Ghi `login_failed` vào audit.
**Remember-me (token, KHÔNG lưu password):** khi tick "ghi nhớ", tạo token ngẫu nhiên, lưu **hash** vào
`remember_tokens(user_id, token_hash, expires_at)`, set cookie HttpOnly+SameSite+Secure dạng `selector:token`;
lần sau verify hash, **rotate** token mỗi lần dùng, **revoke** khi logout/đổi mật khẩu.
**Rubric/Báo cáo:** trả lời thẳng câu Problem-Solving về *remember-me* và *session security*.
**Schema:**
```sql
CREATE TABLE remember_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  selector CHAR(24) NOT NULL, token_hash CHAR(64) NOT NULL, expires_at DATETIME NOT NULL,
  UNIQUE KEY uq_selector (selector), KEY idx_user (user_id),
  CONSTRAINT fk_rt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```
**Công sức:** Lockout S, Remember-me M.

### TẦNG C — Nâng cao / tuỳ chọn

#### F7. Read-only JSON API + token
`GET /api/leads`, `/api/orders`, `/api/stats` trả JSON, yêu cầu header `Authorization: Bearer <token>` so với
bảng `api_tokens` (hoặc token trong config). Có pagination + filter. **Rubric:** *API* (cộng điểm).
Bảo mật: token-based, **chỉ đọc**, rate-limit. Công sức: M.

#### F8. Global search + Advanced filter (date range)
Ô tìm kiếm toàn cục (lead: name/email/phone/course; order: code) + lọc **khoảng ngày**
(`created_from`/`created_to`) kết hợp status + q; hiển thị **chip filter đang áp** + tổng kết quả + "Xoá lọc".
Repository dựng WHERE động nhưng vẫn **prepared/bound**. Công sức: M.

#### F9. Bulk actions + List UX
Checkbox chọn nhiều dòng → đổi status / soft-delete hàng loạt (`POST id[]`). Thêm **chọn page-size** (10/20/50)
và **toggle hướng sort** asc/desc trên tiêu đề cột (Lab05 rubric). Công sức: M.

#### F10. Invoice in được (printable)
`GET /orders/invoice?id=1` → trang HTML sạch (print CSS) gồm thông tin lead + phiếu + số tiền + trạng thái;
in/lưu PDF qua trình duyệt. Công sức: S.

#### F11. Role mở rộng (admin / manager / staff)
Chuẩn hoá ma trận quyền: admin (toàn quyền + force-delete + audit), manager (duyệt/đổi status + export),
staff (create/update). UI ẩn nút theo role; **server vẫn enforce**. Công sức: S–M.

---

## 2. Lộ trình đề xuất (Top 5 làm trước — cao điểm, không phá vỡ hệ thống)

| Thứ tự | Tính năng | Vì sao trước | Rubric | Công sức |
|---|---|---|---|---|
| 1 | **F1 Lead→Order Conversion** | Điểm nhấn nghiệp vụ, dùng transaction sẵn có | transaction | M |
| 2 | **F5 Dashboard Analytics** | Nhìn là thấy "xịn" ngay, dữ liệu đã có | dashboard | M |
| 3 | **F2 Soft delete + Trash** | An toàn dữ liệu, rubric trực tiếp | soft delete | M |
| 4 | **F3 Audit log** | Tính "doanh nghiệp", phục vụ Problem-Solving | (bonus) | M |
| 5 | **F4 CSV Export** | Nhanh, hữu ích, data portability | (bonus) | S |

> Làm xong Top 5 → mỗi cái thêm **1–2 test case** + **1 góc Problem-Solving** trong báo cáo (xem mục 4).

---

## 3. Áp dụng cho Lab05 & Lab04

**ClinicDesk (Lab05)** — tái dùng gần như nguyên: F2 soft delete (patients/appointments), F4 export,
F3 audit, F5 dashboard (lịch hẹn theo ngày, theo status), F8 filter date-range, F9 sort toggle/page-size.
**Bổ sung riêng:** Lab05 hiện **chưa có auth** → thêm **login + middleware bảo vệ trang quản trị** (đúng
rubric "middleware login cơ bản"). + F1-tương đương: từ `patient` tạo nhanh `appointment` (prefill).

**WorkshopHub (Lab04, lưu JSON — giữ nhẹ):** **duyệt đăng ký** (status pending/approved/rejected do staff
đổi) · **export CSV** từ `registrations.json` · **tìm kiếm + lọc** danh sách đăng ký · **archive** thay xoá
(soft delete kiểu JSON) · trang **thống kê đơn giản** (đăng ký theo workshop). Không cần DB.

---

## 4. Tác động lên báo cáo (vì sao đáng làm)

Mỗi tính năng bổ sung **minh chứng + lập luận** cho rubric "làm thêm" và phần Problem-Solving:
- F1 → câu *transaction/atomicity*: 2 thao tác phải toàn-vẹn (order + đổi status) hoặc rollback cả hai.
- F2 → *soft delete vs hard delete*, giữ dữ liệu phục hồi; delete vẫn bằng POST.
- F3 → *accountability/audit*, ai chịu trách nhiệm thay đổi nào.
- F4 → *data portability*, escape CSV chống injection công thức (`=,+,-,@`).
- F5 → *index cho query GROUP BY*, EXPLAIN cho aggregate.
- F6 → trả lời trực tiếp câu *remember-me* (token hash, revoke) và *brute-force/lockout*.
- F7 → *stateless auth (Bearer token)* khác session.

---

## 5. Bạn chọn gì tiếp theo?
- **(A)** Tôi implement luôn **Top 5 cho EduCRM** (F1→F5), test bằng `php -S`+curl, rồi **bổ sung test case +
  Problem-Solving vào PDF**. ← khuyên dùng
- **(B)** Chọn lẻ vài tính năng (vd chỉ F1 + F5 + F2).
- **(C)** Làm cho **cả 3 app** theo mục 3 (lớn hơn — nên chạy nhiều agent song song).
- **(D)** Chỉ cần bản thiết kế này, bạn tự code.
