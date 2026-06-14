# EduCRM - Build Notes (Lab06 Final)

Sinh vien: Le Pham Hong Hien - MSSV: 22110059
Mon hoc: Lap trinh Web - GVHD: TS. Tran Anh Tuan
App: EduCRM - Training Center CRM. DB: training_center_crm.

## 1. Quyet dinh kien truc

- Domain chon: Training Center CRM (quan ly lead tu van + phieu hoc phi). KHONG dung bai mau generic.
- Module A = leads (/leads), Module B = orders (/orders). Form cong khai = /public-leads/create + POST /public-leads.
- Namespace PSR-4: `App\` -> `app/`. Copy vendor cua BookNest, sua autoload_psr4.php + autoload_static.php.
- MVC discipline xac nhan bang grep:
  - Controllers: KHONG co SQL (grep `->prepare|->query|INSERT|DELETE` => No files found).
  - Views: KHONG co SQL.
  - Repositories/Services: KHONG doc `$_POST/$_GET` (chi xuat hien trong comment).
  - Controller thin: doc request -> goi Service -> render/redirect.
  - Service: validate, pagination math, whitelist sort, duplicate handling.
  - Repository: TAT CA SQL, prepared statement, whitelist cot sort lan 2.
- Lazy DB connect: Repository KHONG ket noi trong constructor (chi mo khi co query)
  => loi DB nem ra TRONG dispatch, duoc try/catch o Front Controller bao boc an toan.

## 2. PDO chuan (app/Core/Database.php)
- DSN: mysql:host=127.0.0.1;port=3306;dbname=training_center_crm;charset=utf8mb4
- ATTR_ERRMODE = ERRMODE_EXCEPTION
- ATTR_DEFAULT_FETCH_MODE = FETCH_ASSOC
- ATTR_EMULATE_PREPARES = false (prepared statement native MySQL)

LUU Y quan trong: voi EMULATE_PREPARES=false, MySQL KHONG cho dung lai mot named
placeholder nhieu lan. Ban dau dung `:q` 3 lan trong LIKE -> loi HY093 "Invalid parameter
number". Da sua: moi cot mot placeholder rieng (:q_name, :q_email, :q_phone).

## 3. Schema (database/schema.sql)
- users(id PK, username UNIQUE, password_hash, full_name, role ENUM(admin,staff), created_at, updated_at)
- leads(id PK, full_name, email UNIQUE, phone, course, source, status ENUM, note,
        created_at, updated_at; INDEX idx_leads_status, idx_leads_course, idx_leads_created_at)
- orders(id PK, order_code UNIQUE, lead_id FK->leads(id) ON DELETE CASCADE, course, amount DECIMAL,
         status ENUM, paid_at, note, created_at, updated_at;
         INDEX idx_orders_status, idx_orders_lead_id, idx_orders_created_at)
- Charset utf8mb4_unicode_ci. Engine InnoDB (ho tro FK + transaction).

Seed: admin/admin123 (role admin), staff/staff123 (role staff). 25 leads + 25 orders.

## 4. Route table (16 routes + 404/405)
GET  /                      -> redirect (dashboard neu login, nguoc lai /public-leads/create)
GET  /login                 -> AuthController@loginForm
POST /login                 -> AuthController@handleLogin
POST /logout                -> AuthController@logout
GET  /dashboard             -> DashboardController@index (require_login)
GET  /public-leads/create   -> PublicLeadController@create
POST /public-leads          -> PublicLeadController@store (honeypot + rate limit + PRG)
GET  /leads                 -> LeadController@index (search/sort/pagination)
GET  /leads/create          -> LeadController@create
POST /leads/store           -> LeadController@store
GET  /leads/edit            -> LeadController@edit
POST /leads/update          -> LeadController@update
POST /leads/delete          -> LeadController@delete (admin only)
GET  /orders                -> OrderController@index
GET  /orders/create         -> OrderController@create
POST /orders/store          -> OrderController@store
GET  /orders/edit           -> OrderController@edit
POST /orders/update         -> OrderController@update
POST /orders/delete         -> OrderController@delete (admin only)
GET  /health                -> HealthController@index (JSON)

## 5. Ket qua test thuc te (curl, server 127.0.0.1:8106)

### Auth
- GET /dashboard chua login  => HTTP 302 -> /login (TC04 PASS)
- POST /login sai password    => HTTP 302 -> /login, log "Login failed" (TC02 PASS)
- POST /login dung            => HTTP 302 -> /dashboard (TC03 PASS)
- GET /dashboard sau login    => HTTP 200, hien "Tong quan", "Doanh thu da thu" (PASS)
- POST /logout                => HTTP 302 -> /login; sau do GET /dashboard => 302 /login (TC05 PASS)

### Module A - Leads
- create valid     => HTTP 302 -> /leads (TC11 PASS)
- create duplicate => HTTP 302 -> /leads/create, loi "Email nay da ton tai trong he thong." (TC12 PASS, khong lo SQLSTATE)
- create missing   => HTTP 302, 4 field-error hien thi, old input giu lai (TC10 PASS)
- edit             => HTTP 200, form co du lieu cu (email testnvz999@gmail.com) (TC13 PASS)
- update           => HTTP 302 -> /leads, DB: full_name='Test UPDATED Name', course='Data Science' (TC13 PASS)
- GET /leads/delete=> HTTP 405 (delete chi POST) (TC14 PASS)
- POST /leads/delete (admin) => HTTP 302, row count = 0 (TC14 PASS)

### Module B - Orders
- create valid     => HTTP 302 -> /orders (TC15 PASS)
- create duplicate => HTTP 302 -> /orders/create, loi "Ma phieu nay da ton tai." (TC16 PASS)
- search q=TEST1   => 1 row (ORD-2026-TEST1)
- search by lead name q=Nguyen => 2 rows
- filter status=paid => 7 rows
- edit             => HTTP 200, form co ORD-2026-TEST1
- update amount    => HTTP 302, DB amount=9000000.00
- delete (admin)   => HTTP 302, row count = 0

### Search / Pagination / Sort
- leads q=Tran     => 2 rows (Tran Thi Bich,...) (TC17 PASS)
- leads q=ZZZNOMATCH => "Khong co lead nao khop dieu kien", 0 rows
- page=-5          => Trang 1/3 (clamp ve 1) (TC18 PASS)
- page=9999        => Trang 3/3 (clamp ve totalPages) (TC18 PASS)
- sort=full_name&direction=asc => HTTP 200 (TC19 PASS)
- sort="id DESC; DROP TABLE leads;--"&direction=hacker => HTTP 200, dung sort mac dinh,
  bang leads VAN CON 26 rows (sau khi them test) -> KHONG chay SQL nguy hiem (TC20/TC25 PASS)

### Public form (anti-spam)
- missing required => HTTP 302 -> /public-leads/create, 4 field-error, old input "bademail" giu lai (TC07 PASS)
- honeypot filled (website=...) => HTTP 302, leads count truoc=25 sau=25 (KHONG insert) (TC08 PASS)
- valid            => HTTP 302 (PRG), DB row: status=new, source=website (TC09 PASS)

### Role permission (lam them)
- staff login -> POST /leads/delete => HTTP 302 -> /leads, row VAN CON (staff khong duoc xoa) PASS

### Health / 404 / 405 / Safe error
- GET /health         => HTTP 200 JSON {status:ok, database:up} (TC21 PASS)
- POST /health        => HTTP 405, header "Allow: GET" (TC22 PASS)
- GET /unknown-page-xyz => HTTP 404, "Khong tim thay trang" (TC23 PASS)
- DB hong (doi config sang db sai):
  - /health => HTTP 503 JSON {status:degraded, database:down}, KHONG lo SQLSTATE (TC24 PASS)
  - /leads  => HTTP 500 trang an toan "Da co loi xay ra", grep SQLSTATE=0, path=0, table=0 (TC24/T28 PASS)
  - storage/logs/app.log ghi: "ERROR: SQLSTATE[HY000] [1049] Unknown database ... @ Database.php:50"
    => chi tiet luu server-side, user KHONG thay.

## 6. /health JSON thuc te (DB up)
```
{
    "status": "ok",
    "app": "EduCRM",
    "domain": "Training Center CRM",
    "database": "up",
    "php_version": "8.2.12",
    "timestamp": "2026-06-10T17:54:00+02:00"
}
```

## 7. EXPLAIN thuc te (TC29 / TC25)

### Query list leads filter status + sort created_at
```
EXPLAIN SELECT id, full_name, email, phone, course, source, status, note, created_at
FROM leads WHERE status='new' ORDER BY created_at DESC LIMIT 10 OFFSET 0;
```
| id | select_type | table | type | possible_keys    | key              | key_len | ref   | rows | Extra |
|----|-------------|-------|------|------------------|------------------|---------|-------|------|-------|
| 1  | SIMPLE      | leads | ref  | idx_leads_status | idx_leads_status | 1       | const | 6    | Using index condition; Using where; Using filesort |

Nhan xet: key = idx_leads_status (KHONG NULL) -> MySQL dung index loc status thay vi
full scan. "Using filesort" do sort theo created_at khac cot loc; voi bang nho chap nhan
duoc. Tren bang lon co the them composite index (status, created_at) de bo filesort.

### Lookup theo UNIQUE email
```
EXPLAIN SELECT * FROM leads WHERE email='nguyenvanan1@gmail.com';
```
| id | select_type | table | type  | possible_keys  | key            | key_len | ref   | rows | Extra |
|----|-------------|-------|-------|----------------|----------------|---------|-------|------|-------|
| 1  | SIMPLE      | leads | const | uq_leads_email | uq_leads_email | 602     | const | 1    |       |

Nhan xet: type=const, key=uq_leads_email -> tra cuu trung email O(1) qua unique index.

### Orders JOIN leads filter status + sort
```
EXPLAIN SELECT o.id, o.order_code, l.full_name
FROM orders o JOIN leads l ON l.id=o.lead_id WHERE o.status='paid' ORDER BY o.created_at DESC LIMIT 10;
```
| id | table | type   | possible_keys                        | key               | ref                           | rows | Extra |
|----|-------|--------|--------------------------------------|-------------------|-------------------------------|------|-------|
| 1  | o     | ref    | idx_orders_status,idx_orders_lead_id | idx_orders_status | const                         | 6    | Using index condition; Using where; Using filesort |
| 1  | l     | eq_ref | PRIMARY                              | PRIMARY           | training_center_crm.o.lead_id | 1    |       |

Nhan xet: bang o dung idx_orders_status (ref); JOIN toi leads qua PRIMARY (eq_ref, 1 row).
Khong co full table scan.

## 8. Bug da gap & sua trong qua trinh build (de trung thuc trong bao cao)
1. HY093 Invalid parameter number: reuse `:q` placeholder voi native prepares.
   Sua: moi cot mot placeholder rieng.
2. 1054 Unknown column 'l.full_name' o countFiltered orders: query COUNT thieu JOIN leads.
   Sua: them JOIN leads vao count query.
3. Fatal error lo stack trace khi DB hong: do Repository ket noi DB trong constructor
   (chay luc dang ky route, TRUOC try/catch). Sua: lazy connect - chi mo ket noi khi co query.

## 9. Lenh chay
```
# Tao DB + seed
C:/xampp/mysql/bin/mysql.exe -u root < database/schema.sql
C:/xampp/mysql/bin/mysql.exe -u root training_center_crm < database/seed.sql
# Chay server (tu trong EduCRM/)
C:/xampp/php/php.exe -S 127.0.0.1:8106 -t public
```
