# WorkshopHub — Build Notes (Lab04)

Tai lieu noi bo ghi lai quyet dinh thiet ke, route, va **ket qua test that** (curl)
de viet bao cao. Sinh vien: Le Pham Hong Hien — 22110059.

## 1. Moi truong

- PHP 8.2.12 (cli) — `C:/xampp/php/php.exe`
- Chay: `php -S 127.0.0.1:8104 -t public` tu trong `WorkshopHub/`
- Khong co Composer global -> copy `vendor/` tu Lab03 BookNest, sua 2 file
  `autoload_psr4.php` + `autoload_static.php` de map `WorkshopHub\ => src/`.
  Da test: autoload nap dung class, app chay khong loi.

## 2. Quyet dinh thiet ke chinh

- **Domain:** Mini Workshop Registration Portal (WorkshopHub). Resource chinh = `registration`.
- **Redirect dung 303 See Other** cho PRG (chuan hon 302 cho POST->GET).
- **In-list validation:** `TOPICS` = [Web Development, Data Science, UI/UX Design, Mobile App, Cyber Security];
  `SESSIONS` = [Morning, Afternoon, Evening].
- **Phone rule:** `^0\d{9}$` (10 so, bat dau 0).
- **Honeypot field:** `website` (an bang CSS `.hp-field`, off-screen).
- **Rate limit:** 5 giay giua 2 submit thanh cong, luu `$_SESSION['_last_submit_at']`.
- **Session name:** `WSHSESSID`. Cookie: HttpOnly + SameSite=Lax + Secure(theo env).
- **Idle timeout:** 900s mac dinh; 10s khi co file `storage/.demo_timeout` hoac env `WSH_DEMO_TIMEOUT=1`.
- **regenerate id:** trong `Session::login()` (src/Support/Session.php) — goi `session_regenerate_id(true)`
  TRUOC khi luu user_id/role/login_at/last_activity_at.
- **logout_clean:** `Session::logoutClean()` — (1) `$_SESSION=[]`, (2) `setcookie(... expires past ...)`, (3) `session_destroy()`.

## 3. Vi tri code quan trong (de trich dan trong bao cao)

| Co che                     | File                                  | Ham/dong                          |
|----------------------------|---------------------------------------|-----------------------------------|
| Cookie params truoc start  | `src/Support/Session.php`             | `Session::start()`                |
| session_regenerate_id(true)| `src/Support/Session.php`             | `Session::login()`                |
| logout_clean               | `src/Support/Session.php`             | `Session::logoutClean()`          |
| idle timeout               | `src/Support/Session.php`             | `Session::checkIdleTimeout()`     |
| require_login              | `src/Support/helpers.php`             | `require_login()`                 |
| honeypot + rate limit      | `src/Controllers/RegistrationController.php` | `store()`                  |
| validation                 | `src/Support/Validator.php`           | toan bo                           |
| PRG redirect               | `src/Controllers/RegistrationController.php` | `redirect('/registrations')`|
| escape output              | `src/Support/helpers.php` + cac view  | `h()`                             |
| 404/405                    | `src/Router.php`                      | `dispatch()`                      |

## 4. KET QUA TEST THAT (curl) — chay tren 127.0.0.1:8104

### GET co ban
```
[GET /]                    -> 200
[GET /registrations]       -> 200
[GET /registrations/create]-> 200
[GET /login]               -> 200
[GET /health]              -> 200  (JSON: status ok, app WorkshopHub, php 8.2.12)
```

### T01/T05/T06 — PRG + flash mot lan
```
POST /registrations (valid) -> 303 loc=/registrations
Records: 3 -> 4 (them 1 ban ghi)
Refresh GET /registrations  -> 200 ; Records van = 4 (refresh GET khong tao trung)
flash-success xuat hien lan 1, refresh lan 2 dem duoc = 0 (hien dung mot lan)
```

### T02/T03 — validation rong / sai dinh dang
```
POST invalid (name=A, email=bad, phone=12, topic=Foo, session=) -> 303 loc=/registrations/create
Records KHONG doi (4 -> 4)
Field errors render:
  Ho ten phai co it nhat 2 ky tu.
  Email khong dung dinh dang email.
  So dien thoai phai gom 10 chu so va bat dau bang 0.
  Chu de khong hop le.
  Ca tham du la bat buoc.
```

### T04 — old input
```
Submit name dung + email sai -> back to form:
  name="full_name" value="Nguyen Van Long"   (giu lai)
  name="email" value="bademail"              (giu lai de sua)
  name="phone" value="0905551234"            (giu lai)
  <option value="Web Development" selected>   (giu lai)
Chi field email co loi "Email khong dung dinh dang email."
```

### T07 — honeypot
```
POST + website=http://spam -> 303 loc=/registrations
flash-error: "Yeu cau bi tu choi (phat hien hanh vi bot)."
Records KHONG doi (4 -> 4)
```

### T08 — rate limit
```
submit1 -> 303 loc=/registrations           (thanh cong, Records 4 -> 5)
submit2 (ngay lap tuc) -> 303 loc=/registrations/create
flash-error: "Ban gui qua nhanh. Vui long cho vai giay roi thu lai."
Records van = 5 (chi Rate One duoc luu)
```

### T09 — escape output (XSS)
```
POST full_name=<script>alert(1)</script>
Tren danh sach hien thi: &lt;script&gt;alert(1)&lt;/script&gt;
So the <script> tho (raw) trong HTML = 0  (khong chay script)
```

### T10 — login sai
```
POST /login (password=WRONG) -> 303 loc=/login
flash-error: "Email hoac mat khau khong dung."
```

### T11 — login dung + regenerate id
```
Pre-login  session id: j5ssp9gi70opb0r1t0dat7cvvr
POST /login (Workshop@2026) -> 303 loc=/dashboard
Post-login session id: dqja7arsq2iae3e8lr6iqu08sc   (DA DOI -> chong session fixation)
GET /dashboard authed -> 200
GET /session-demo -> hien user staff@workshophub.vn, role staff, login_at...
```

### T12 — dashboard protection
```
GET /dashboard (chua login) -> 303 loc=/login
flash-error: "Ban can dang nhap de truy cap khu vuc nay."
```

### T13 — logout sach
```
POST /logout -> 303 loc=/login
flash-success: "Ban da dang xuat. Hen gap lai!"
GET /dashboard sau logout -> 303 loc=/login (khong vao duoc nua)
```

### T14 — GET /logout
```
GET /logout -> 405 Method Not Allowed
Header: Allow: POST
(khong logout bang GET)
```

### T15 — idle timeout (bat demo 10s)
```
Bat file storage/.demo_timeout -> idle timeout = 10 giay
Login -> dashboard 200
Cho 12s (qua han) -> GET /dashboard -> 303 loc=/login
flash-warning: "Phien dang nhap da het han do khong hoat dong. Vui long dang nhap lai."
```

### T16 — 404 vs 405
```
GET /no-such                  -> 404 Not Found
POST /registrations/create    -> 405 Method Not Allowed ; Allow: GET
```

### Session cookie flags (Set-Cookie tren login)
```
Set-Cookie: WSHSESSID=...; path=/; HttpOnly; SameSite=Lax
(Secure VANG MAT vi dang chay HTTP local — dung mong doi)
```

## 5. Ghi chu sua loi trong qua trinh build

- **Loi flash timeout/logout khong hien:** `logoutClean()` destroy session + xoa cookie
  ("WSHSESSID=deleted"), sau do `flash_set` ghi vao session da chet -> mat flash.
  **Khac phuc:** them `Session::startFresh()` = `start()` + `session_regenerate_id(true)`
  de gui Set-Cookie hop le (ghi de cookie "deleted"), flash duoc luu va doc o /login.
  Da test lai: flash warning (T15) va flash success logout (T13) hien dung.
- **Env var WSH_DEMO_TIMEOUT khong vao PHP qua background `&`:** them co che file
  `storage/.demo_timeout` lam phuong an du phong on dinh hon khi test.
