# EduCRM - Training Center CRM (PHP Lab06 Final)

Secure MVC Mini CRM cho mot **trung tam dao tao**: quan ly **lead tu van** va
**phieu hoc phi (order)**, kem **form dang ky cong khai**, **dang nhap nhan vien**
va kien truc **MVC** day du (Controller - Service - Repository - View).

- **Sinh vien:** Le Pham Hong Hien — **MSSV:** 22110059
- **Mon hoc:** Lap trinh Web — **GVHD:** TS. Tran Anh Tuan
- **Truong:** Dai hoc Khoa hoc Tu nhien, DHQG-HCM — Khoa Toan - Tin hoc
- **GitHub:** https://github.com/lphhien112-gif/PHP-
- **Database:** `training_center_crm`

---

## 1. Yeu cau moi truong

- PHP 8.2+ (XAMPP: `C:/xampp/php/php.exe`)
- MySQL / MariaDB (XAMPP: `C:/xampp/mysql/bin/`)
- Khong can Composer global (da kem san `vendor/` voi PSR-4 autoload `App\ -> app/`)

## 2. Tao database

```bash
# Tao bang (schema) - tu thu muc EduCRM/
C:/xampp/mysql/bin/mysql.exe -u root < database/schema.sql

# Nap du lieu mau (2 tai khoan + 25 leads + 25 orders + audit demo)
C:/xampp/mysql/bin/mysql.exe -u root training_center_crm < database/seed.sql
```

**Da co DB cu (giu du lieu) -> chi can ap migration cac tinh nang moi:**

```bash
# F1-F5: them cot deleted_at (soft delete) + bang activity_logs (audit) - idempotent
C:/xampp/mysql/bin/mysql.exe -u root -h 127.0.0.1 training_center_crm < database/migrations_features.sql

# F6-F11: role manager + login_attempts + remember_tokens + api_tokens (+ seed) - idempotent
C:/xampp/mysql/bin/mysql.exe -u root -h 127.0.0.1 training_center_crm < database/migrations_features2.sql

# Module C: bang courses (danh muc khoa hoc co hinh anh) + seed 8 khoa hoc - idempotent
C:/xampp/mysql/bin/mysql.exe -u root -h 127.0.0.1 training_center_crm < database/migrations_courses.sql
```

Cau hinh ket noi nam o `config/database.php` (mac dinh host `127.0.0.1`, user `root`,
khong mat khau, db `training_center_crm`).

## 3. Chay server

```bash
# Tu trong thu muc EduCRM/
C:/xampp/php/php.exe -S 127.0.0.1:8106 -t public
```

Mo trinh duyet: <http://127.0.0.1:8106/login>

## 4. Tai khoan demo

| Username  | Password     | Role    | Quyen                                                  |
|-----------|--------------|---------|--------------------------------------------------------|
| `admin`   | `admin123`   | admin   | Toan quyen (force-delete + audit + user mgmt)          |
| `manager` | `manager123` | manager | Doi status + export + restore + soft-delete + trash    |
| `staff`   | `staff123`   | staff   | Chi tao/sua (create/update)                            |

### Demo API token (F7)

Read-only JSON API dung Bearer token. Token demo (da seed san):

```
EDUCRM-DEMO-API-TOKEN-2026
```

Goi thu:

```bash
curl -H "Authorization: Bearer EDUCRM-DEMO-API-TOKEN-2026" "http://127.0.0.1:8106/api/leads?per_page=5&status=new"
curl -H "Authorization: Bearer EDUCRM-DEMO-API-TOKEN-2026" http://127.0.0.1:8106/api/stats
# Thieu / sai token -> 401 JSON
curl http://127.0.0.1:8106/api/leads
```

> Token chi luu duoi dang **HASH SHA-256** trong bang `api_tokens` (khong luu token tho).

## 5. Danh sach route

| Method | URL                     | Controller@Action              | Y nghia                                   |
|--------|-------------------------|--------------------------------|-------------------------------------------|
| GET    | `/`                     | (redirect)                     | Login -> dashboard, chua login -> form cong khai |
| GET    | `/login`                | AuthController@loginForm       | Form dang nhap                            |
| POST   | `/login`                | AuthController@handleLogin     | Validate + verify + regenerate + redirect |
| POST   | `/logout`               | AuthController@logout          | Logout sach + destroy session             |
| GET    | `/dashboard`            | DashboardController@index      | Tong quan (require login)                 |
| GET    | `/public-leads/create`  | PublicLeadController@create    | Form cong khai (honeypot/rate limit)      |
| POST   | `/public-leads`         | PublicLeadController@store     | Validate + anti-spam + PRG                |
| GET    | `/leads`                | LeadController@index           | List + search + pagination + sort         |
| GET    | `/leads/create`         | LeadController@create          | Form them lead                            |
| POST   | `/leads/store`          | LeadController@store           | Validate + create + duplicate + PRG       |
| GET    | `/leads/edit?id=`       | LeadController@edit            | Form sua (du lieu cu)                      |
| POST   | `/leads/update`         | LeadController@update          | Validate + update + PRG                    |
| POST   | `/leads/delete`         | LeadController@delete          | **F2** Xoa MEM (soft delete, admin only)  |
| GET    | `/leads/export`         | LeadController@export          | **F4** Xuat CSV theo filter (BOM, an toan)|
| GET    | `/leads/convert?id=`    | LeadController@convert         | **F1** Form tao phieu thu tu lead (prefill)|
| GET    | `/leads/trash`          | LeadController@trash           | **F2** Thung rac lead (admin only)        |
| POST   | `/leads/restore`        | LeadController@restore         | **F2** Khoi phuc lead (admin only)        |
| POST   | `/leads/force-delete`   | LeadController@forceDelete     | **F2** Xoa vinh vien (admin only)         |
| GET    | `/orders`               | OrderController@index          | List + search + pagination + sort         |
| GET    | `/orders/create`        | OrderController@create         | Form tao phieu                            |
| POST   | `/orders/store`         | OrderController@store          | Validate + create (transaction) + PRG; **F1** nhan `from_lead_id` |
| GET    | `/orders/edit?id=`      | OrderController@edit           | Form sua phieu                            |
| POST   | `/orders/update`        | OrderController@update         | Validate + update + PRG                    |
| POST   | `/orders/delete`        | OrderController@delete         | **F2** Xoa MEM (soft delete, admin only)  |
| GET    | `/orders/export`        | OrderController@export         | **F4** Xuat CSV theo filter               |
| GET    | `/orders/trash`         | OrderController@trash          | **F2** Thung rac phieu (admin only)       |
| POST   | `/orders/restore`       | OrderController@restore        | **F2** Khoi phuc phieu (admin only)       |
| POST   | `/orders/force-delete`  | OrderController@forceDelete    | **F2** Xoa vinh vien (admin only)         |
| GET    | `/courses`              | CourseController@index         | **Module C** Grid khoa hoc + search/filter/sort/pagination |
| GET    | `/courses/view?id=`     | CourseController@view          | **Module C** Trang chi tiet (doc mo ta, click poster)      |
| GET    | `/courses/create`       | CourseController@create        | **Module C** Form them (manage_courses)   |
| POST   | `/courses/store`        | CourseController@store         | **Module C** Validate + create + PRG      |
| GET    | `/courses/edit?id=`     | CourseController@edit          | **Module C** Form sua (manage_courses)    |
| POST   | `/courses/update`       | CourseController@update        | **Module C** Validate + update + PRG      |
| POST   | `/courses/toggle`       | CourseController@toggle        | **Module C** Bat/tat hien thi             |
| POST   | `/courses/delete`       | CourseController@delete        | **Module C** Xoa mem (manage_courses)     |
| GET    | `/courses/export`       | CourseController@export        | **Module C** Xuat CSV theo filter         |
| GET    | `/courses/trash`        | CourseController@trash         | **Module C** Thung rac (manage_courses)   |
| POST   | `/courses/restore`      | CourseController@restore       | **Module C** Khoi phuc (manage_courses)   |
| POST   | `/courses/force-delete` | CourseController@forceDelete   | **Module C** Xoa vinh vien (admin only)   |
| POST   | `/leads/bulk`           | LeadController@bulk            | **F9** Hang loat: xoa mem / doi status (CSRF, role-gated) |
| POST   | `/orders/bulk`          | OrderController@bulk           | **F9** Hang loat: xoa mem / doi status (CSRF, role-gated) |
| GET    | `/orders/invoice?id=`   | OrderController@invoice        | **F10** Hoa don in duoc (print CSS, login) |
| GET    | `/api/leads`            | ApiController@leads            | **F7** JSON read-only (Bearer token, filter, pagination) |
| GET    | `/api/orders`           | ApiController@orders           | **F7** JSON read-only (Bearer token, filter, pagination) |
| GET    | `/api/stats`            | ApiController@stats            | **F7** JSON read-only tong hop so lieu     |
| GET    | `/audit`                | AuditController@index          | **F3** Nhat ky hoat dong (admin only, filter + phan trang) |
| GET    | `/health`               | HealthController@index         | JSON trang thai app/db                    |
| ANY    | (khong ton tai)         | Router                         | 404 Not Found                             |
| (sai method) | (route ton tai)   | Router                         | 405 Method Not Allowed + header Allow     |

## 5b. Tinh nang moi (F1-F5)

| # | Tinh nang | Mo ta | Bao mat |
|---|-----------|-------|---------|
| **F1** | Lead -> Order Conversion | Nut "Tao phieu thu" o lead `qualified`/`contacted` mo form prefilled. Submit: **1 transaction** INSERT order + UPDATE `leads.status='converted'`; rollback ca hai neu trung `order_code`. | CSRF; chi login; chan lead da `converted`/`lost`. |
| **F2** | Soft delete + Trash | Xoa = dat `deleted_at`. Moi list/query loc `deleted_at IS NULL`. `/leads/trash` + `/orders/trash` liet ke ban da xoa, **Khoi phuc** + **Xoa vinh vien**. | Soft delete: admin; force-delete + trash: **admin only** (enforce server-side). |
| **F3** | Audit log | Bang `activity_logs` ghi `create/update/delete/restore/convert/login/logout/login_failed`. `/audit` (admin) phan trang + loc theo user/action/entity. | Admin-only; KHONG log password/PII tho. |
| **F4** | CSV Export | `/leads/export` + `/orders/export` stream CSV theo dung filter (`q`/`status`), header `Content-Disposition: attachment`, **UTF-8 BOM** cho Excel, **chong CSV injection** (prefix `'` cho o bat dau `= + - @`). | Trong vung dang nhap. |
| **F5** | Dashboard Analytics | Lead/ngay 14 ngay (CSS bars), pheu chuyen doi new->contacted->qualified->converted, doanh thu 6 thang (paid), Top 5 khoa hoc. Khong dung thu vien JS chart. | Trong vung dang nhap. |

> **Import CSV (tuy chon trong FEATURE-DESIGN F4) duoc bo qua** (export da du minh chung data-portability).

## 5c. Tinh nang moi dot 2 (F6-F11)

| # | Tinh nang | Mo ta | Bao mat |
|---|-----------|-------|---------|
| **F6** | Login hardening | (a) **Lockout**: dem dang nhap sai theo username+IP (bang `login_attempts`); >= 5 lan / 10 phut -> khoa 15 phut, flash than thien, audit `login_failed`. (b) **Remember-me**: checkbox o form login; tao token ngau nhien, luu **SHA-256 hash** vao `remember_tokens(selector, token_hash, expires_at)`; cookie `selector:token` HttpOnly+SameSite=Lax+Secure-per-env; auto-login khi revisit, **ROTATE** moi lan dung, **REVOKE** khi logout. | KHONG BAO GIO luu password; hash_equals chong timing; rotate chong replay. |
| **F7** | Read-only JSON API | `GET /api/leads`, `/api/orders`, `/api/stats` tra JSON; yeu cau `Authorization: Bearer <token>` so voi hash trong `api_tokens`; pagination + filter (q/status); rate-limit theo IP (60 req / 60s, file bucket). Thieu/sai token -> 401 JSON. | Token luu HASH; chi doc; rate-limit; stateless (khong session). |
| **F8** | Advanced filter | Them `created_from`/`created_to` (date-range) cho list lead + order, ket hop q/status; **chip filter dang ap** + "Xoa tat ca"; SQL van prepared/bound. | Validate dinh dang ngay (YYYY-MM-DD); placeholder bound. |
| **F9** | Bulk actions + List UX | Checkbox chon nhieu dong -> **xoa mem hang loat** / **doi status hang loat** (`POST id[]`); chon **page-size** (10/20/50); sort-direction toggle tren tieu de cot. | CSRF; role-gated (`soft_delete`/`status_change`) enforce SERVER-SIDE; id bound PARAM_INT. |
| **F10** | Hoa don in duoc | `GET /orders/invoice?id=` -> trang HTML sach (print CSS) gom thong tin hoc vien + phieu + so tien + trang thai; nut "In hoa don" dung `window.print()`. | Yeu cau dang nhap. |
| **F11** | Role mo rong | `users.role` ENUM -> `admin`/`manager`/`staff`. Ma tran quyen: **admin** (tat ca + force-delete + audit + user mgmt), **manager** (status change + export + restore + soft-delete + trash), **staff** (create/update). Enforce SERVER-SIDE (`can()`/`require_can()` trong controller), an nut o view theo quyen. | Enforce o Controller (khong chi an nut); staff POST thang vao route manager/admin van bi chan. |

### Ma tran phan quyen (F11)

| Quyen \ Role           | admin | manager | staff |
|------------------------|:-----:|:-------:|:-----:|
| create / update        |  ✓   |   ✓    |  ✓   |
| soft-delete            |  ✓   |   ✓    |  ✗   |
| restore / trash        |  ✓   |   ✓    |  ✗   |
| status-change (bulk)   |  ✓   |   ✓    |  ✗   |
| export CSV             |  ✓   |   ✓    |  ✗   |
| force-delete           |  ✓   |   ✗    |  ✗   |
| audit / nhat ky        |  ✓   |   ✗    |  ✗   |
| **quan ly khoa hoc**   |  ✓   |   ✓    |  ✗   |

## 5d. Module C - Quan ly Khoa hoc (co hinh anh)

Bien "khoa hoc" tu whitelist tinh thanh **module CRUD** day du, phong phu cho bao cao:

- **Bang `courses`**: `name` (UNIQUE tren ban con song, giong M1), `slug`, `category`, `level`
  (beginner/intermediate/advanced), `price`, `duration_weeks`, `image`, `description`, `is_active`,
  soft delete (`deleted_at`). Seed 8 khoa hoc gan dung 8 anh trong `public/assets/img/courses/`.
- **Grid the co hinh anh** (`/courses`): search + loc theo nhom/trang thai + sort + phan trang; nut
  bat/tat hien thi, sua, xoa mem; **thung rac** + khoi phuc + xoa vinh vien (giong F2); **xuat CSV**
  (giong F4). Image picker chi chon file co san trong thu muc courses/ (**chong path traversal**).
- **Tich hop lien module**: dropdown khoa hoc o form Lead/Phieu hoc phi va **validate server-side**
  gio lay tu **cac khoa hoc dang MO trong DB** (`course_names()`), khong con hardcode. Neu bang chua
  migrate/DB loi -> fallback ve whitelist `config('courses')` (khong lam vo form cu).
- **Phan quyen** (F11 mo rong): them quyen `manage_courses` cho **admin + manager**; xoa vinh vien
  van chi **admin**. Enforce SERVER-SIDE trong controller (staff POST thang vao route van bi chan).
- **Trang chi tiet** (`/courses/view?id=`): click **poster** o grid mo trang doc - hero anh lon +
  gioi thieu day du + bang thong tin (nhom/trinh do/thoi luong/hoc phi) + **muc do quan tam**
  (so lead, so phieu, doanh thu da thu cho khoa hoc do). Moi user login deu xem duoc; chi
  admin/manager thay nut "Sua". (Trong app noi bo - KHONG phai landing page cong khai.)

## 6. Cau truc thu muc

```
EduCRM/
├── public/
│   ├── index.php            # Front Controller (entry point duy nhat)
│   └── assets/app.css       # Stylesheet
├── config/
│   ├── app.php              # Cau hinh app (debug, timeout, whitelist...)
│   └── database.php         # Thong tin ket noi MySQL
├── app/
│   ├── Core/
│   │   ├── Database.php                 # PDO singleton (utf8mb4, exception, no emulate)
│   │   ├── Router.php                   # Map METHOD+PATH; 404/405
│   │   ├── helpers.php                  # e, redirect, render, partial, flash, old, require_login, csrf...
│   │   └── DuplicateRecordException.php # Loi trung UNIQUE -> field error
│   ├── Controllers/        # THIN: doc request -> Service -> render/redirect (no SQL)
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PublicLeadController.php
│   │   ├── LeadController.php           # Module A
│   │   ├── OrderController.php          # Module B
│   │   └── HealthController.php
│   ├── Core/Csv.php        # F4: stream CSV an toan (BOM + chong formula injection)
│   ├── Services/           # Business rule: validate, pagination, duplicate, audit, conversion
│   │   ├── AuthService.php
│   │   ├── LeadService.php          # + convertToOrder (F1), soft delete (F2)
│   │   ├── OrderService.php
│   │   ├── AuditService.php         # F3: log() + list()
│   │   └── DashboardService.php     # F5: gom so lieu analytics
│   ├── Repositories/       # TAT CA SQL (prepared statement, khong doc $_POST)
│   │   ├── UserRepository.php
│   │   ├── LeadRepository.php       # + softDelete/restore/forceDelete/trash/export/updateStatus
│   │   ├── OrderRepository.php      # + insert (no-tx), soft delete, trash, export
│   │   ├── AuditRepository.php      # F3
│   │   └── StatsRepository.php      # F5 (GROUP BY aggregate)
│   └── Views/              # Chi hien thi, escape bang e()
│       ├── layouts/{main,auth,public,error}.php   # main = admin shell (sidebar)
│       ├── partials/{nav,flash}.php                # nav = sidebar inline-SVG icons
│       ├── auth/login.php
│       ├── dashboard/index.php                     # F5 charts
│       ├── leads/{index,create,edit,convert,trash}.php
│       ├── orders/{index,create,edit,trash}.php
│       ├── audit/index.php                         # F3
│       ├── public-leads/create.php
│       └── errors/{404,405,500}.php
├── database/{schema.sql, seed.sql, migrations_features.sql}
├── storage/logs/app.log     # Log loi DB / login failed / honeypot
├── vendor/                  # PSR-4 autoload (App\ -> app/)
└── README.md
```

## 7. Bao mat da ap dung

- **PDO chuan:** charset utf8mb4, `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`.
- **Prepared statements** cho moi du lieu tu GET/POST (chong SQL Injection).
- **Whitelist sort/direction** o ca Service va Repository (chong injection qua `?sort=`).
- **Session:** `session_set_cookie_params` (HttpOnly, SameSite=Lax, Secure khi HTTPS) truoc
  `session_start`; `session_regenerate_id(true)` sau login; idle timeout 30 phut; logout xoa
  session + cookie + destroy.
- **CSRF token** cho moi form POST create/update/delete/logout.
- **Anti-spam form cong khai:** honeypot field an + rate limit theo session.
- **Output escape** bang `e()` o moi View (chong XSS).
- **Duplicate handling:** UNIQUE constraint -> `DuplicateRecordException` -> loi than thien
  (khong lo SQLSTATE).
- **Role permission (enforce SERVER-SIDE):** chi `admin` duoc xoa/khoi phuc/xoa-vinh-vien va xem `/audit`.
  Cac controller kiem tra `is_admin()` trong action POST (khong chi an nut o UI) — staff POST thang vao
  route admin van bi chan + redirect.
- **F4 CSV an toan:** prefix `'` cho o bat dau `= + - @` (chong formula/CSV injection trong Excel/Sheets).
- **UI moi:** admin shell sidebar trai (inline-SVG icon, active pill + accent bar), top bar breadcrumb,
  the bo cuc bong mem 16px radius, hand-written CSS (khong framework ngoai), responsive (sidebar ->
  drawer tren `max-width:860px`), giu nguyen toan bo illustration (`loading="lazy"` + alt).

## 8. Luu y debug / production

- `config/app.php` co `'debug' => false` (production):
  - Khi DB loi, user chi thay trang **500 an toan** ("Da co loi xay ra"), KHONG lo
    SQLSTATE / ten bang / duong dan file / stack trace.
  - Chi tiet loi duoc ghi vao `storage/logs/app.log` (server-side).
- Doi `'debug' => true` khi dev de xem chi tiet loi tren man hinh.

## 9. /health (vi du)

```json
{
    "status": "ok",
    "app": "EduCRM",
    "domain": "Training Center CRM",
    "database": "up",
    "php_version": "8.2.12",
    "timestamp": "2026-06-10T17:54:00+02:00"
}
```
