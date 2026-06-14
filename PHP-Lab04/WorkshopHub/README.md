# WorkshopHub — Mini Workshop Registration Portal

> **PHP Lab04 — Secure Forms, PRG, Anti-spam & Session Login Flow**

- **Sinh vien:** Le Pham Hong Hien — **MSSV:** 22110059
- **Mon hoc:** Lap trinh Web — **GVHD:** TS. Tran Anh Tuan
- **Truong Dai hoc Khoa hoc Tu nhien, DHQG-HCM, Khoa Toan - Tin hoc**
- **GitHub:** https://github.com/lphhien112-gif/PHP-

WorkshopHub la cong dang ky tham du workshop danh cho khach tham quan, kem khu vuc
dang nhap + bang dieu khien danh rieng cho nhan vien. Ung dung minh hoa: form bao mat,
validation server-side, PRG, chong spam (honeypot + rate limit) va quan ly phien dang nhap.

Du lieu luu bang **file JSON** trong `storage/` (Lab04 khong dung database).

---

## 1. Yeu cau moi truong

- PHP >= 8.0 (da test tren PHP 8.2.12).
- Khong can Composer global: thu muc `vendor/` (PSR-4 autoload) da duoc commit kem.

## 2. Cach chay

```bash
# Tu trong thu muc WorkshopHub/
php -S 127.0.0.1:8104 -t public
```

Mo trinh duyet: <http://127.0.0.1:8104/>

## 3. Tai khoan demo (nhan vien)

| Truong   | Gia tri                  |
|----------|--------------------------|
| Email    | `staff@workshophub.vn`   |
| Mat khau | `Workshop@2026`          |

Mat khau duoc luu duoi dang **bcrypt hash** (`password_hash`) trong `storage/users.json`,
xac thuc bang `password_verify`.

## 4. Danh sach route

| Method | URL                       | Controller@Action                 | Y nghia                                   |
|--------|---------------------------|-----------------------------------|-------------------------------------------|
| GET    | `/`                       | HomeController@index              | Trang tong quan                           |
| GET    | `/registrations`          | RegistrationController@index      | Danh sach dang ky + flash                 |
| GET    | `/registrations/create`   | RegistrationController@create     | Form dang ky                              |
| POST   | `/registrations`          | RegistrationController@store      | Validate + anti-spam + luu JSON + PRG     |
| GET    | `/login`                  | AuthController@loginForm          | Form dang nhap                            |
| POST   | `/login`                  | AuthController@handleLogin        | Verify password + regenerate + redirect   |
| POST   | `/logout`                 | AuthController@logout             | Logout sach + redirect /login             |
| GET    | `/dashboard`              | DashboardController@index         | Chi cho user da dang nhap                 |
| GET    | `/session-demo`           | DashboardController@sessionDemo   | Hien thi thong tin session (debug)        |
| GET    | `/health`                 | HealthController@check            | JSON health check (lam them)              |
| ANY    | URL khong ton tai         | Router                            | 404 Not Found                             |
| -      | Route co that, sai method | Router                            | 405 Method Not Allowed (+ header Allow)   |

## 5. Tinh nang bao mat

- **Input safety:** doc `$_POST` qua `?? ''` + `trim()`; khong tin du lieu user.
- **Validation server-side:** required, email, phone (`^0\d{9}$`), do dai, in-list (chu de & ca).
- **Escape output:** moi du lieu in ra HTML qua `h()` (`htmlspecialchars`, `ENT_QUOTES`).
- **PRG:** POST thanh cong tra `303 See Other` -> GET, tranh re-submit khi refresh.
- **Flash message:** hien dung mot lan (luu trong session, doc xong xoa).
- **Honeypot:** field an `website`; co du lieu => chan nhu bot.
- **Rate limit:** chan submit thanh cong 2 lan trong 5 giay (luu `_last_submit_at` trong session).
- **Session cookie flags:** `HttpOnly`, `SameSite=Lax`, `Secure` theo moi truong — dat **truoc** `session_start()`.
- **Login:** `session_regenerate_id(true)` chong session fixation; luu `user_id`, `role`, `login_at`, `last_activity_at`.
- **Idle timeout:** 900s (mac dinh). Het han => logout sach + redirect `/login` kem flash.
- **Logout sach:** xoa session data, destroy session, xoa cookie phien.

### Bat che do timeout ngan de demo

Tao file rong `storage/.demo_timeout` (hoac dat bien moi truong `WSH_DEMO_TIMEOUT=1`)
=> idle timeout rut con **10 giay** de quay video/test. Xoa file de tro lai 900s.

## 6. Cau truc thu muc

```
WorkshopHub/
|-- composer.json                # PSR-4: "WorkshopHub\" => "src/"
|-- vendor/                       # Autoloader (commit kem)
|-- public/
|   |-- index.php                 # Front Controller
|   `-- assets/css/style.css      # CSS responsive
|-- src/
|   |-- Router.php                # 404/405
|   |-- Response.php              # html/json/redirect/view
|   |-- Support/
|   |   |-- helpers.php           # h, redirect, flash, old, require_login...
|   |   |-- Session.php           # cookie params, regenerate, logout_clean, timeout
|   |   |-- Storage.php           # doc/ghi JSON
|   |   `-- Validator.php         # required/email/phone/length/in-list
|   `-- Controllers/
|       |-- HomeController.php
|       |-- RegistrationController.php
|       |-- AuthController.php
|       |-- DashboardController.php
|       `-- HealthController.php
|-- views/                        # layout + cac view
`-- storage/
    |-- users.json                # tai khoan demo (bcrypt hash)
    `-- registrations.json        # du lieu mau
```

## 7. Da lam day du

Tat ca yeu cau bat buoc cua Lab04 + cac bai kiem tra **T01–T16** da duoc kiem chung
bang `curl` (xem `../report/build-notes.md`). Phan lam them: `/health` JSON, badge
trang thai, giao dien responsive, file demo timeout.
