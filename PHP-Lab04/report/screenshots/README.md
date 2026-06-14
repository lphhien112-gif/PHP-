# Screenshot Checklist — WorkshopHub (Lab04)

Chay app: `php -S 127.0.0.1:8104 -t public` (tu trong `WorkshopHub/`), roi chup cac man hinh sau.
Bao cao `.tex` hien dung macro `\screenshot{...}` lam placeholder; thay bang `\includegraphics`
neu da co anh that.

| # | Ten file goi y                  | Man hinh can chup                                                        |
|---|---------------------------------|--------------------------------------------------------------------------|
| 1 | `01-home.png`                   | GET `/` — trang chu (hero, features, danh sach chu de)                    |
| 2 | `02-list.png`                   | GET `/registrations` — bang danh sach + badge trang thai                  |
| 3 | `03-create-form.png`            | GET `/registrations/create` — form dang ky day du                        |
| 4 | `04-validation-errors.png`      | Submit form sai -> loi canh tung field + giu old input (T02/T03/T04)      |
| 5 | `05-flash-success.png`          | Sau submit hop le -> flash success tren `/registrations` (T01/T05/T06)    |
| 6 | `06-honeypot-blocked.png`       | DevTools dien field `website` -> flash error bot (T07)                    |
| 7 | `07-rate-limit.png`             | Submit 2 lan trong 5s -> flash "gui qua nhanh" (T08)                      |
| 8 | `08-xss-escaped.png`            | Nhap `<script>alert(1)</script>` -> hien dang text an toan (T09)          |
| 9 | `09-login.png`                  | GET `/login` — form dang nhap + goi y tai khoan demo                      |
|10 | `10-login-error.png`            | Login sai -> flash error (T10)                                            |
|11 | `11-dashboard.png`              | GET `/dashboard` sau login — thong ke + bang dang ky (T11)                |
|12 | `12-session-demo.png`           | GET `/session-demo` — session id + du lieu phien (T11)                    |
|13 | `13-dashboard-protected.png`    | GET `/dashboard` chua login -> redirect `/login` + flash (T12)            |
|14 | `14-404.png`                    | GET URL sai -> trang 404 (T16)                                            |
|15 | `15-405.png`                    | GET `/logout` hoac POST sai method -> 405 + Allow header (T14/T16)        |
|16 | `16-curl-tests.png`             | Terminal: ket qua curl T01-T16 (xem `../build-notes.md`)                  |

## Goi y chup nhanh bang DevTools / curl

- **Honeypot (T07):** mo DevTools -> Elements, xoa thuoc tinh an cua `#website` hoac dung
  Console: `document.querySelector('[name=website]').value='x'` roi submit.
- **Headers 404/405:** dung Network tab xem Status + Response Headers (Allow).
- **curl:** xem cac lenh trong `../build-notes.md` muc 4.
