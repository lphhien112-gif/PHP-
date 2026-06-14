
Lab06 (Final) - PHP Secure MVC Mini CRM Requirements
(Lưu ý : Final Lab chiếm 40% trên tổng điểm)
Câu 1 - Thực hiện lại Lab06 Final trên một mini project tương đương
Hãy thực hiện lại toàn bộ theo bài Lab06 Final hướng dẫn trên một ứng dụng mới hoặc biến thể mới có mức độ tương đương với Secure Mini CRM - Lead & Order Management Portal.
Sinh viên có thể chọn một trong các hướng sau hoặc đề xuất bài toán tương đương:
Training Center CRM: quản lý lead tư vấn, học viên tiềm năng và order/phiếu thanh toán khóa học.
Clinic Appointment CRM: quản lý bệnh nhân tiềm năng, lịch hẹn khám và mã lịch hẹn không trùng.
Equipment Rental CRM: quản lý khách thuê thiết bị, phiếu thuê và mã phiếu thuê không trùng.
Bookstore Order CRM: quản lý khách hàng, đơn sách và mã đơn hàng không trùng.
Repair Ticket CRM: quản lý khách sửa chữa, ticket hỗ trợ và mã ticket không trùng.
Event Registration CRM: quản lý người đăng ký, vé/đơn đăng ký và mã đăng ký không trùng.
Service Agency CRM: quản lý lead dịch vụ, hợp đồng/order dịch vụ và trạng thái chăm sóc.
Không được giữ nguyên toàn bộ bài mẫu nếu đã chọn bài toán khác. Sinh viên phải đổi tên project, database, bảng, cột, route, controller, service, repository, view, nội dung giao diện, dữ liệu mẫu và thông báo lỗi cho phù hợp bài toán mới.
1.1 Yêu cầu bắt buộc cần làm lại từ Lab06 Final
Tạo project PHP có Front Controller/Router giống tinh thần Lab03: mọi request đi qua public/index.php.
Tạo form công khai hoặc form tạo lead có đọc input an toàn, trim, validate server-side, giữ old input khi lỗi.
Áp dụng PRG sau POST thành công để tránh refresh tạo dữ liệu trùng.
Thêm anti-spam cơ bản cho form công khai: honeypot field và rate limit theo session.
Tạo login/logout flow bằng session: cookie flags, session_regenerate_id(true), timeout, flash message, logout sạch.
Thiết kế database có users và ít nhất 2 bảng nghiệp vụ chính, có primary key, unique constraint, index và timestamp.
Kết nối MySQL bằng PDO chuẩn: charset=utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC, ATTR_EMULATE_PREPARES=false.
Dùng prepared statements cho toàn bộ thao tác với dữ liệu từ GET/POST.
Xây dựng CRUD cho ít nhất 2 module nghiệp vụ: list, create, edit, update, delete bằng POST cho thao tác thay đổi dữ liệu.
Tạo search, pagination và sort an toàn bằng whitelist sort/direction, LIMIT/OFFSET đúng cách.
Dùng unique constraint để chặn trùng dữ liệu quan trọng và bắt duplicate key bằng exception/thông báo thân thiện.
Dùng EXPLAIN để kiểm tra index cho query list/filter/sort.
Refactor theo MVC: Controller mỏng, Service xử lý nghiệp vụ, Repository xử lý SQL, View chỉ hiển thị, Layout/Partial dùng chung.
Tạo 404, 405, 500; production không hiển thị SQLSTATE, tên bảng, đường dẫn file hoặc stack trace cho user.
Nộp GitHub kèm README, hướng dẫn chạy, tài khoản demo, danh sách route, test cases và ảnh màn hình.
1.2 Route tối thiểu bắt buộc
Method
URL mẫu
Controller@Action
Response / Ý nghĩa
GET
/
HomeController@index
Trang giới thiệu hoặc redirect dashboard nếu đã login
GET
/login
AuthController@login
HTML form login
POST
/login
AuthController@handleLogin
Validate + verify password + session regenerate + redirect
POST
/logout
AuthController@logout
Logout sạch + destroy session + redirect login
GET
/dashboard
DashboardController@index
Trang tổng quan yêu cầu đăng nhập
GET
/public-leads/create
PublicLeadController@create
Form công khai có honeypot/rate limit
POST
/public-leads
PublicLeadController@store
Validate + anti-spam + PRG
GET
/module-a
ModuleAController@index
List + search + pagination + sort safe
GET
/module-a/create
ModuleAController@create
Form thêm dữ liệu
POST
/module-a/store
ModuleAController@store
Validate + create + duplicate handling + PRG
GET
/module-a/edit?id=1
ModuleAController@edit
Form sửa có dữ liệu cũ
POST
/module-a/update
ModuleAController@update
Validate + update + PRG
POST
/module-a/delete
ModuleAController@delete
Delete bằng POST + PRG
GET
/module-b
ModuleBController@index
List + search + pagination + sort safe
GET
/module-b/create
ModuleBController@create
Form thêm dữ liệu
POST
/module-b/store
ModuleBController@store
Validate + create + duplicate handling + PRG
GET
/module-b/edit?id=1
ModuleBController@edit
Form sửa có dữ liệu cũ
POST
/module-b/update
ModuleBController@update
Validate + update + PRG
POST
/module-b/delete
ModuleBController@delete
Delete bằng POST + PRG
GET
/health
HealthController@index
JSON kiểm tra app/database
ANY
URL không tồn tại
Router
404 Not Found
Sai method
Route có tồn tại
Router
405 Method Not Allowed
Ghi chú: module-a/module-b phải đổi theo bài toán thật. Ví dụ Clinic App có thể dùng /patients và /appointments; Training Center CRM có thể dùng /leads và /orders.
1.3 Cấu trúc project bắt buộc
project/
├── public/
│   └── index.php
├── config/
│   ├── app.php
│   └── database.php
├── app/
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Router.php
│   │   ├── helpers.php
│   │   └── DuplicateRecordException.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ModuleAController.php
│   │   └── ModuleBController.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── ModuleAService.php
│   │   └── ModuleBService.php
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── ModuleARepository.php
│   │   └── ModuleBRepository.php
│   └── Views/
│       ├── layouts/main.php
│       ├── partials/nav.php
│       ├── partials/flash.php
│       ├── auth/login.php
│       ├── dashboard/index.php
│       ├── module-a/index.php, create.php, edit.php
│       └── module-b/index.php, create.php, edit.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── storage/logs/
└── README.md
1.4 Sinh viên phải đổi toàn bộ theo bài toán mới
Tên project
Tên database
Tên bảng/cột
Tên class
Tên controller/service/repository
Tên route
Tên view
Dữ liệu mẫu
Thông báo lỗi
Nội dung dashboard
Nội dung JSON /health
Commit message
README và ảnh chụp màn hình
Câu 1A - Checklist task nhỏ bắt buộc để chứng minh hiểu bài
Sinh viên phải hoàn thành và chụp minh chứng cho từng task nhỏ sau. Đây là phần bắt buộc nhằm đảm bảo sinh viên hiểu các kỹ thuật tổng hợp từ Week 4 đến Week 8.
Mã
Task kiểm tra
Yêu cầu thực hiện
Minh chứng cần nộp
T01
Setup môi trường
Chạy php -v, kiểm tra MySQL/MariaDB, Git và browser/Postman/curl.
Ảnh terminal hoặc mô tả môi trường.
T02
Tạo project đúng cấu trúc
Có public, config, app/Core, Controllers, Services, Repositories, Views, database, storage/logs.
Ảnh VS Code Explorer.
T03
Front Controller + Router
public/index.php là entry point, route table có GET/POST và fallback 404/405.
Ảnh code public/index.php/Router.
T04
Session cookie setup
session_set_cookie_params() chạy trước session_start(), có HttpOnly, SameSite, Secure theo môi trường.
Ảnh code session setup.
T05
Helpers cơ bản
Có e(), redirect(), render(), partial(), flash()/get_flash(), old(), require_login().
Ảnh code helpers.
T06
Layout/Partial
Có layout chung, nav, flash partial; view con không lặp HTML header/footer.
Ảnh code layout + giao diện.
T07
Public form secure
Form công khai đọc POST, trim, validate server-side, hiển thị lỗi đúng field, giữ old input.
Ảnh form lỗi.
T08
PRG form công khai
Sau submit hợp lệ phải redirect; F5 không tạo dữ liệu trùng.
Ảnh URL sau redirect + dữ liệu.
T09
Honeypot/rate limit
Field ẩn bị điền hoặc submit quá nhanh phải bị chặn.
Ảnh code và test.
T10
Login sai/đúng
Login sai báo lỗi; login đúng redirect dashboard.
Ảnh login test.
T11
Session regenerate
Sau login thành công có session_regenerate_id(true).
Ảnh code AuthController/AuthService.
T12
Timeout/logout sạch
Timeout redirect login; logout xóa session data, destroy session, xóa cookie session.
Ảnh code và test logout.
T13
Database schema
Có users + 2 bảng nghiệp vụ, primary key, unique, index, created_at/updated_at.
Ảnh schema.sql.
T14
Seed dữ liệu
Có tài khoản demo và ít nhất 20 dòng dữ liệu mỗi module để test pagination.
Ảnh seed hoặc SELECT COUNT(*).
T15
PDO chuẩn
Database.php có charset=utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false.
Ảnh code Database.php.
T16
Repository prepared statements
Không nối trực tiếp input vào SQL; dùng prepare/execute/bindValue.
Ảnh code Repository.
T17
Service layer
Validate, pagination, duplicate handling nằm ở Service; Controller không ôm business rule dài.
Ảnh Service.
T18
Controller mỏng
Controller chỉ đọc request, gọi Service, render/redirect; không viết SQL.
Ảnh Controller.
T19
View an toàn
View chỉ hiển thị dữ liệu, mọi output từ DB/user input dùng e().
Ảnh View.
T20
Module A CRUD
List/Create/Edit/Update/Delete chạy đúng, POST cho create/update/delete.
Ảnh module A.
T21
Module A duplicate
Trùng unique key báo lỗi thân thiện, không lộ SQLSTATE.
Ảnh duplicate error.
T22
Module B CRUD
List/Create/Edit/Update/Delete chạy đúng theo rule riêng của module B.
Ảnh module B.
T23
Module B duplicate
Trùng unique key của module B báo lỗi đúng field.
Ảnh duplicate error.
T24
Search/pagination/sort
URL q/page/sort/direction hoạt động; page âm/quá lớn được xử lý.
Ảnh URL và list.
T25
Sort nguy hiểm
Thử sort=id DESC; DROP TABLE..., hệ thống dùng sort mặc định và không lỗi SQL.
Ảnh test URL.
T26
Health JSON
GET /health trả JSON app/database status.
Ảnh browser/Postman/curl.
T27
404/405
/unknown trả 404; POST /health hoặc sai method trả 405.
Ảnh test.
T28
Production safe error
debug=false không hiện SQLSTATE/đường dẫn file khi DB lỗi.
Ảnh safe error.
T29
EXPLAIN/index
Chạy EXPLAIN cho query list/filter/sort và nhận xét key/index.
Ảnh EXPLAIN.
T30
GitHub/README
README có cách chạy, tạo DB, route, tài khoản demo, ảnh màn hình; git log tối thiểu 6 commit.
Link GitHub + ảnh git log.
Câu 1B - Test cases bắt buộc trong báo cáo
Sinh viên phải tạo bảng Test Result trong báo cáo. Mỗi dòng cần có: Test case, Cách test, Kết quả mong đợi, Kết quả thực tế, Ảnh minh chứng, Pass/Fail.
Mã test
Cách test
Kết quả mong đợi
TC01
GET /login
Form login hiển thị, không yêu cầu session.
TC02
Login sai mật khẩu
Hiện lỗi thân thiện, không tạo session user.
TC03
Login đúng
Redirect /dashboard, session user được tạo, flash hiện 1 lần.
TC04
Truy cập /dashboard khi chưa login
Redirect /login.
TC05
Logout
Destroy session, không truy cập dashboard nếu chưa login lại.
TC06
Timeout phiên
User không hoạt động quá thời gian quy định bị yêu cầu login lại.
TC07
Public form thiếu required field
Hiện lỗi cạnh field và giữ old input.
TC08
Public form honeypot
Nếu field ẩn có dữ liệu, request bị từ chối.
TC09
Public form submit hợp lệ
Redirect theo PRG, flash success, F5 không tạo trùng.
TC10
Module A create thiếu required field
Không lưu DB, hiển thị lỗi đúng field.
TC11
Module A create hợp lệ
Redirect list, flash success, DB có dòng mới.
TC12
Module A duplicate unique key
Hiện lỗi thân thiện, không lộ SQLSTATE.
TC13
Module A edit/update
Form lấy dữ liệu cũ theo id; update thành công redirect list.
TC14
Module A delete bằng POST
Xóa/soft-delete thành công, GET delete không được dùng.
TC15
Module B create hợp lệ
Redirect list, flash success.
TC16
Module B duplicate unique key
Hiện lỗi đúng field.
TC17
Search /module-a?q=...
Chỉ hiển thị dữ liệu khớp từ khóa.
TC18
Page âm/quá lớn
Page được chuẩn hóa về 1 hoặc totalPages.
TC19
Sort hợp lệ
Danh sách sort đúng cột và direction hợp lệ.
TC20
Sort nguy hiểm
Không chạy SQL nguy hiểm, dùng sort mặc định.
TC21
GET /health
JSON status app/db.
TC22
POST /health
405 Method Not Allowed.
TC23
GET /unknown
404 Not Found.
TC24
DB lỗi trong production
Không hiện SQLSTATE/tên bảng/path; ghi log hoặc safe message.
TC25
EXPLAIN query list
Giảng viên kiểm tra query có index phù hợp khi filter/sort.
Câu 2 - Problem Solving
Hãy trả lời các câu hỏi Problem Solving sau dựa trên chính mini project của em. Không trả lời chung chung; mỗi câu cần có ví dụ cụ thể từ project, route, schema hoặc code của em.
1. Front Controller & Router
Trong project của em, mọi request có đi qua public/index.php không? Router đang map METHOD + PATH -> Controller@Action như thế nào? Vì sao nếu mỗi file PHP tự xử lý một URL riêng thì project sẽ rối khi thêm auth, middleware, 404/405?
2. Secure form
Form công khai hoặc form tạo lead của em kiểm tra dữ liệu server-side như thế nào? Vì sao không thể chỉ dựa vào required/type=email trên HTML?
3. PRG Pattern
Sau POST thành công, project redirect về route nào? Nếu render kết quả trực tiếp trên request POST thì user bấm F5 có thể gây hậu quả gì?
4. Anti-spam cơ bản
Honeypot và rate limit trong bài của em đang chặn hành vi nào? Hai kỹ thuật này có giới hạn gì và khi nào cần nâng cấp lên CSRF/reCAPTCHA/rate limit theo IP?
5. Session/login flow
Hãy mô tả flow login đúng trong project: validate input -> verify password -> session_regenerate_id(true) -> set session -> flash -> redirect. Nếu không regenerate sau login thì rủi ro gì?
6. Logout, timeout và cookie flags
Logout sạch trong project xóa những gì? Timeout xử lý thế nào? HttpOnly, SameSite, Secure giúp giảm rủi ro gì?
7. Remember me
Nếu có checkbox remember me, vì sao không được lưu password vào cookie? Nếu muốn làm thật, em sẽ dùng token như thế nào?
8. Database schema
Vì sao em thiết kế các bảng hiện tại? Hãy chỉ ra primary key, foreign key nếu có, unique constraint, index và timestamp trong schema của em.
9. Prepared statements
Chọn 1 câu INSERT và 1 câu SELECT trong Repository của em. Phân tích SQL command và user input được tách riêng như thế nào để tránh SQL Injection.
10. Unique constraint & duplicate handling
Vì sao chỉ kiểm tra trùng bằng PHP là chưa đủ? Hãy mô tả tình huống 2 request cùng submit dữ liệu trùng và database unique constraint giúp gì.
11. Search/pagination/sort safe
URL list của em dùng những tham số nào? Em xử lý page âm, page quá lớn, sort/direction không hợp lệ và sort nguy hiểm như thế nào?
12. Index & EXPLAIN
Đưa 1 query list/filter/sort trong project và kết quả EXPLAIN. Cột key có dùng index không? Nếu key=NULL trên bảng lớn, em sẽ cải tiến gì?
13. MVC đúng trách nhiệm
Trong project của em, Controller, Service, Repository, View đang làm gì? Hãy chỉ ra một ví dụ cụ thể chứng minh Controller không viết SQL và View không query DB.
14. Layout/Partial & XSS
Vì sao nên dùng layout/partial thay vì lặp header/menu/footer ở nhiều view? Dữ liệu từ DB/user input được escape ở đâu để tránh XSS?
15. Dev/prod error message
Vì sao production không nên hiển thị $e->getMessage(), SQLSTATE hoặc stack trace? Project của em ghi log và hiển thị safe message như thế nào?
16. 404 vs 405
Hãy đưa ví dụ route không tồn tại trả 404 và route tồn tại nhưng sai method trả 405 trong project của em. Vì sao cần phân biệt hai loại lỗi này?
17. Delete bằng POST
Vì sao delete/update không nên dùng GET? Phân tích rủi ro nếu crawler, preview link hoặc user click nhầm vào URL delete.
18. Hướng phát triển thật
Nếu phát triển project thành hệ thống thật, em sẽ ưu tiên nâng cấp gì trước: CSRF, role permission, soft delete, transaction, logging, audit trail, API, tests, hay Docker? Giải thích theo bài toán của em.
Câu 3 - Bài làm thêm khuyến khích
Các phần sau không bắt buộc, nhưng được cộng điểm nếu làm đúng và có minh chứng rõ ràng:
Thêm CSRF token cho tất cả form POST create/update/delete/logout.
Thêm role permission: admin được delete, staff chỉ create/update trạng thái.
Thêm soft delete bằng deleted_at thay vì DELETE thật.
Thêm transaction khi tạo order kèm order_items hoặc payment record.
Thêm filter theo status/date range bên cạnh search q.
Thêm dashboard thống kê số lead mới, order, doanh thu và trạng thái hệ thống.
Thêm logging vào storage/logs/app.log cho lỗi DB và login failed.
Thêm seed_data.php tạo 100-500 dòng để test pagination/index.
Thêm test script hoặc checklist tự động bằng curl/Postman collection.
Cải thiện UI nhưng vẫn giữ đúng kiến trúc MVC và flow kỹ thuật.
Rubric chấm điểm gợi ý
Hạng mục
Điểm
Tiêu chí
Setup, Front Controller, Router, 404/405, GitHub/README
10 điểm
Project chạy được, route rõ, 404/405 đúng, README đủ hướng dẫn.
Secure form, validation, PRG, honeypot/rate limit
15 điểm
Form an toàn, validate server-side, old input, PRG, chống spam cơ bản.
Login/session/logout/timeout/cookie flags
15 điểm
Login đúng, regenerate, flash, timeout, logout sạch, không lưu password cookie.
Database schema, PDO, Repository, prepared statements
15 điểm
Schema có users + 2 bảng, unique/index, PDO chuẩn, không SQL injection.
CRUD module A và module B
15 điểm
List/Create/Edit/Update/Delete chạy đúng, validate, duplicate handling, delete POST.
Search, pagination, sort whitelist, EXPLAIN/index
10 điểm
q/page/sort/direction an toàn, xử lý input xấu, có EXPLAIN và nhận xét index.
MVC đúng trách nhiệm, layout/partial, safe error
10 điểm
Controller mỏng, Service rule, Repository SQL, View escape, production error safe.
Test cases và minh chứng
5 điểm
Có bảng test result, ảnh chụp, pass/fail rõ ràng.
Problem Solving
5 điểm
Trả lời có lập luận, gắn trực tiếp với project, không chung chung.
Yêu cầu nộp bài
Ghi chú bài thực hành: PHP Lab06 Final
Tiêu đề email hoặc LMS: MSSV_HoTenSinhVien_PHP_Lab06_Final
File báo cáo: MSSV_HoTenSinhVien_PHP_Lab06_Final.pdf
Link GitHub project đặt trong báo cáo và trong nội dung email/LMS.
Báo cáo phải có: mô tả project, schema, route table, ảnh màn hình, bảng test cases, EXPLAIN, Problem Solving, phần đã làm đủ/chưa đủ.
README GitHub phải có: cách tạo DB, cách chạy server, tài khoản demo, danh sách route, cấu trúc folder, lưu ý debug/prod.
Nếu nộp trễ, ghi rõ lý do nộp trễ.
Deadline và nơi nộp: 05/07/2026 qua email  tuantran261083course@gmail.com 
Checklist cuối trước khi nộp
[ ] Project chạy được bằng php -S localhost:8000 -t public.
[ ] Có public/index.php và Router xử lý 404/405.
[ ] Có login/logout/session regenerate/timeout/logout sạch.
[ ] Có form secure với validate server-side, PRG, old input, flash.
[ ] Có honeypot/rate limit cho form public.
[ ] Có database users + 2 bảng nghiệp vụ, unique/index/timestamps.
[ ] PDO cấu hình đúng charset=utf8mb4 và exception mode.
[ ] Không ghép trực tiếp GET/POST vào SQL.
[ ] Controller không SQL; View không query DB; Repository không đọc POST.
[ ] Mọi output từ DB/user input trong View dùng e().
[ ] Module A và B có CRUD đầy đủ.
[ ] Search/pagination/sort whitelist hoạt động và chống input nguy hiểm.
[ ] Duplicate key hiển thị lỗi thân thiện, không lộ SQLSTATE.
[ ] Production safe error không lộ tên bảng/path/stack trace.
[ ] Có EXPLAIN query và nhận xét index.
[ ] Có bảng test result TC01-TC25.
[ ] Có trả lời Problem Solving gắn với project.
[ ] GitHub có README, seed/schema, ảnh minh chứng và commit rõ ràng.

