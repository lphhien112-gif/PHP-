# Screenshot checklist - ClinicDesk (PHP Lab05)

Chup va luu vao thu muc nay theo dung ten file. Bao cao .tex dung macro
`\screenshot{...}` lam placeholder; neu muon chen anh that, doi macro sang
`\includegraphics`.

Khoi dong truoc khi chup:
```
C:/xampp/mysql/bin/mysqld.exe        (chay nen)
cd ClinicDesk
C:/xampp/php/php.exe -S 127.0.0.1:8105 -t public
```

| File | Noi dung can chup | Tuong ung |
|------|-------------------|-----------|
| 01-env.png       | `php -v` + `mysql --version` (hoac SELECT VERSION()) | T01 |
| 02-structure.png | VS Code Explorer cay thu muc ClinicDesk | T02 |
| 03-schema.png    | phpMyAdmin/Workbench 3 bang + index, hoac SHOW CREATE TABLE | T03 |
| 04-seed.png      | SELECT COUNT(*) patients=22, appointments=24 | T04 |
| 05-health.png    | Browser/curl GET /health JSON status=ok database=connected | T06, TC01 |
| 06-patients-list.png | GET /patients (bang + search + pagination + sort) | T09, TC02 |
| 07-patient-create.png | GET /patients/create form | T10 |
| 08-patient-success.png | Sau khi tao -> /patients flash success | T10, TC03 |
| 09-patient-duplicate.png | Form bao loi trung email, giu input | T11, TC05 |
| 10-patient-missing.png | Form bao loi thieu required field | TC04 |
| 11-patient-edit.png | GET /patients/edit?id= form co du lieu cu | T12, TC06 |
| 12-patient-updated.png | /patients sau update + flash | T12, TC07 |
| 13-patient-delete.png | Confirm dialog xoa + list sau xoa | T13, TC08 |
| 14-appointments-list.png | GET /appointments list + filter status | T14, TC09 |
| 15-appt-create-dup.png | Tao lich hen + loi trung appointment_code | T15, TC10 |
| 16-page-invalid.png | URL page=-5 va page=9999 deu duoc clamp | T16, TC14 |
| 17-sort-malicious.png | URL sort=id;DROP TABLE... van chay binh thuong | T17, TC13 |
| 18-error-500.png | DB sai -> 500 safe message (khong lo SQLSTATE) | T18, TC15 |
| 19-explain.png   | Ket qua EXPLAIN + ghi key/index | T19 |
| 20-git-log.png   | git log --oneline (>= 5 commit) hoac GitHub | T20 |
| 21-404.png       | GET /unknown -> 404 | TC11 |
| 22-405.png       | POST /health -> 405 Allow: GET | TC12 |

Tat ca ket qua thuc te (status code, JSON, EXPLAIN, log) da ghi trong
`report/build-notes.md` va trich vao bao cao.
