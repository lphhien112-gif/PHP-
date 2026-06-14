# TASKS — Sửa ảnh lỗi (Task 1) + Giai đoạn 2 (Task 2)

> Sinh viên: Le Pham Hong Hien — 22110059. Art direction: **Flat vector (corporate-SaaS)**.
> Quy trình: **bạn sinh ảnh bằng antigravity** (dán prompt bên dưới) → đặt đúng tên file vào
> `<App>/public/assets/img/` → báo Claude wire vào view/CSS theo mục **Integration** kèm sẵn.
>
> Nhắc lại bối cảnh quan trọng: phần ảnh là **điểm cộng/polish**, KHÔNG nằm trong rubric bắt buộc
> (rubric chấm chức năng: secure form, PRG, session, MVC, PDO CRUD). Làm đẹp thoải mái, chỉ cần
> **không phá chức năng** (giữ `alt`, `loading="lazy"`, không để ảnh nền đè form).

## STYLE ANCHOR (dán vào MỌI prompt)
> Modern **flat vector illustration**, clean corporate-SaaS aesthetic (Stripe / Notion / detailed unDraw).
> Smooth geometric shapes, soft 2-stop gradients, subtle long shadows, generously rounded corners, thin
> 1–2px accent lines only. Friendly, professional, diverse rounded characters when people appear. Crisp
> vector edges, high detail but uncluttered, balanced negative space, clear focal point, centered.
> **NEGATIVE / AVOID:** no text, no lettering, no numbers, no watermark, no logo, photorealism, 3D render,
> isometric grid, clay, heavy black outlines, gritty texture, harsh drop shadows, busy background, neon
> overload, ui screenshot, brand logos.

Palette nhanh: **Lab04** indigo `#6366F1`/`#4F46E5` + teal `#14B8A6`, nền `#FFFFFF`.
**Lab05** sky `#38BDF8` + violet `#A78BFA` glow, nền TỐI `#0F172A`.
**Lab06** indigo `#4F46E5` → purple `#7C3AED`, nền `#FFFFFF`.

---

# TASK 1 — Sinh lại 2 ảnh lỗi

## [ ] 1.1 — `hero-dashboard.png` (Lab06) — bỏ chữ AI
**Lý do:** ảnh hiện tại dính chữ méo ("Student Leads / Applications / Enrolled Students / $"), vi phạm rule *no text*.
**Đích:** `PHP-Lab06/EduCRM/public/assets/img/hero-dashboard.png` (ghi đè) — 1200×700 (12:7), nền `#FFFFFF`.

```text
{STYLE ANCHOR}. Subject: a training-center CRM dashboard hero — a friendly advisor at a desk reviewing a
funnel of student leads converting into enrolled students, with floating analytics cards (bar chart, growth
arrow, coins) and a graduation cap motif. Use ONLY simple abstract shapes for any chart/card — NO words, NO
letters, NO numbers anywhere in the image, leave all labels blank. Palette: indigo #4F46E5 into purple
#7C3AED, light slate background, green #16A34A success accents. Optimistic, organized, data-driven. Clean
empty space on the LEFT for a welcome headline. Solid white #FFFFFF background. Aspect ratio 12:7.
```
> Negative bổ sung khi sinh: *"no text, no labels, no letters, no numbers, blank charts"*.
> Integration: **không cần sửa code** — chỉ ghi đè đúng tên file.

## [ ] 1.2 — `stat-revenue.png` (Lab06) — rõ "cột tăng + xu"
**Lý do:** ảnh hiện tại trông như lọ thuốc/hũ, không đọc ra "doanh thu".
**Đích:** `PHP-Lab06/EduCRM/public/assets/img/stat-revenue.png` (ghi đè) — 192² (1:1), nền trong suốt.

```text
{STYLE ANCHOR}. Single centered icon-illustration: three flat solid ascending BAR-CHART columns (clearly
flat rectangles, increasing in height left-to-right) with a bold upward arrow rising over them and a small
neat stack of round gold coins in front. Unmistakably a revenue/growth chart, NOT bottles or jars. Indigo
#4F46E5 + purple #7C3AED bars, green #16A34A arrow, gold coins. Transparent background. Lots of padding.
Square 1:1.
```
> Integration: **không cần sửa code** — ghi đè đúng tên file.

---

# TASK 2 — Giai đoạn 2 (mesh/glow + empty + error + logo)

Sinh xong đặt vào thư mục img tương ứng. Mục **Integration** là việc Claude làm sau khi có ảnh.

## A. Background mesh / glow (tăng "độ xịn" nhiều nhất)

### [ ] 2.A1 — `bg-hero-mesh.png` (Lab04) — 1920×720, nền sáng mờ
```text
{STYLE ANCHOR, abstract — NO characters}. A very soft abstract background of large blurred gradient blobs
and a few thin geometric line-arcs and dots. Palette: pale indigo #EEF2FF, soft indigo #6366F1 at low
opacity, hints of teal #14B8A6. Extremely subtle, light, airy — meant to sit BEHIND text at ~15% strength.
No focal subject. Wide banner. Aspect ratio 8:3.
```
- Đích: `PHP-Lab04/WorkshopHub/public/assets/img/bg-hero-mesh.png`
- Integration: `style.css` → `.hero { background: url('/assets/img/bg-hero-mesh.png') center/cover no-repeat; }`

### [ ] 2.A2 — `bg-glow.png` (Lab05) — 1920×720, nền TỐI
```text
{STYLE ANCHOR, NO characters}. Abstract dark background: large soft radial glows in sky-blue #38BDF8 and
violet #A78BFA over solid dark navy #0F172A, a few faint thin line-arcs and dots, subtle vignette. No
subject, very low intensity (sits behind content). Aspect ratio 8:3.
```
- Đích: `PHP-Lab05/ClinicDesk/public/assets/img/bg-glow.png`
- Integration: `style.css` → `.clinic-hero { background:url('/assets/img/bg-glow.png') center/cover; }`

### [ ] 2.A3 — `bg-topbar-pattern.png` (Lab06) — 1920×480, trắng mờ trong suốt
```text
{STYLE ANCHOR, NO characters}. Abstract seamless overlay pattern of faint white dots, thin wavy lines and
soft circles, meant to sit ON TOP of an indigo→purple gradient at ~12% opacity. White / very-light only,
transparent background, no subject. Aspect ratio 4:1.
```
- Đích: `PHP-Lab06/EduCRM/public/assets/img/bg-topbar-pattern.png`
- Integration: `app.css` → `.topbar { background:linear-gradient(120deg,#4f46e5,#7c3aed),url('/assets/img/bg-topbar-pattern.png'); background-blend-mode:overlay; background-size:cover; }`

## B. Empty-state illustration

### [ ] 2.B1 — `empty-registrations.png` (Lab04) — 640×480 (4:3), trong suốt
```text
{STYLE ANCHOR}. A gentle empty-state illustration: an open empty clipboard / inbox with a few floating dots
and a small plus-circle invitation, friendly "nothing here yet" mood. Indigo #6366F1 + teal #14B8A6 on
transparent background, soft. NO text. Aspect ratio 4:3.
```
- Đích: `PHP-Lab04/.../img/empty-registrations.png`
- Integration: `views/registrations/index.php` `.empty-state` (đã có `<div class="empty-state">` ở dòng 13) → chèn `<img>` đầu div + CSS `.empty-state img{width:220px;margin:0 auto 12px;display:block;}`

### [ ] 2.B2 — `empty-data.png` (Lab05) — 640×480, trong suốt, hợp nền tối
```text
{STYLE ANCHOR}. Gentle empty-state: an empty medical clipboard / open folder with floating dots, "no
records yet" mood. Luminous sky-blue #38BDF8 + violet #A78BFA glow, designed to sit on dark navy #0F172A,
transparent background. NO text. Aspect ratio 4:3.
```
- Đích: `PHP-Lab05/.../img/empty-data.png`
- Integration: **cần đổi markup** — empty hiện là `<td class="empty">text</td>` (text-only) ở `patients/index.php:58` và `appointments/index.php`. Claude sẽ bọc ảnh + dòng chữ vào ô đó.

### [ ] 2.B3 — `empty-data.png` (Lab06) — 640×480, trong suốt
```text
{STYLE ANCHOR}. Gentle empty-state: an empty CRM table/folder with floating plus-dots, "no records yet, add
one" mood. Indigo #4F46E5 → purple #7C3AED on transparent background. NO text. Aspect ratio 4:3.
```
- Đích: `PHP-Lab06/.../img/empty-data.png`
- Integration: `.empty` hiện là `<div class="empty">text</div>` (`leads/index.php:34`, `orders/index.php`). Claude chèn `<img>` + CSS `.empty img{width:200px;margin:0 auto 12px;display:block;}`

## C. Error illustration (404/405/500)

### [ ] 2.C1 — `illus-error.png` (Lab04) — 720×600 (6:5), trong suốt
```text
{STYLE ANCHOR}. A friendly "lost / page not found" illustration: a small character with a map/compass next
to a disconnected signpost, light and non-alarming. Indigo #6366F1 + teal #14B8A6, transparent background.
NO text. Aspect ratio 6:5.
```
- Đích: `PHP-Lab04/.../img/illus-error.png`
- Integration: `views/errors/404.php` + `405.php` `.error-page` (chèn `<img>` trên `.error-code`) + CSS `.error-page img{width:280px;margin:0 auto 18px;display:block;}`

### [ ] 2.C2 — `illus-error.png` (Lab05) — 720×600, trong suốt, nền tối
```text
{STYLE ANCHOR}. Friendly "page not found" in a clinic context: a small character with an empty chart looking
at a disconnected sign. Luminous sky-blue #38BDF8 + violet #A78BFA glow on dark navy #0F172A, transparent
background. NO text. Aspect ratio 6:5.
```
- Đích: `PHP-Lab05/.../img/illus-error.png`
- Integration: `app/Views/errors/404.php`,`405.php`,`500.php`

### [ ] 2.C3 — `illus-error.png` (Lab06) — 720×600, trong suốt
```text
{STYLE ANCHOR}. Friendly "page not found" CRM context: a small character with a disconnected dashboard tile
/ broken link, calm and non-alarming. Indigo #4F46E5 → purple #7C3AED, transparent background. NO text.
Aspect ratio 6:5.
```
- Đích: `PHP-Lab06/.../img/illus-error.png`
- Integration: `app/Views/layouts/error.php` (`.auth-card` dòng 13) — chèn 1 lần ở layout là cả 404/405/500 đều có.

## D. Logo mark + favicon (thay emoji ở brand)

> Hiện brand đang dùng emoji: Lab04 `🎓 WorkshopHub`, Lab05 `🩺 ClinicDesk`. Thay bằng logo SVG/PNG cho pro.

### [ ] 2.D1 — Lab04 `logo-mark.svg` (128²) + `favicon-32.png` (64²)
```text
{STYLE ANCHOR}. A minimal flat-vector app logo MARK (no text): a simple rounded shape combining a workshop
podium / presentation screen with a small check, indigo #6366F1 → #4F46E5 with a teal #14B8A6 accent.
Centered, transparent background, lots of padding. Square 1:1. Simple enough to read at 32px.
```
- Đích: `PHP-Lab04/.../img/logo-mark.svg` + `favicon-32.png`
- Integration: `views/layout.php:25` thay emoji bằng `<img class="brand-logo">`; thêm `<link rel="icon">` vào `<head>`.

### [ ] 2.D2 — Lab05 `logo-mark.svg` + `favicon-32.png`
```text
{STYLE ANCHOR}. A minimal flat-vector app logo MARK (no text): a rounded medical cross fused with a
calendar/clock dot, luminous sky-blue #38BDF8 + violet #A78BFA, glowing, suits dark bg, transparent
background. Square 1:1. Readable at 32px.
```
- Đích: `PHP-Lab05/.../img/` — Integration: `app/Views/layout.php:20` + `<head>`.

### [ ] 2.D3 — Lab06 `logo-mark.svg` + `favicon-32.png`
```text
{STYLE ANCHOR}. A minimal flat-vector app logo MARK (no text): a rounded funnel / graduation-cap combined
with a small growth arrow, indigo #4F46E5 → purple #7C3AED. Centered, transparent background. Square 1:1.
Readable at 32px.
```
- Đích: `PHP-Lab06/.../img/` — Integration: `app/Views/layouts/main.php`,`auth.php`,`public.php` `.brand` + `<head>`.

---

# Checklist tổng

**Task 1 (sinh lại — ưu tiên cao):**
- [ ] 1.1 hero-dashboard.png (Lab06, bỏ chữ)
- [ ] 1.2 stat-revenue.png (Lab06, rõ cột+xu)

**Task 2 (Giai đoạn 2):**
- [ ] 2.A1 bg-hero-mesh (L04)  · [ ] 2.A2 bg-glow (L05)  · [ ] 2.A3 bg-topbar-pattern (L06)
- [ ] 2.B1 empty L04  · [ ] 2.B2 empty L05  · [ ] 2.B3 empty L06
- [ ] 2.C1 error L04  · [ ] 2.C2 error L05  · [ ] 2.C3 error L06
- [ ] 2.D1 logo+favicon L04  · [ ] 2.D2 L05  · [ ] 2.D3 L06

**Sau khi có ảnh → Claude làm:**
- [ ] Wire toàn bộ ảnh vào view/CSS theo mục Integration.
- [ ] Chụp lại screenshot thật (`php -S localhost:8000 -t public`) → cập nhật ảnh trong PDF report (report hiện đang dùng screenshot CŨ chưa có illustration).
- [ ] Kiểm tra không vỡ layout / không che form.

> Tổng cần sinh: **2 ảnh sửa + 12 slot mới** (3 bg + 3 empty + 3 error + 3 logo, mỗi logo kèm 1 favicon).
> Ưu tiên: Task 1 → 2.A (mesh/glow, đẹp nhanh) → 2.C (error) → 2.B (empty) → 2.D (logo).
