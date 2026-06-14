# ClinicDesk - Mini Clinic Appointment DB App (PHP Lab05)

- Sinh vien: **Le Pham Hong Hien** - MSSV: **22110059**
- Mon hoc: Lap trinh Web - GVHD: **TS. Tran Anh Tuan**
- Truong Dai hoc Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc
- GitHub: https://github.com/lphhien112-gif/PHP-

ClinicDesk quan ly **benh nhan** (`/patients`) va **lich hen kham** (`/appointments`)
voi day du CRUD, tim kiem, phan trang, sap xep an toan (whitelist), unique constraint
chong trung, va xu ly loi an toan. Kien truc:

```
Browser -> public/index.php (Front Controller) -> Router -> Controller
        -> Repository -> PDO -> MySQL -> View / Redirect -> Browser
```

## 1. Yeu cau

- PHP >= 8.1 (da test 8.2.12 - XAMPP)
- MySQL/MariaDB (da test MariaDB 10.4.32 - XAMPP, user `root`, khong mat khau)

## 2. Cau truc thu muc

```
ClinicDesk/
  composer.json              # PSR-4: "ClinicDesk\\" => "app/"
  public/
    index.php                # Front Controller (entry point duy nhat)
    .htaccess                # rewrite -> index.php (cho Apache)
    assets/css/style.css
  config/
    app.php                  # ten app, debug, per_page, log path
    database.php             # thong tin ket noi (tach khoi controller/view)
  app/
    Core/                    # Database, Router, Response, View, Paginator, Flash, Logger
    Controllers/             # Home, Health, Patient, Appointment
    Repositories/            # PatientRepository, AppointmentRepository (toan bo SQL o day)
    Views/                   # layout, home, patients/*, appointments/*, errors/*
  database/
    schema.sql               # 3 bang: users, patients, appointments (PK/UNIQUE/INDEX)
    seed.sql                 # 22 patients, 24 appointments, 1 user
    seed_data.php            # (bonus) sinh 100-300 ban ghi
  storage/logs/app.log       # log loi DB
  vendor/                    # autoload PSR-4 (da map ClinicDesk\ -> app/)
```

## 3. Cai dat Database

MySQL phai dang chay. Tu thu muc `ClinicDesk/`:

```bash
# Tao DB + 3 bang
C:/xampp/mysql/bin/mysql.exe -u root < database/schema.sql

# Nap du lieu mau (22 patients, 24 appointments)
C:/xampp/mysql/bin/mysql.exe -u root < database/seed.sql

# (Tuy chon - bonus) sinh them ~250 patients, 400 appointments de test EXPLAIN
C:/xampp/php/php.exe database/seed_data.php 250 400
```

Cau hinh ket noi nam o `config/database.php`. Co the doi qua bien moi truong:
`DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS`.

## 4. Chay ung dung

```bash
C:/xampp/php/php.exe -S 127.0.0.1:8105 -t public
```

Mo trinh duyet: http://127.0.0.1:8105

Bat debug (hien loi chi tiet luc phat trien):
```bash
# PowerShell
$env:APP_DEBUG="1"; C:/xampp/php/php.exe -S 127.0.0.1:8105 -t public
```

## 5. Cac route

| Method | URL | Controller@Action | Y nghia |
|--------|-----|-------------------|---------|
| GET  | `/` | HomeController@index | Dashboard 2 module |
| GET  | `/health` | HealthController@index | JSON kiem tra DB |
| GET  | `/patients` | PatientController@index | List + search + sort + pagination |
| GET  | `/patients/create` | PatientController@create | Form tao moi |
| POST | `/patients/store` | PatientController@store | Validate + INSERT + redirect |
| GET  | `/patients/edit?id=` | PatientController@edit | Form sua co du lieu cu |
| POST | `/patients/update` | PatientController@update | Validate + UPDATE + redirect |
| POST | `/patients/delete` | PatientController@delete | Xoa bang POST + redirect |
| GET  | `/appointments` | AppointmentController@index | List + search + status filter + sort + pagination |
| GET  | `/appointments/create` | AppointmentController@create | Form tao moi |
| POST | `/appointments/store` | AppointmentController@store | Validate + INSERT + redirect |
| GET  | `/appointments/edit?id=` | AppointmentController@edit | Form sua |
| POST | `/appointments/update` | AppointmentController@update | Validate + UPDATE + redirect |
| POST | `/appointments/delete` | AppointmentController@delete | Xoa bang POST + redirect |
| ANY  | (khong ton tai) | Router | 404 Not Found |
| (sai method) | (route ton tai) | Router | 405 Method Not Allowed + Allow |

Tham so list URL: `q` (tim kiem), `page` (phan trang), `sort` (cot, whitelist),
`direction` (asc/desc), `status` (chi appointments - filter trang thai).

## 6. Demo nhanh (curl)

```bash
curl http://127.0.0.1:8105/health
curl "http://127.0.0.1:8105/patients?q=Nguyen&sort=name&direction=ASC&page=1"
curl "http://127.0.0.1:8105/appointments?status=confirmed&sort=appointment_date"
```

## 7. Diem ky thuat noi bat

- **PDO**: DSN `charset=utf8mb4`; `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `ATTR_EMULATE_PREPARES=false`.
- **Repository**: 100% SQL nam trong Repository, dung `prepare()` + `bindValue()` + `execute()`.
- **Safe sort**: ORDER BY chi nhan cot/huong tu whitelist, khong lay thang tu `$_GET`.
- **Pagination**: clamp `page<=0` va `page>max`.
- **Unique**: `patients.email`, `appointments.appointment_code` UNIQUE; bat SQLSTATE 23000 -> loi field-level.
- **PRG**: moi create/update/delete thanh cong deu redirect (302).
- **Safe error**: production khong lo SQLSTATE; ghi log `storage/logs/app.log`, hien safe message.
