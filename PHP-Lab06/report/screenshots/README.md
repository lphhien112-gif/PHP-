# Screenshot checklist - EduCRM Lab06 Final

Chay server: tu trong `EduCRM/` go `C:/xampp/php/php.exe -S 127.0.0.1:8106 -t public`
roi mo trinh duyet, chup cac man hinh sau (luu vao thu muc nay).

| # | Ten file goi y          | URL / Hanh dong                              | Minh chung cho |
|---|-------------------------|----------------------------------------------|----------------|
| 1 | 01-login.png            | GET /login                                   | T10, TC01      |
| 2 | 02-login-error.png      | POST /login sai mat khau                      | T10, TC02      |
| 3 | 03-dashboard.png        | GET /dashboard sau khi login                  | T06, TC03      |
| 4 | 04-public-form.png      | GET /public-leads/create                      | T07            |
| 5 | 05-public-error.png     | Submit form cong khai thieu field             | T07, TC07      |
| 6 | 06-public-success.png   | Submit hop le -> flash success (URL da redirect) | T08, TC09   |
| 7 | 07-leads-list.png       | GET /leads (list + search + pagination)       | T20, TC11      |
| 8 | 08-leads-create.png     | GET /leads/create                             | T20            |
| 9 | 09-leads-duplicate.png  | Tao lead trung email -> loi than thien        | T21, TC12      |
| 10| 10-leads-edit.png       | GET /leads/edit?id=1                           | TC13           |
| 11| 11-orders-list.png      | GET /orders                                   | T22, TC15      |
| 12| 12-orders-create.png    | GET /orders/create                            | T22            |
| 13| 13-orders-duplicate.png | Tao order trung order_code -> loi             | T23, TC16      |
| 14| 14-search-filter.png    | /leads?q=Tran&status=...                       | T24, TC17      |
| 15| 15-sort-malicious.png   | /leads?sort=id+DESC;DROP... (van 200)          | T25, TC20      |
| 16| 16-health.png           | GET /health (JSON)                            | T26, TC21      |
| 17| 17-405.png              | POST /health (405 + Allow)                     | T27, TC22      |
| 18| 18-404.png              | GET /unknown                                  | T27, TC23      |
| 19| 19-safe-error.png       | DB hong + debug=false -> trang 500 an toan     | T28, TC24      |
| 20| 20-explain.png          | EXPLAIN query list trong MySQL CLI             | T29, TC25      |
| 21| 21-vscode-tree.png      | Cay thu muc trong VS Code                      | T02            |
| 22| 22-git-log.png          | git log (>= 6 commit)                          | T30            |
| 23| 19-dashboard-kpi.png    | GET /dashboard - khoi "Xu huong kinh doanh" (3 KPI xu huong) | QW3   |
| 24| 20-analytics-data.png   | GET /analytics - chart co du lieu + khoi "Lead theo nguon"   | QW1, QW2 |
| 25| 21-ui-tieng-viet.png    | Mot man bat ky the hien UI tieng Viet CO DAU day du          | QW5   |
| 26| 22-course-create.png    | GET /courses/create - form them khoa hoc (anh + preview)     | Module C |
| 27| 23-course-view.png      | GET /courses/view?id=1 - chi tiet + "Muc do quan tam"        | Module C |
| 28| 24-course-trash.png     | GET /courses/trash - thung rac khoa hoc (soft delete)        | Module C |
| 29| 25-explain.png          | EXPLAIN chay that trong MariaDB CLI (3 query index)          | T29, TC25 |
| 30| 26-env.png              | Terminal: php -v + mysql SELECT 1 + cay thu muc + run server | T01, T02 |
| 31| 27-code.png             | Code that: public/index.php (routes) + Router.php dispatch   | T03      |
| 32| 28-schema.png           | MariaDB: SHOW TABLES + DESCRIBE leads + SELECT COUNT(*)      | T13, T14 |

Ghi chu Quick Wins: 3 anh 19/20/21 duoc tham chieu trong .tex qua macro `\figauto`
(tu nhung anh that neu file ton tai, nguoc lai ve placeholder de PDF van bien dich).
Ten file dat dung: `19-dashboard-kpi.png`, `20-analytics-data.png`, `21-ui-tieng-viet.png`.

Ghi chu: trong bao cao LaTeX, cac anh GUI duoc bieu dien bang macro `\screenshot{...}`
(khung placeholder) de PDF van bien dich duoc khi chua chup. Cac output dang text
(/health JSON, EXPLAIN, schema, code) duoc nhung truc tiep bang lstlisting.
