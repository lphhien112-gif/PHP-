# ClinicDesk - Build Notes (PHP Lab05)

Sinh vien: Le Pham Hong Hien - MSSV: 22110059
App: ClinicDesk - Mini Clinic Appointment DB App
DB: clinic_appointment_db (MariaDB 10.4.32 / XAMPP, PHP 8.2.12)
Run: `php -S 127.0.0.1:8105 -t public` tu trong ClinicDesk/

## Kien truc
Browser -> public/index.php (Front Controller) -> Router -> Controller -> Repository -> PDO -> MySQL -> View/Redirect.

Namespace PSR-4: `ClinicDesk\` -> `app/`. Da copy vendor tu Lab03 BookNest va edit:
- vendor/composer/autoload_psr4.php : 'ClinicDesk\\' => app
- vendor/composer/autoload_static.php : prefixLengths 'C'=>['ClinicDesk\\'=>11], prefixDirs -> /app
- autoload_classmap.php giu nguyen (chi co Composer\InstalledVersions).

## Cau truc thu muc
ClinicDesk/
  composer.json (psr-4 ClinicDesk\ => app/)
  public/index.php (.htaccess), public/assets/css/style.css
  config/app.php, config/database.php
  app/Core/ Database.php Router.php Response.php View.php Paginator.php Flash.php Logger.php
  app/Controllers/ HomeController HealthController PatientController AppointmentController
  app/Repositories/ PatientRepository AppointmentRepository
  app/Views/ layout.php home.php patients/{index,create,edit,_form}.php appointments/{...} errors/{404,405,500}.php
  database/ schema.sql seed.sql seed_data.php
  storage/logs/app.log
  vendor/ (PSR-4 autoload da edit)

## Database schema (3 bang)
- users(id PK, username UNIQUE, full_name, role, password_hash, created_at, updated_at)
- patients(id PK, name, email UNIQUE, phone, gender ENUM, address, created_at, updated_at)
    INDEX: uq_patients_email (UNIQUE), idx_patients_phone, idx_patients_created_at
- appointments(id PK, appointment_code UNIQUE, patient_name, patient_email, department,
               appointment_date, status ENUM, note, created_at, updated_at)
    INDEX: uq_appointments_code (UNIQUE), idx_appointments_status_created (status,created_at),
           idx_appointments_date, idx_appointments_code
Charset: utf8mb4 / utf8mb4_unicode_ci, ENGINE=InnoDB.

Seed clean: 22 patients, 24 appointments, 1 user admin.
seed_data.php (bonus) sinh them 100-300 ban ghi.

## PDO config (config/database.php + app/Core/Database.php)
DSN: mysql:host=127.0.0.1;port=3306;dbname=clinic_appointment_db;charset=utf8mb4
Options: ERRMODE_EXCEPTION, DEFAULT_FETCH_MODE=FETCH_ASSOC, ATTR_EMULATE_PREPARES=false.
Database = Singleton (1 ket noi tai su dung).

## Whitelist sort
PatientRepository::SORTABLE = id,name,email,created_at ; DEFAULT=created_at DESC.
AppointmentRepository::SORTABLE = id,appointment_code,patient_name,appointment_date,status,created_at.
resolveSort() tra ten cot that tu mang whitelist (sort khong hop le -> created_at).
resolveDirection() chi cho ASC/DESC (khac -> DESC).
=> ORDER BY chi noi ten cot da qua whitelist; q/page khong bao gio vao ORDER BY.

## Pagination (app/Core/Paginator.php)
per_page = 5. Clamp page < 1 -> 1; page > totalPages -> totalPages.
offset = (page-1)*perPage. LIMIT/OFFSET bind PARAM_INT.

## Duplicate handling
emailExists()/codeExists() la kiem tra mem. Chot chan that = UNIQUE constraint.
INSERT/UPDATE bat PDOException; neu getCode()==='23000' -> loi field-level (email / appointment_code),
giu lai input cu, HTTP 409. PRG sau khi thanh cong.

## Safe error
Router::runHandler() bat Throwable -> Logger ghi chi tiet vao storage/logs/app.log -> Response::serverError().
errors/500.php: production chi hien safe message; APP_DEBUG=1 moi hien [DEBUG] detail.

=========================================================================
## KET QUA TEST THUC TE (curl tren 127.0.0.1:8105)
=========================================================================

### TC01 / T01 Moi truong
- PHP 8.2.12 (C:/xampp/php/php.exe), MariaDB 10.4.32 (C:/xampp/mysql).
- `mysql -u root -e "SELECT VERSION();"` => 10.4.32-MariaDB.

### TC01 GET /health => HTTP 200
{
    "status": "ok",
    "app": "ClinicDesk",
    "module": "Mini Clinic Appointment DB App",
    "database": "connected",
    "db_name": "clinic_appointment_db",
    "php_version": "8.2.12",
    "timestamp": "2026-06-10 22:53:00"
}

### TC02 GET /patients => HTTP 200, list + search + sort + pagination OK.
- page 2 hien ID #17..#15 (per_page=5).

### TC03 POST /patients/store hop le => HTTP 302 Location: /patients
- DB: id=23 name='Test Patient Curl' email='test.curl@example.com' (verified).

### TC04 POST /patients/store thieu required => HTTP 422
- Field errors: "Vui long nhap ho ten", "Vui long nhap email", "Vui long nhap so dien thoai". Khong insert.

### TC05 POST /patients/store trung email => HTTP 409
- "Email nay da ton tai. Vui long dung email khac." + giu input (value="Another One").

### TC06 GET /patients/edit?id=23 => HTTP 200, form co value="test.curl@example.com".
- edit?id=99999 => HTTP 404.

### TC07 POST /patients/update => HTTP 302 Location: /patients
- DB: name='Test Patient Updated' address='456 New Addr' (verified).

### TC08 POST /patients/delete (id=23) => HTTP 302 Location: /patients
- DB: COUNT WHERE id=23 = 0 (da xoa). GET /patients/delete => HTTP 405 Allow: POST.

### TC09 GET /appointments => HTTP 200.
- create appointment APT-2026-9001 => 302; DB datetime '2026-06-25 09:30:00' (datetime-local -> Y-m-d H:i:s OK).

### TC10 POST /appointments/store trung appointment_code (APT-2026-0001) => HTTP 409
- "Ma lich hen nay da ton tai. Vui long dung ma khac."

### Filter/search/sort appointments
- status=confirmed => "Tong 9 lich hen". q=APT-2026-0003 => match. sort=appointment_date&direction=ASC => 200.

### TC11 GET /unknown-page => HTTP 404.
### TC12 POST /health => HTTP 405, header Allow: GET.

### TC13 sort=id;DROP TABLE patients => HTTP 200 (fall back DEFAULT created_at), DB COUNT patients = 22 (intact).
### TC14 page=-5 => "trang 1/5" ; page=9999 => "trang 5/5" (clamp).

### TC15 / T18 DB loi (DB_NAME=wrong_db_xyz, APP_DEBUG=0)
- GET /patients => HTTP 500, hien "Da co loi xay ra" + "Loi da duoc ghi log"; grep SQLSTATE|Unknown database|PDOException = 0 (KHONG lo).
- GET /health => HTTP 503, database=disconnected.
- storage/logs/app.log:
    [2026-06-10 22:55:50] ERROR: PDOException: SQLSTATE[HY000] [1049] Unknown database 'wrong_db_xyz' in .../app/Core/Database.php:43
    [2026-06-10 22:56:06] ERROR: Health check DB that bai: SQLSTATE[HY000] [1049] Unknown database 'wrong_db_xyz'
- APP_DEBUG=1: trang 500 hien them <code>SQLSTATE[HY000] [1049] Unknown database 'wrong_db_xyz'</code> (chung minh toggle dung).

### Render check: /, /patients, /patients/create, /appointments, /appointments/create,
   /patients/edit?id=1, /appointments/edit?id=1 => deu HTTP 200, 0 PHP Warning/Notice.
   q=zzzznomatch => "Khong co benh nhan nao khop" (empty state OK).

=========================================================================
## EXPLAIN (T19 / Cau 2.9) - du lieu THUC TE
=========================================================================

### A) Filter status + sort created_at (query chinh cua /appointments?status=confirmed)
EXPLAIN SELECT id, appointment_code, patient_name, appointment_date, status
        FROM appointments WHERE status='confirmed' ORDER BY created_at DESC LIMIT 5 OFFSET 0;
-> table=appointments  type=ref  possible_keys=idx_appointments_status_created
   key=idx_appointments_status_created  key_len=1  ref=const  rows=9  Extra=Using where
   => DUNG composite index (status, created_at). Khong full scan.

(Tren bang lon 424 dong: rows=125, van key=idx_appointments_status_created.)

### B) Tra cuu theo appointment_code (search chinh xac / unique)
EXPLAIN SELECT id, appointment_code FROM appointments WHERE appointment_code='APT-2026-0003';
-> type=const  key=uq_appointments_code  key_len=82  rows=1  Extra=Using index.

### C) patients lookup email (unique)
EXPLAIN SELECT id FROM patients WHERE email='an.nguyen@example.com';
-> type=const  key=uq_patients_email  key_len=602  rows=1  Extra=Using index.

### D) patients lookup phone (idx_patients_phone)
EXPLAIN SELECT id,name,phone FROM patients WHERE phone='0901000001';
-> type=ref  key=idx_patients_phone  key_len=82  rows=1  Extra=Using index condition.

### E) patients ORDER BY created_at LIMIT (truong hop key=NULL)
EXPLAIN SELECT id,name,email FROM patients ORDER BY created_at DESC LIMIT 5;
-> type=ALL  key=NULL  rows=22(/272)  Extra=Using filesort.
   => Bang nho/khong co WHERE, optimizer chon full scan + filesort thay vi di index.
   Cai tien: them WHERE created_at >= ? (range) hoac FORCE INDEX:
   - WHERE created_at>='2026-05-01' ORDER BY created_at DESC LIMIT 5
       -> type=range key=idx_patients_created_at rows=105 Extra=Using where.
   - FORCE INDEX(idx_patients_created_at) ORDER BY created_at DESC LIMIT 5
       -> type=index key=idx_patients_created_at rows=5 (quet 5 dong thay vi 272 + filesort).
   => Index created_at CO dung duoc cho sort; chi la optimizer tu chon khi du lieu nho.

NHAN XET KEY: Cac query loc/tra cuu chinh deu dung index (ref/const). Composite
(status, created_at) phuc vu dung mau "filter roi sort moi nhat". Khi key=NULL tren
bang lon -> bo sung WHERE chon loc hoac index phu hop de tranh filesort.

## Routes (14 + 404/405)
GET  /                       HomeController@index
GET  /health                 HealthController@index (JSON)
GET  /patients               PatientController@index
GET  /patients/create        PatientController@create
POST /patients/store         PatientController@store
GET  /patients/edit?id=      PatientController@edit
POST /patients/update        PatientController@update
POST /patients/delete        PatientController@delete
GET  /appointments           AppointmentController@index
GET  /appointments/create    AppointmentController@create
POST /appointments/store     AppointmentController@store
GET  /appointments/edit?id=  AppointmentController@edit
POST /appointments/update    AppointmentController@update
POST /appointments/delete    AppointmentController@delete
ANY  (khong ton tai)         Router -> 404
(sai method)                 Router -> 405 + Allow

## Lam them (bonus) da co
- seed_data.php sinh 100-300 ban ghi.
- Filter theo status ben canh search q (appointments).
- Sort direction toggle asc/desc tren header cot (ca 2 module).
- Logging vao storage/logs/app.log khi DB loi.
- bang users + password_hash bcrypt san sang cho auth/role sau nay.
- Confirm dialog (JS) truoc khi POST delete.
