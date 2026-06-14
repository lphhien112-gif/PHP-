# UI/UX Image Spec — Flat Vector Illustration Kit cho 3 app PHP

> **Mục tiêu:** Nâng UI/UX của WorkshopHub (Lab04), ClinicDesk (Lab05), EduCRM (Lab06) bằng bộ
> minh hoạ **flat vector** đồng bộ. Tài liệu này dùng để giao cho **antigravity** sinh ảnh, và
> hướng dẫn **fill ảnh vào giao diện** (đường dẫn file + chỉnh view/CSS).
>
> Sinh viên: Le Pham Hong Hien — 22110059. Art direction: **Flat vector (corporate-SaaS)**.

---

## 0. Quy trình đề xuất (đọc trước khi sinh hàng loạt)

1. **Khoá phong cách trước (1 ảnh thử):** Sinh **1 key-art hero** của EduCRM bằng prompt mục 4.E2.
   Duyệt màu/độ chi tiết/nét nhân vật. Khi ưng → dùng đúng *Style Anchor* đó cho mọi ảnh còn lại
   (thêm câu: *"in the exact same flat vector style, line weight, shading and color treatment as the
   reference image"*). Đây là cách giữ **đồng bộ** giữa ~30 ảnh.
2. **Sinh theo từng app** (mỗi app 1 palette). Đặt tên file đúng bảng manifest để wire không phải sửa.
3. **Tối ưu ảnh** (Squoosh/TinyPNG): icon < 30KB, hero < 150KB, ưu tiên PNG-24 có alpha (hoặc WebP).
4. **Bàn giao** → tôi (Claude) sẽ chèn vào view/CSS theo mục "Integration" của từng slot (hoặc bạn tự
   làm theo snippet kèm sẵn).

### Mẹo cho model sinh ảnh của antigravity (Gemini / Imagen / Nano-Banana)
- **Tỷ lệ:** nêu rõ `aspect ratio W:H` trong prompt (vd `aspect ratio 3:2`).
- **Nền trong suốt:** đa số model **không** xuất alpha thật. 2 lựa chọn:
  - *(khuyên dùng)* sinh **thẳng trên nền đúng màu slot** — app sáng dùng nền `#FFFFFF`,
    ClinicDesk (tối) dùng nền `#0F172A` — để thả vào là khớp, khỏi tách nền.
  - hoặc sinh trên nền trắng phẳng rồi **remove background** (remove.bg / Photoshop) để ra PNG alpha.
- **Tránh chữ:** luôn thêm negative *"no text, no lettering, no watermark"* (chữ do model sinh
  thường sai chính tả tiếng Việt).
- **Độ phân giải:** sinh ở **@2x** kích thước hiển thị (vd hiển thị 480px → sinh 960px) cho màn retina.

---

## 1. Style Anchor (DÁN VÀO MỌI PROMPT)

> **STYLE:** Modern **flat vector illustration**, clean corporate-SaaS aesthetic (think Stripe / Notion /
> a more detailed unDraw). Smooth geometric shapes, soft 2-stop gradients, subtle long shadows, generously
> rounded corners, thin 1–2px accent lines only. Friendly, professional, **diverse rounded characters**
> when people appear. Crisp vector edges, high detail but uncluttered, balanced negative space, clear
> focal point, centered composition.
>
> **NEGATIVE / AVOID:** no text, no lettering, no numbers, no watermark, no logo, photorealism, 3D render,
> isometric grid, clay/plasticine, heavy black outlines, gritty texture, harsh drop shadows, busy
> background, neon overload, ui screenshot, brand logos.

---

## 2. Palette theo từng app (đưa hex vào prompt)

| App | Nền chủ đạo | Primary | Phụ / Accent | Sắc trạng thái | "Đặt ảnh trên nền" |
|---|---|---|---|---|---|
| **WorkshopHub** (Lab04) | sáng `#F8FAFC→#EEF2FF` | indigo `#6366F1` / `#4F46E5` | teal `#14B8A6` | green `#16A34A`, amber `#D97706`, red `#DC2626` | `#FFFFFF` |
| **ClinicDesk** (Lab05) | **TỐI** `#0F172A` | sky `#38BDF8` / `#0EA5E9` | violet `#A78BFA` | green `#22C55E`, amber `#F59E0B`, red `#EF4444` | `#0F172A` |
| **EduCRM** (Lab06) | sáng `#F1F5F9`, panel trắng | indigo `#4F46E5` | purple `#7C3AED` (gradient) | green `#16A34A`, info `#0284C7`, amber `#D97706` | `#FFFFFF` |

**Lưu ý ClinicDesk (nền tối):** thêm vào mọi prompt:
> *"Designed to sit on a dark navy #0F172A background: luminous sky-blue and violet fills, white/light
> highlights, soft outer glow; avoid dark fills that vanish on dark bg; image background is solid dark
> navy #0F172A."*

---

## 3. Quy ước kỹ thuật & đặt tên (chung 3 app)

- **Thư mục đích:** `<App>/public/assets/img/`
  (Lab04 `WorkshopHub/public/assets/img/`, Lab05 `ClinicDesk/public/assets/img/`, Lab06 `EduCRM/public/assets/img/`).
- **Định dạng:** logo/icon → ưu tiên **SVG**; nếu antigravity chỉ ra raster → **PNG-24 alpha @2x**
  (hoặc WebP). Ảnh hero/cover → PNG/WebP @2x.
- **Đặt tên kebab-case** đúng cột "File" trong manifest → khỏi sửa code khi thả ảnh.
- **Retina:** sinh @2x, hiển thị bằng CSS `width`/`max-width` (vd ảnh 960px hiển thị 480px).
- **Cover fit:** ảnh nền/thumbnail đặt trong khung tỉ lệ cố định + `object-fit: cover`.
- **Lazy load:** ảnh dưới màn hình đầu thêm `loading="lazy"`.
- **Favicon:** thêm vào `<head>` của layout:
  `<link rel="icon" type="image/png" href="/assets/img/favicon-32.png">`.

---

## 4. WORKSHOPHUB — Lab04 (nền sáng, indigo + teal, chủ đề: workshop/seminar)

**Manifest**

| # | Slot | File | Kích thước @2x | Nền | Wire vào |
|---|---|---|---|---|---|
| W1 | Logo mark | `logo-mark.svg` + `favicon-32.png` | 128² / 64² | trong suốt | `views/layout.php` `.navbar-brand`, `<head>` |
| W2 | Hero illustration | `hero-workshop.png` | 1200×800 (3:2) | `#FFFFFF` | `views/home.php` `.hero` |
| W3 | Hero background mesh | `bg-hero-mesh.png` | 1920×720 | sáng, mờ | CSS `.hero` background |
| W4 | Icon "Form bảo mật" | `feat-secure-form.png` | 256² | trong suốt | `.feature-card:nth(1) .feature-icon` |
| W5 | Icon "PRG Pattern" | `feat-prg.png` | 256² | trong suốt | feature card 2 |
| W6 | Icon "Chống spam" | `feat-antispam.png` | 256² | trong suốt | feature card 3 |
| W7 | Icon "Đăng nhập an toàn" | `feat-secure-login.png` | 256² | trong suốt | feature card 4 |
| W8 | Empty state | `empty-registrations.png` | 640×480 | trong suốt | `registrations/index.php` `.empty-state` |
| W9 | Login illustration | `illus-login.png` | 900×680 | `#FFFFFF` | `auth/login.php` (cột/đầu `.auth-card`) |
| W10 | Error 404/405 | `illus-error.png` | 720×600 | trong suốt | `errors/404.php`, `405.php` `.error-page` |

**Prompts**

```text
[W2 hero-workshop] {STYLE ANCHOR}. Subject: a lively professional workshop / seminar scene — a friendly
presenter at a screen pointing to a lightbulb idea, 3 diverse seated attendees with open laptops taking
notes, floating UI cards, calendar and checkmark motifs around them. Palette: indigo #6366F1 and #4F46E5
as primary, teal #14B8A6 accents, light slate background. Warm, welcoming, organized. Composition leaves
clean space on the left for a headline. Solid white #FFFFFF background. Aspect ratio 3:2.
```
```text
[W3 bg-hero-mesh] {STYLE ANCHOR, but abstract — no characters}. Subject: a very soft abstract background
of large blurred gradient blobs and a few thin geometric line-arcs and dots. Palette: pale indigo #EEF2FF,
soft indigo #6366F1 at low opacity, hints of teal #14B8A6. Extremely subtle, light, airy — meant to sit
BEHIND text at ~15% strength. No focal subject. Wide banner. Aspect ratio 8:3.
```
```text
[W4 feat-secure-form] {STYLE ANCHOR}. A single centered flat-vector icon-illustration: a document/form with
a shield and a small green check, signifying server-side form validation & safety. Indigo #6366F1 + teal
#14B8A6, soft gradient, rounded. Isolated, lots of padding, transparent background. Square. Aspect ratio 1:1.
```
```text
[W5 feat-prg] {STYLE ANCHOR}. Single centered icon-illustration: two looping arrows (POST → redirect → GET)
around a small browser card, representing the Post/Redirect/Get pattern preventing duplicate submit. Indigo
+ teal. Transparent background. Square 1:1.
```
```text
[W6 feat-antispam] {STYLE ANCHOR}. Single centered icon-illustration: a small friendly robot/bug being
blocked by a shield with a "no entry" motif and a hidden honeypot trap, representing anti-spam (honeypot +
rate limit). Indigo + teal, one amber #D97706 warning accent. Transparent background. Square 1:1.
```
```text
[W7 feat-secure-login] {STYLE ANCHOR}. Single centered icon-illustration: a padlock with a session key and a
small refresh swirl (session regenerate), signifying secure login. Indigo + teal. Transparent. Square 1:1.
```
```text
[W8 empty-registrations] {STYLE ANCHOR}. A gentle empty-state illustration: an open empty clipboard / inbox
with a few floating dots and a small plus-circle invitation, friendly "nothing here yet" mood. Indigo + teal
on transparent background, soft. Aspect ratio 4:3.
```
```text
[W9 illus-login] {STYLE ANCHOR}. A staff member unlocking a secure dashboard door / entering credentials on
a large login card, calm and trustworthy. Indigo #6366F1 + teal #14B8A6. Solid white #FFFFFF background,
subject centered with breathing room. Aspect ratio 4:3.
```
```text
[W10 illus-error] {STYLE ANCHOR}. A friendly "lost / page not found" illustration: a small character with a
map/compass next to a disconnected signpost, light and non-alarming. Indigo + teal, transparent background.
Aspect ratio 6:5.
```

**Integration (ví dụ wire)** — `views/home.php` `.hero`:
```html
<section class="hero">
  <span class="hero-badge">PHP Lab04 • Secure Forms & Session</span>
  <img class="hero-art" src="/assets/img/hero-workshop.png" alt="" loading="eager">
  <h1>Cong dang ky <span class="hl">Workshop</span></h1>
  ...
</section>
```
CSS thêm vào `style.css`:
```css
.hero { position: relative; background: url('/assets/img/bg-hero-mesh.png') center/cover no-repeat; }
.hero-art { width: min(520px, 80%); height: auto; margin: 8px auto 4px; display:block; }
.feature-icon img { width: 56px; height: 56px; }          /* thay emoji bằng <img> */
.empty-state img { width: 220px; margin: 0 auto 12px; display:block; }
.error-page img  { width: 280px; margin: 0 auto 18px; display:block; }
```

---

## 5. CLINICDESK — Lab05 (nền TỐI, sky-blue + violet, chủ đề: phòng khám / lịch hẹn)

> ⚠️ Mọi prompt thêm câu nền-tối ở mục 2. Nền ảnh = `#0F172A`.

**Manifest**

| # | Slot | File | Kích thước @2x | Nền | Wire vào |
|---|---|---|---|---|---|
| C1 | Logo mark | `logo-mark.svg` + `favicon-32.png` | 128² / 64² | trong suốt | `layout.php` `.brand`, `<head>` |
| C2 | Dashboard hero | `hero-clinic.png` | 1200×750 (8:5) | `#0F172A` | `home.php` (đầu trang) |
| C3 | Hero glow mesh | `bg-glow.png` | 1920×720 | tối | CSS nền hero |
| C4 | Module "Bệnh nhân" | `mod-patients.png` | 256² | trong suốt | `home.php` `.module-card` |
| C5 | Module "Lịch hẹn" | `mod-appointments.png` | 256² | trong suốt | `.module-card` |
| C6 | Icon "Health/DB" | `mod-health.png` | 256² | trong suốt | dashboard / nav |
| C7 | Empty state | `empty-data.png` | 640×480 | trong suốt | `patients/index`, `appointments/index` `.empty` |
| C8 | Avatar set | `avatar-male.png` `avatar-female.png` `avatar-other.png` | 192² | trong suốt | bảng `patients` (cột tên) |
| C9 | Error 404/405/500 | `illus-error.png` | 720×600 | trong suốt | `errors/*` `.error-page` |

**Prompts**

```text
[C2 hero-clinic] {STYLE ANCHOR} {DARK-BG CLAUSE}. Subject: a calm modern clinic front-desk scene — a
friendly receptionist/doctor at a counter, a large appointment calendar with a glowing checkmark, a patient
card and a heartbeat pulse line floating nearby. Luminous sky-blue #38BDF8 and violet #A78BFA on dark navy
#0F172A, white highlights, soft glow. Organized, trustworthy, healthcare. Clean space on one side for a
headline. Aspect ratio 8:5.
```
```text
[C3 bg-glow] Abstract dark background: large soft radial glows in sky-blue #38BDF8 and violet #A78BFA over
solid dark navy #0F172A, a few faint thin line-arcs and dots, subtle vignette. No subject, very low
intensity (sits behind content). {STYLE ANCHOR minus characters}. Aspect ratio 8:3.
```
```text
[C4 mod-patients] {STYLE ANCHOR} {DARK-BG CLAUSE}. Single centered icon-illustration: a patient ID card with
a person silhouette and a small medical cross, representing patient records. Glowing sky-blue + violet on
transparent background. Square 1:1.
```
```text
[C5 mod-appointments] {STYLE ANCHOR} {DARK-BG CLAUSE}. Single centered icon-illustration: a calendar with a
clock and a checkmark, representing appointments with unique codes. Glowing sky-blue + violet, transparent
background. Square 1:1.
```
```text
[C6 mod-health] {STYLE ANCHOR} {DARK-BG CLAUSE}. Single centered icon-illustration: a database cylinder with
a heartbeat pulse line and a green #22C55E "ok" dot, representing DB health-check. Sky-blue + green glow,
transparent background. Square 1:1.
```
```text
[C7 empty-data] {STYLE ANCHOR} {DARK-BG CLAUSE}. Gentle empty-state: an empty medical clipboard / open
folder with floating dots, "no records yet" mood. Sky-blue + violet glow on transparent background.
Aspect ratio 4:3.
```
```text
[C8 avatars] {STYLE ANCHOR} {DARK-BG CLAUSE}. A clean circular flat-vector avatar of a [male / female /
neutral] person, head-and-shoulders, friendly, simple, consistent style across the set. Sky-blue/violet
accents, suitable on dark bg, transparent background. Square 1:1. (Sinh 3 ảnh, đổi giới tính.)
```
```text
[C9 illus-error] {STYLE ANCHOR} {DARK-BG CLAUSE}. Friendly "page not found" in a clinic context: a small
character with an empty chart looking at a disconnected sign. Sky-blue + violet glow, transparent background.
Aspect ratio 6:5.
```

**Integration** — `app/Views/home.php` đầu trang + CSS `style.css`:
```css
.clinic-hero { position:relative; background:url('/assets/img/bg-glow.png') center/cover; border:1px solid var(--border); border-radius:16px; padding:28px; }
.clinic-hero img { width:min(460px,70%); height:auto; }
.module-card .mod-art { width:54px; height:54px; margin-bottom:8px; }
.empty img { width:200px; margin:0 auto 12px; display:block; }
td .avatar { width:30px; height:30px; border-radius:50%; vertical-align:middle; margin-right:8px; }
.error-page img { width:280px; margin:0 auto 18px; display:block; }
```
Bảng patients (thay/đứng cạnh tên) dùng `avatar-<gender>.png` theo cột `gender`.

---

## 6. EDUCRM — Lab06 Final (nền sáng, indigo→purple gradient, chủ đề: CRM trung tâm đào tạo)

**Manifest**

| # | Slot | File | Kích thước @2x | Nền | Wire vào |
|---|---|---|---|---|---|
| E1 | Logo mark | `logo-mark.svg` + `favicon-32.png` | 128² / 64² | trong suốt | `layouts/main.php`,`auth.php`,`public.php` `.brand`, `<head>` |
| E2 | Dashboard welcome | `hero-dashboard.png` | 1200×700 (12:7) | `#FFFFFF` | `dashboard/index.php` (card đầu) |
| E3 | Login illustration | `illus-login.png` | 900×720 | `#FFFFFF` | `auth/login.php` `.auth-card` |
| E4 | Public lead hero | `hero-public-lead.png` | 1200×675 (16:9) | `#FFFFFF` | `public-leads/create.php` `.public-hero` |
| E5 | Gradient overlay pattern | `bg-topbar-pattern.png` | 1920×480 | trong suốt/trắng mờ | CSS `.topbar`, `.auth-wrap` |
| E6 | Stat "Tổng lead" | `stat-leads.png` | 192² | trong suốt | `.stat` (dashboard) |
| E7 | Stat "Đơn/Phiếu" | `stat-orders.png` | 192² | trong suốt | `.stat` |
| E8 | Stat "Doanh thu" | `stat-revenue.png` | 192² | trong suốt | `.stat` |
| E9 | Stat "Hệ thống/Lead mới" | `stat-system.png` | 192² | trong suốt | `.stat` |
| E10 | Empty state | `empty-data.png` | 640×480 | trong suốt | `leads/index`,`orders/index` `.empty` |
| E11 | Error 404/405/500 | `illus-error.png` | 720×600 | trong suốt | `errors/*` `.error-page` |
| E12 | Lead avatar set | `avatar-1..4.png` | 192² | trong suốt | bảng `leads`/`orders` |

**Prompts**

```text
[E2 hero-dashboard] {STYLE ANCHOR}. Subject: a training-center CRM dashboard hero — a friendly advisor at a
desk reviewing a funnel of student leads converting into enrolled students, with floating analytics cards
(bar chart, growth arrow, coins) and a graduation cap motif. Palette: indigo #4F46E5 primary into purple
#7C3AED, light slate background, green #16A34A success accents. Optimistic, organized, data-driven. Clean
space on the left for a welcome headline. Solid white #FFFFFF background. Aspect ratio 12:7.
```
```text
[E3 illus-login] {STYLE ANCHOR}. A staff member securely signing into a CRM — a large login card with a
padlock, session key and a subtle refresh swirl (session regenerate), trustworthy mood. Indigo #4F46E5 →
purple #7C3AED. Solid white #FFFFFF background, centered subject. Aspect ratio 5:4.
```
```text
[E4 hero-public-lead] {STYLE ANCHOR}. A warm "register your interest / free consultation" scene for a
training center — a smiling advisor welcoming a prospective student who fills a form on a phone/card, books
and a graduation cap around them, a small shield (secure form) accent. Indigo → purple, green check accent.
Solid white #FFFFFF background, balanced. Aspect ratio 16:9.
```
```text
[E5 bg-topbar-pattern] Abstract seamless overlay pattern of faint white dots, thin wavy lines and soft
circles, meant to sit ON TOP of an indigo→purple gradient at ~12% opacity. White/very-light only,
transparent background, no subject. {STYLE ANCHOR minus characters}. Aspect ratio 4:1.
```
```text
[E6 stat-leads] {STYLE ANCHOR}. Single centered icon-illustration: a magnet/funnel attracting person-dots
(lead capture). Indigo #4F46E5 + purple #7C3AED, info-blue #0284C7 accent. Transparent background. Square 1:1.
```
```text
[E7 stat-orders] {STYLE ANCHOR}. Single centered icon-illustration: a receipt/invoice with a checkmark and a
credit card (tuition order/payment). Indigo + purple, green #16A34A accent. Transparent. Square 1:1.
```
```text
[E8 stat-revenue] {STYLE ANCHOR}. Single centered icon-illustration: a rising bar chart with coins and an
upward arrow (revenue). Indigo + purple, green #16A34A. Transparent. Square 1:1.
```
```text
[E9 stat-system] {STYLE ANCHOR}. Single centered icon-illustration: a server/database with a green "ok"
pulse and a small bell (new leads / system status). Indigo + purple, green accent. Transparent. Square 1:1.
```
```text
[E10 empty-data] {STYLE ANCHOR}. Gentle empty-state: an empty CRM table/folder with floating plus-dots,
"no records yet, add one" mood. Indigo → purple on transparent background. Aspect ratio 4:3.
```
```text
[E11 illus-error] {STYLE ANCHOR}. Friendly "page not found" CRM context: a small character with a
disconnected dashboard tile / broken link, calm and non-alarming. Indigo → purple, transparent background.
Aspect ratio 6:5.
```
```text
[E12 avatars] {STYLE ANCHOR}. A clean circular flat-vector avatar, head-and-shoulders, friendly diverse
person, consistent set of 4 (vary gender/skin tone/hair). Indigo/purple accents, transparent background.
Square 1:1. (Sinh 4 ảnh.)
```

**Integration** — `auth/login.php` (`.auth-card`) + `dashboard/index.php` + CSS `app.css`:
```css
.topbar { background:linear-gradient(120deg,#4f46e5,#7c3aed),url('/assets/img/bg-topbar-pattern.png'); background-blend-mode:overlay; background-size:cover; }
.auth-card .illus { width:160px; display:block; margin:0 auto 14px; }
.dash-hero { display:flex; align-items:center; gap:20px; }
.dash-hero img { width:min(360px,42%); height:auto; }
.stat .stat-art { width:40px; height:40px; float:right; opacity:.9; }
.public-hero img { width:min(420px,80%); height:auto; margin:0 auto 10px; display:block; }
.empty img { width:200px; margin:0 auto 12px; display:block; }
.error-page img { width:280px; margin:0 auto 18px; display:block; }
td .avatar { width:30px; height:30px; border-radius:50%; vertical-align:middle; margin-right:8px; }
```

---

## 7. Bảng tổng & thứ tự ưu tiên sinh ảnh

**Tối thiểu để "ấn tượng ngay" (sinh trước, 12 ảnh):**
E2 hero-dashboard · E3 illus-login · E4 hero-public-lead · W2 hero-workshop · C2 hero-clinic ·
4× feature icon W4–W7 · 4× stat icon E6–E9.

**Đầy đủ:** toàn bộ manifest 3 app (~32 file gồm biến thể avatar).

**Tổng số prompt cần chạy:** Lab04 = 9, Lab05 = 9 (avatar ×3), Lab06 = 11 (avatar ×4) → ~29 lần sinh
(một số ra nhiều biến thể, chọn cái đẹp nhất).

---

## 8. Sau khi có ảnh

Đặt ảnh đúng tên vào `<App>/public/assets/img/`. Báo tôi — tôi sẽ:
1. Wire ảnh vào view/CSS theo mục Integration (thay emoji `.feature-icon`/`.module-card` bằng `<img>`,
   chèn hero/login/empty/error/avatar, thêm favicon).
2. Test lại bằng `php -S` + chụp lại screenshot thật (pipeline puppeteer đã dựng) để **cập nhật ảnh
   minh hoạ trong báo cáo PDF** → báo cáo cũng đẹp lên theo.
3. (Tuỳ chọn) thêm `loading="lazy"`, nén ảnh, và bổ sung `alt` mô tả.
```
