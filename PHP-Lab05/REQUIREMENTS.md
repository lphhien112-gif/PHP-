
Lab05 - PHP Database CRUD Requirements
Câu 1 - Thực hiện lại Lab05 trên một ứng dụng mới tương đương
Hãy thực hiện lại toàn bộ theo bài Lab05 hướng dẫn trên một ứng dụng khác có mức độ tương đương với Mini Lead & Order Database Management App.
Sinh viên được tự chọn một trong các hướng sau hoặc đề xuất bài toán tương đương:
Mini Course Enrollment DB App: quản lý học viên đăng ký khóa học và hóa đơn/đơn đăng ký.
Mini Clinic Appointment DB App: quản lý bệnh nhân, lịch hẹn và mã lịch hẹn không trùng.
Mini Equipment Rental DB App: quản lý thiết bị, phiếu mượn/trả và mã phiếu không trùng.
Mini Bookstore Order DB App: quản lý khách hàng tiềm năng, đơn sách và mã đơn hàng.
Mini Training Center CRM DB App: quản lý lead tư vấn, đơn thanh toán học phí và trạng thái chăm sóc.
Mini Medical Supplies Order DB App: quản lý vật tư, yêu cầu mua hàng và mã yêu cầu không trùng.
Không được giữ nguyên toàn bộ bài mẫu Lab05 nếu đã chọn bài toán khác. Sinh viên phải đổi tên project, tên bảng, tên dữ liệu, route, controller, repository, view, field, thông báo lỗi và nội dung giao diện cho phù hợp với bài toán mới.
1.1 Yêu cầu bắt buộc cần làm lại từ Lab05
Kiểm tra môi trường PHP, MySQL/MariaDB, Git, VS Code và công cụ test như browser/curl/Postman.
Tạo project PHP có cấu trúc rõ ràng, kế thừa tinh thần Lab03: Browser -> public/index.php -> Router -> Controller -> Repository -> PDO -> MySQL -> View/Redirect -> Browser.
Tạo database riêng cho bài làm, có ít nhất 3 bảng: users và 2 bảng nghiệp vụ chính của ứng dụng mới.
Thiết kế khóa chính, unique constraint và index phù hợp với bài toán.
Tạo config/app.php và config/database.php, tách thông tin kết nối khỏi Controller/View.
Kết nối MySQL bằng PDO chuẩn: charset=utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC, ATTR_EMULATE_PREPARES=false.
Tạo Database class để quản lý kết nối.
Tạo Repository cho 2 module nghiệp vụ chính, toàn bộ SQL nằm trong Repository.
Dùng prepared statements cho INSERT, SELECT, UPDATE, DELETE.
Tạo CRUD cơ bản cho 2 module: List, Create, Edit, Update, Delete.
Tạo search, pagination và sort an toàn bằng whitelist sort/direction.
Dùng unique constraint để chặn trùng dữ liệu quan trọng, ví dụ email lead, mã đơn hàng, mã lịch hẹn, SKU hoặc mã phiếu.
Bắt lỗi duplicate key và hiển thị thông báo dễ hiểu ở đúng field.
Dùng PRG Pattern sau POST thành công: create/update/delete xong phải redirect.
Tạo error view 404, 405, 500; production không hiển thị SQLSTATE hoặc lỗi kỹ thuật thô.
Dùng EXPLAIN để kiểm tra query list/search/sort có dùng index hợp lý không.
Commit code bằng Git và đưa project lên GitHub.
1.2 Ứng dụng mới phải có ít nhất các route sau
Method
URL mẫu
Controller@Action
Response / Ý nghĩa
GET
/
HomeController@index
HTML Dashboard giới thiệu module
GET
/health
HealthController@index
JSON kiểm tra DB status
GET
/module-a
ModuleAController@index
List + search + pagination + sort
GET
/module-a/create
ModuleAController@create
Form tạo mới
POST
/module-a/store
ModuleAController@store
Validate + Repository INSERT + redirect/form lỗi
GET
/module-a/edit?id=1
ModuleAController@edit
Form sửa có dữ liệu cũ
POST
/module-a/update
ModuleAController@update
Validate + UPDATE + redirect/form lỗi
POST
/module-a/delete
ModuleAController@delete
Delete bằng POST + redirect
GET
/module-b
ModuleBController@index
List + search + pagination + sort
GET
/module-b/create
ModuleBController@create
Form tạo mới
POST
/module-b/store
ModuleBController@store
Validate + Repository INSERT + redirect/form lỗi
GET
/module-b/edit?id=1
ModuleBController@edit
Form sửa có dữ liệu cũ
POST
/module-b/update
ModuleBController@update
Validate + UPDATE + redirect/form lỗi
POST
/module-b/delete
ModuleBController@delete
Delete bằng POST + redirect
ANY
URL không tồn tại
Router
404 Not Found
Sai method
Route có tồn tại
Router
405 Method Not Allowed
Ghi chú: module-a/module-b phải được đổi theo bài toán thật. Ví dụ nếu chọn Clinic Appointment App thì có thể là /patients và /appointments. Nếu chọn Course Enrollment App thì có thể là /students và /enrollments.
1.3 Sinh viên phải đổi toàn bộ theo bài toán mới
Tên project
Tên database
Tên bảng
Tên cột
Dữ liệu mẫu
Tên class
Tên controller
Tên repository
Tên action
Tên route
Tên view
Nội dung giao diện
Thông báo lỗi
Nội dung JSON /health
Commit message
Ảnh chụp màn hình minh chứng
1.4 Ví dụ chuyển đổi bài toán
Ví dụ nếu chọn Mini Clinic Appointment DB App, hệ thống có thể quản lý:
patients: id, name, email, phone, gender, created_at, updated_at; email có thể unique nếu yêu cầu.
appointments: id, appointment_code, patient_name, patient_email, appointment_date, status, note; appointment_code unique.
Route gợi ý: GET /patients, GET /patients/create, POST /patients/store, GET /appointments, GET /appointments/create, POST /appointments/store.
Search gợi ý: patient name/email/phone, appointment code/patient name/email.
Index gợi ý: email, phone, status + created_at, appointment_date, appointment_code.
Câu 1A - Các phần nhỏ bắt buộc để kiểm tra sinh viên hiểu bài
Sinh viên phải hoàn thành và chụp minh chứng cho từng task nhỏ sau. Đây không phải phần làm thêm; đây là checklist bắt buộc để đảm bảo hiểu các khái niệm của Lab05.
Mã
Task kiểm tra
Yêu cầu thực hiện
Minh chứng cần nộp
T01
Kiểm tra môi trường
Chạy php -v, mysql --version hoặc chứng minh MySQL đang chạy.
Ảnh terminal hoặc mô tả môi trường.
T02
Tạo cấu trúc project
Tạo public, config, app/Core, app/Controllers, app/Repositories, app/Views, database, storage/logs.
Ảnh VS Code Explorer.
T03
Tạo database schema
Tạo database riêng và ít nhất 3 bảng, có primary key, unique key, index.
Ảnh phpMyAdmin/MySQL Workbench hoặc đoạn SQL schema.
T04
Seed dữ liệu
Thêm ít nhất 15 bản ghi cho mỗi module chính để test pagination.
Ảnh SELECT COUNT(*) hoặc bảng dữ liệu.
T05
Kiểm tra PDO config
DSN có charset=utf8mb4; options có ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false.
Ảnh code Database.php và giải thích ngắn.
T06
Health check DB
GET /health trả JSON status ok nếu DB kết nối được.
Ảnh browser/Postman/curl.
T07
Repository không nối chuỗi SQL nguy hiểm
INSERT/SELECT/UPDATE/DELETE dùng prepare() và execute().
Ảnh code Repository.
T08
ORDER BY an toàn
Sort và direction phải qua whitelist, không lấy thẳng từ $_GET.
Ảnh code whitelist.
T09
List module A
Hiển thị danh sách, chỉ SELECT cột cần hiển thị, có search/pagination.
Ảnh giao diện list.
T10
Create module A hợp lệ
Submit form hợp lệ, lưu DB, redirect về list và hiện flash success.
Ảnh trước/sau submit.
T11
Duplicate module A
Tạo dữ liệu trùng unique key, hệ thống báo lỗi thân thiện và giữ dữ liệu cũ.
Ảnh form lỗi.
T12
Edit/Update module A
Mở form sửa bằng id, cập nhật hợp lệ, redirect về list.
Ảnh form edit và list sau update.
T13
Delete module A bằng POST
Xóa bằng form POST, có confirm; không xóa bằng GET link.
Ảnh code view hoặc test.
T14
List module B
Hiển thị danh sách module B có search/pagination/sort.
Ảnh giao diện list.
T15
Create + duplicate module B
Tạo mới hợp lệ và test trùng unique key của module B.
Ảnh success và lỗi duplicate.
T16
Page âm hoặc page quá lớn
URL page=-5 hoặc page lớn phải được xử lý về giới hạn hợp lệ.
Ảnh URL và kết quả.
T17
Sort nguy hiểm
Thử sort=id DESC; DROP TABLE..., hệ thống không bị lỗi và dùng sort mặc định.
Ảnh URL hoặc mô tả test.
T18
Safe DB error
Tạm sửa sai password DB; production/debug false không hiện SQLSTATE cho user.
Ảnh màn hình lỗi 500 safe message.
T19
EXPLAIN query
Chạy EXPLAIN cho query list/filter/sort của ít nhất 1 module.
Ảnh kết quả EXPLAIN và ghi key có dùng index nào.
T20
Git và GitHub
Commit tối thiểu 5 lần theo các mốc: setup, schema, repository, CRUD, tests.
Ảnh git log --oneline hoặc link GitHub.
Câu 1B - Test cases bắt buộc trong báo cáo
Sinh viên phải tạo bảng Test Result trong báo cáo của mình. Mỗi dòng cần có: Test case, Cách test, Kết quả mong đợi, Kết quả thực tế, Ảnh minh chứng, Pass/Fail.
Mã test
Cách test
Kết quả mong đợi
TC01
GET /health
JSON status=ok, database=connected.
TC02
GET /module-a
Danh sách có search, pagination, sort và không lỗi.
TC03
POST create module A hợp lệ
Redirect về list, hiện flash success, DB có dữ liệu mới.
TC04
POST create module A thiếu required field
Hiển thị lỗi đúng field, không insert DB.
TC05
POST create module A trùng unique key
Báo lỗi thân thiện, giữ dữ liệu cũ.
TC06
GET edit module A với id hợp lệ
Form edit hiển thị dữ liệu cũ.
TC07
POST update module A hợp lệ
Redirect về list, dữ liệu được cập nhật.
TC08
POST delete module A
Xóa/ẩn dữ liệu và redirect về list.
TC09
GET /module-b
Danh sách module B có search/pagination/sort.
TC10
POST create module B trùng unique key
Báo lỗi đúng field.
TC11
URL không tồn tại
404 Not Found.
TC12
Sai method với route có tồn tại
405 Method Not Allowed.
TC13
Sort nguy hiểm qua URL
Không chạy SQL nguy hiểm; sort quay về mặc định.
TC14
Page âm hoặc page quá lớn
Page được chuẩn hóa về giới hạn hợp lệ.
TC15
DB lỗi ở production
Không hiện SQLSTATE; chỉ hiện safe message.
Câu 2 - Problem Solving
Hãy trả lời các câu hỏi Problem Solving sau dựa trên chính ứng dụng em đã xây dựng ở Câu 1. Không trả lời chung chung theo lý thuyết; mỗi câu cần có ví dụ từ project của em.
1. Database design
Vì sao em tách dữ liệu thành các bảng hiện tại? Nếu gom tất cả vào một bảng lớn thì sẽ gặp vấn đề gì khi mở rộng? Hãy chỉ ra primary key, unique key và index trong schema của em.
2. PDO connection
Vì sao cần charset=utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC và EMULATE_PREPARES=false? Nếu thiếu từng cấu hình này, project của em có thể gặp lỗi gì?
3. Prepared statements
Trong project của em, hãy chọn 1 câu INSERT và 1 câu SELECT. Phân tích dữ liệu người dùng được truyền vào SQL như thế nào để tránh SQL Injection.
4. Repository pattern
Vì sao SQL nên nằm trong Repository thay vì viết trực tiếp trong Controller hoặc View? Hãy chỉ rõ Repository nào trong bài của em đang xử lý các query chính.
5. CRUD sạch
Trong module chính của em, luồng Create và Update đi qua các bước nào: request -> validate -> repository -> redirect/view? Nếu bỏ validate hoặc bỏ PRG thì hậu quả gì?
6. Unique constraint và duplicate key
Vì sao chỉ kiểm tra trùng bằng PHP là chưa đủ? Hãy mô tả tình huống 2 request cùng submit một email/mã đơn giống nhau và database unique constraint giúp gì.
7. PRG Pattern
Sau POST thành công, project của em redirect về đâu? Nếu render trực tiếp sau POST, chuyện gì xảy ra khi user bấm F5?
8. Search, pagination và sort an toàn
Trong URL list của em có những tham số nào như q, page, sort, direction? Em đã kiểm soát page âm, page quá lớn, sort/direction không hợp lệ như thế nào?
9. Index và EXPLAIN
Hãy đưa 1 query list/filter/sort trong project của em và kết quả EXPLAIN. Cột key có dùng index không? Nếu key=NULL trên bảng lớn, em sẽ cải tiến index hoặc query như thế nào?
10. Safe error message
Vì sao production không nên hiển thị $e->getMessage() hoặc SQLSTATE cho user? Trong bài của em, lỗi DB được log và hiển thị ra giao diện như thế nào?
11. Delete bằng POST
Vì sao delete không nên dùng GET link? Hãy phân tích rủi ro nếu crawler, preview link hoặc người dùng click nhầm vào URL delete.
12. Hướng phát triển tiếp theo
Nếu phát triển project của em thành hệ thống thật, em sẽ ưu tiên nâng cấp gì trước: authentication/session, CSRF, soft delete, transaction, service layer, logging, role permission, hay API? Giải thích lý do gắn với bài toán của em.
Câu 3 - Bài làm thêm khuyến khích
Các phần sau không bắt buộc, nhưng được cộng điểm nếu làm đúng và có minh chứng rõ ràng:
Tạo seed_data.php sinh tự động 100-300 bản ghi để test pagination và EXPLAIN.
Thêm soft delete bằng deleted_at thay vì DELETE thật.
Thêm filter theo status bên cạnh search q.
Thêm sort direction toggle asc/desc trên cột name/created_at/status.
Thêm transaction cho thao tác tạo order kèm order_items nếu project có bảng chi tiết đơn hàng.
Thêm Service layer để Controller mỏng hơn: Controller -> Service -> Repository.
Thêm logging vào storage/logs/app.log khi DB lỗi.
Thêm middleware login cơ bản để bảo vệ trang quản trị.
Thêm CSRF token cho form POST create/update/delete.
Thiết kế giao diện đẹp hơn nhưng vẫn giữ đúng flow kỹ thuật.
Rubric chấm điểm gợi ý
Hạng mục
Điểm
Tiêu chí
Cấu trúc project, Git, route, 404/405
15 điểm
Có public index, Router, Controller, GitHub, URL chuẩn, 404/405 đúng.
Database schema, seed, unique, index
15 điểm
Schema hợp lý, có users + 2 bảng nghiệp vụ, unique/index rõ, seed đủ dữ liệu.
PDO, Database class, Repository, prepared statements
20 điểm
Kết nối chuẩn, không nối chuỗi input vào SQL, Repository gom query.
CRUD module A và module B
20 điểm
List/Create/Edit/Update/Delete chạy đúng, validate, PRG, flash, giữ dữ liệu khi lỗi.
Search, pagination, safe sort, duplicate handling
15 điểm
Có q/page/sort/direction, whitelist, duplicate key friendly error, page invalid xử lý tốt.
Test cases, EXPLAIN, safe error message
10 điểm
Có bảng test, ảnh minh chứng, EXPLAIN, production error không lộ SQLSTATE.
Problem Solving
5 điểm
Trả lời có lập luận, gắn với project, không chung chung.
Yêu cầu nộp bài
Ghi chú bài thực hành: PHP Lab05
Tiêu đề email hoặc tiêu đề LMS: MSSV_HoTenSinhVien_PHP_Lab05
File báo cáo: MSSV_HoTenSinhVien_PHP_Lab05.pdf
Link GitHub project: đặt trong báo cáo và trong nội dung email/LMS.
Báo cáo phải có ảnh chụp kết quả chạy, bảng test cases, phần trả lời Problem Solving và ghi rõ phần nào đã làm đủ/chưa đủ.
Không cần nộp toàn bộ source code qua email nếu đã có GitHub link public/private theo yêu cầu của giảng viên.
Nếu nộp trễ, ghi rõ lý do nộp trễ.
Deadline và nơi nộp: 22/06/2026 qua email  tuantran261083course@gmail.com 
Checklist cuối trước khi nộp
[ ] Project chạy được bằng php -S localhost:8000 -t public.
[ ] GET /health trả JSON kiểm tra DB.
[ ] Có ít nhất 2 module nghiệp vụ chính, mỗi module có CRUD cơ bản.
[ ] Không có SQL trong View.
[ ] Không ghép trực tiếp $_GET/$_POST vào SQL.
[ ] Sort/direction có whitelist.
[ ] Create/update/delete thành công đều dùng redirect.
[ ] Duplicate unique key hiển thị lỗi dễ hiểu.
[ ] Có ảnh EXPLAIN và nhận xét key/index.
[ ] Có test lỗi DB và safe error message.
[ ] Có câu trả lời Problem Solving gắn với project.
[ ] Có GitHub link và git log/commit rõ ràng.

