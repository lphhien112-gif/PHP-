<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;

/**
 * WorkshopHub — HomeController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * ------------------------------------------------------------------
 * GET / : trang tong quan ung dung.
 */
class HomeController
{
    public function index(): void
    {
        Response::view('home', [
            'title' => 'WorkshopHub — Cong dang ky Workshop',
        ]);
    }

    public function workshopDetails(): void
    {
        $topic = $_GET['topic'] ?? '';

        $details = [
            'Web Development' => [
                'title' => 'Web Development',
                'tagline' => 'Xay dung ung dung web hien dai tu frontend den backend',
                'banner' => '/assets/img/banners/web-development.png',
                'overview' => 'Tham gia workshop Web Development de thuc hanh xay dung ung dung web thuc te end-to-end. Tu thiet ke giao dien Responsive voi HTML5/CSS3, xu ly du lieu phia server voi PHP, den trien khai bao mat va toi uu hieu suat. Ban se hoan thanh mot du an hoan chinh co the dua vao portfolio ca nhan.',
                'level' => 'Co ban — Trung cap',
                'duration' => '3 buoi (6 tieng/buoi)',
                'capacity' => 30,
                'tools' => ['VS Code', 'PHP 8.x', 'MySQL', 'Git', 'Chrome DevTools'],
                'modules' => [
                    ['name' => 'Kien truc Web va HTML5 ngu nghia', 'desc' => 'Tim hieu mo hinh Client-Server, HTTP request/response, va cach viet HTML5 co cau truc tot cho SEO va Accessibility.'],
                    ['name' => 'CSS hien dai va Responsive Design', 'desc' => 'Flexbox, Grid Layout, CSS Custom Properties, Media Queries — xay dung giao dien tuong thich moi thiet bi.'],
                    ['name' => 'PHP Server-side va Form Handling', 'desc' => 'Xu ly form bao mat, Validation, Escape output, PRG pattern — tranh duplicate submission va chong XSS.'],
                    ['name' => 'Co so du lieu va Bao mat Session', 'desc' => 'Ket noi MySQL voi PDO, Prepared Statements chong SQL Injection, quan ly session va dang nhap an toan.'],
                ],
                'prerequisites' => [
                    'Biet su dung may tinh co ban va trinh duyet web',
                    'Da cai dat mot Text Editor (khuyen dung VS Code)',
                    'Khong yeu cau kinh nghiem lap trinh truoc',
                ],
                'project' => [
                    'name' => 'WorkshopHub — He thong dang ky Workshop',
                    'desc' => 'Xay dung mot ung dung web hoan chinh cho phep nguoi dung dang ky tham du workshop, voi form bao mat, chong spam, va khu vuc quan tri cho nhan vien.',
                ],
                'instructor' => [
                    'name' => 'TS. Tran Anh Tuan',
                    'role' => 'Giang vien — Khoa Toan - Tin hoc',
                    'org' => 'Truong DH KHTN, DHQG-HCM',
                ],
            ],
            'Data Science' => [
                'title' => 'Data Science',
                'tagline' => 'Kham pha du lieu, phan tich va xay dung mo hinh du bao',
                'banner' => '/assets/img/banners/data-science.png',
                'overview' => 'Data Science la mot trong nhung ky nang duoc san don nhat hien nay. Workshop nay se dua ban tu con so 0 den viec tu tay thu thap, lam sach, phan tich va truc quan hoa du lieu thuc te. Ban se lam quen voi cac cong cu pho bien va xay dung mo hinh Machine Learning co ban.',
                'level' => 'Co ban — Trung cap',
                'duration' => '3 buoi (6 tieng/buoi)',
                'capacity' => 25,
                'tools' => ['Python 3.x', 'Jupyter Notebook', 'Pandas', 'Matplotlib', 'Scikit-learn'],
                'modules' => [
                    ['name' => 'Python cho phan tich du lieu', 'desc' => 'Cu phap Python co ban, cau truc du lieu, va thu vien NumPy/Pandas de thao tac voi du lieu co cau truc.'],
                    ['name' => 'Lam sach va xu ly du lieu tho', 'desc' => 'Ky thuat xu ly du lieu thieu, outlier, chuyen doi kieu du lieu va merge nhieu nguon du lieu thanh bo du lieu sach.'],
                    ['name' => 'Truc quan hoa du lieu', 'desc' => 'Su dung Matplotlib va Seaborn de tao bieu do bar, line, scatter, heatmap — ke chuyen bang hinh anh tu du lieu.'],
                    ['name' => 'Machine Learning co ban', 'desc' => 'Xay dung mo hinh Linear Regression va Classification voi Scikit-learn, danh gia hieu suat mo hinh.'],
                ],
                'prerequisites' => [
                    'Biet su dung may tinh co ban',
                    'Co kien thuc Toan co ban (thong ke, xac suat)',
                    'Khong bat buoc biet Python truoc — se duoc huong dan tu dau',
                ],
                'project' => [
                    'name' => 'Phan tich bo du lieu — Du doan xu huong',
                    'desc' => 'Thu thap du lieu thuc te, lam sach, phan tich xu huong, va xay dung mo hinh du doan don gian. Trinh bay ket qua bang Dashboard truc quan.',
                ],
                'instructor' => [
                    'name' => 'TS. Tran Anh Tuan',
                    'role' => 'Giang vien — Khoa Toan - Tin hoc',
                    'org' => 'Truong DH KHTN, DHQG-HCM',
                ],
            ],
            'UI/UX Design' => [
                'title' => 'UI/UX Design',
                'tagline' => 'Thiet ke giao dien dep va trai nghiem nguoi dung xuat sac',
                'banner' => '/assets/img/banners/ui-ux-design.png',
                'overview' => 'Mot san pham thanh cong khong chi can code tot ma con can giao dien than thien va trai nghiem muot ma. Workshop nay giup ban nam vung nguyen tac thiet ke lay nguoi dung lam trung tam (UCD), tu nghien cuu nguoi dung, wireframe, den prototype tuong tac va danh gia kha dung.',
                'level' => 'Co ban',
                'duration' => '2 buoi (6 tieng/buoi)',
                'capacity' => 25,
                'tools' => ['Figma', 'FigJam', 'Maze (Usability Testing)', 'Google Fonts'],
                'modules' => [
                    ['name' => 'Nguyen tac thiet ke UI co ban', 'desc' => 'Hieu ve he thong luoi (Grid), phoi mau (Color Theory), Typography, va khoang cach (Spacing) trong thiet ke giao dien so.'],
                    ['name' => 'Nghien cuu nguoi dung va Persona', 'desc' => 'Phuong phap phong van, khao sat nguoi dung, xay dung User Persona va User Journey Map.'],
                    ['name' => 'Wireframe va Prototype', 'desc' => 'Tu y tuong den wireframe lo-fi tren giay, sau do dung Figma de xay dung prototype hi-fi tuong tac duoc.'],
                    ['name' => 'Danh gia kha dung (Usability)', 'desc' => 'Thiet ke va thuc hien phien Usability Testing, phan tich ket qua va lap lai thiet ke.'],
                ],
                'prerequisites' => [
                    'Khong can kinh nghiem thiet ke truoc',
                    'Mang theo laptop co cai san Figma (mien phi)',
                    'Co mat kieu tham my va mong muon hoc thiet ke',
                ],
                'project' => [
                    'name' => 'Thiet ke ung dung di dong — Tu y tuong den Prototype',
                    'desc' => 'Nghien cuu nguoi dung thuc te, xay dung wireframe, thiet ke prototype tuong tac tren Figma, va trinh bay truoc lop.',
                ],
                'instructor' => [
                    'name' => 'TS. Tran Anh Tuan',
                    'role' => 'Giang vien — Khoa Toan - Tin hoc',
                    'org' => 'Truong DH KHTN, DHQG-HCM',
                ],
            ],
            'Mobile App' => [
                'title' => 'Mobile App',
                'tagline' => 'Phat trien ung dung di dong da nen tang cho iOS va Android',
                'banner' => '/assets/img/banners/mobile-app.png',
                'overview' => 'Ung dung di dong la phuong thuc tuong tac pho bien nhat hien nay. Workshop nay trang bi cho ban ky nang xay dung ung dung cross-platform — viet mot lan, chay tren ca iOS va Android. Tu thiet ke UI tren man hinh nho, quan ly state, den tich hop REST API va deploy.',
                'level' => 'Trung cap',
                'duration' => '3 buoi (6 tieng/buoi)',
                'capacity' => 25,
                'tools' => ['Flutter/React Native', 'Android Studio', 'Xcode (macOS)', 'Postman', 'Firebase'],
                'modules' => [
                    ['name' => 'Gioi thieu framework Cross-platform', 'desc' => 'So sanh Flutter vs React Native, cai dat moi truong, va xay dung ung dung Hello World dau tien.'],
                    ['name' => 'Thiet ke UI cho man hinh nho', 'desc' => 'Component-based UI, Navigation, va Responsive Layout tren nhieu kich thuoc man hinh di dong.'],
                    ['name' => 'Quan ly State va API', 'desc' => 'State Management (Provider/Redux), goi REST API, xu ly du lieu JSON, va hien thi danh sach dong.'],
                    ['name' => 'Build, Test va Deploy', 'desc' => 'Chay tren thiet bi that/emulator, tao file APK/IPA, va quy trinh dua ung dung len App Store/Google Play.'],
                ],
                'prerequisites' => [
                    'Co kien thuc lap trinh co ban (bat ky ngon ngu nao)',
                    'Da cai dat Android Studio hoac Xcode',
                    'Biet su dung command line co ban',
                ],
                'project' => [
                    'name' => 'Ung dung quan ly cong viec ca nhan (To-Do App)',
                    'desc' => 'Xay dung ung dung To-Do da nen tang voi CRUD, luu tru local, dong bo API, va giao dien Material Design/Cupertino.',
                ],
                'instructor' => [
                    'name' => 'TS. Tran Anh Tuan',
                    'role' => 'Giang vien — Khoa Toan - Tin hoc',
                    'org' => 'Truong DH KHTN, DHQG-HCM',
                ],
            ],
            'Cyber Security' => [
                'title' => 'Cyber Security',
                'tagline' => 'Phong thu va bao ve he thong truoc cac moi de doa mang',
                'banner' => '/assets/img/banners/cyber-security.png',
                'overview' => 'An toan thong tin la yeu to song con cua moi he thong so. Workshop nay dat ban vao goc nhin cua ke tan cong (offensive) de tu do biet cach phong thu (defensive). Ban se thuc hanh tan cong va va lo hong tren moi truong mo phong an toan, hoc cach bao ve ung dung web khoi Top 10 OWASP.',
                'level' => 'Trung cap — Nang cao',
                'duration' => '3 buoi (6 tieng/buoi)',
                'capacity' => 20,
                'tools' => ['Kali Linux (VM)', 'Burp Suite', 'OWASP ZAP', 'Wireshark', 'DVWA'],
                'modules' => [
                    ['name' => 'Tong quan ve An toan thong tin', 'desc' => 'Mo hinh CIA Triad, cac loai hinh tan cong pho bien, va to chuc OWASP voi Top 10 lo hong bao mat.'],
                    ['name' => 'Tan cong ung dung web', 'desc' => 'Thuc hanh SQL Injection, XSS (Reflected/Stored), CSRF, va Path Traversal tren moi truong DVWA.'],
                    ['name' => 'Phong thu va Hardening', 'desc' => 'Input Validation, Prepared Statements, CSP Headers, HTTPS, va cac ky thuat phong chong hieu qua.'],
                    ['name' => 'Session Security va Ma hoa', 'desc' => 'Password hashing (bcrypt), session hijacking prevention, HTTPS/TLS, va quan ly khoa bao mat.'],
                ],
                'prerequisites' => [
                    'Co kien thuc co ban ve mang may tinh (TCP/IP, HTTP)',
                    'Biet su dung Linux command line co ban',
                    'Da hoc qua mot ngon ngu lap trinh (PHP, Python hoac JS)',
                ],
                'project' => [
                    'name' => 'Bao cao kiem thu bao mat (Pentest Report)',
                    'desc' => 'Thuc hien kiem thu tren ung dung web mo phong, phat hien lo hong, va viet bao cao kem giai phap khac phuc chuyen nghiep.',
                ],
                'instructor' => [
                    'name' => 'TS. Tran Anh Tuan',
                    'role' => 'Giang vien — Khoa Toan - Tin hoc',
                    'org' => 'Truong DH KHTN, DHQG-HCM',
                ],
            ],
        ];

        if (!isset($details[$topic])) {
            redirect('/');
        }

        Response::view('workshop/details', [
            'title' => $details[$topic]['title'] . ' — WorkshopHub',
            'ws' => $details[$topic],
        ]);
    }
}
