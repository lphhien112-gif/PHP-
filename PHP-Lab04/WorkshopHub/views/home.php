<?php
/** Trang chu — WorkshopHub */
?>
<section class="hero">
    <div class="hero-badge">PHP Lab04 &bull; Secure Forms &amp; Session</div>
    <img class="hero-art" src="/assets/img/hero-workshop.png" alt="Workshop hero" loading="eager">
    <h1>Cong dang ky <span class="hl">Workshop</span></h1>
    <p class="hero-sub">
        WorkshopHub la cong dang ky tham du workshop danh cho khach tham quan,
        kem khu vuc dang nhap va bang dieu khien danh rieng cho nhan vien.
        Ung dung minh hoa form bao mat, PRG, chong spam va quan ly phien dang nhap.
    </p>
    <div class="hero-actions">
        <a href="/registrations/create" class="btn btn-primary">Dang ky ngay</a>
        <a href="/registrations" class="btn btn-ghost">Xem danh sach</a>
    </div>
</section>

<section class="features">
    <div class="feature-card">
        <div class="feature-icon"><img src="/assets/img/feat-secure-form.png" alt="Form bao mat" loading="lazy"></div>
        <h3>Form bao mat</h3>
        <p>Validation server-side day du: required, email, phone, do dai va in-list. Escape moi output bang <code>h()</code>.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><img src="/assets/img/feat-prg.png" alt="PRG Pattern" loading="lazy"></div>
        <h3>PRG Pattern</h3>
        <p>POST thanh cong se redirect ve route GET, tranh tao du lieu trung khi refresh trinh duyet.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><img src="/assets/img/feat-antispam.png" alt="Chong spam" loading="lazy"></div>
        <h3>Chong spam</h3>
        <p>Honeypot an + rate limit bang session chan submit qua nhanh (duoi 5 giay).</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><img src="/assets/img/feat-secure-login.png" alt="Dang nhap an toan" loading="lazy"></div>
        <h3>Dang nhap an toan</h3>
        <p>password_hash/verify, session_regenerate_id sau login, idle timeout va logout sach.</p>
    </div>
</section>

<section class="info-panel">
    <h2>Cac chu de workshop</h2>
    <div class="topic-chips">
        <?php foreach (\WorkshopHub\Controllers\RegistrationController::TOPICS as $topic): ?>
            <span class="chip"><?= h($topic) ?></span>
        <?php endforeach; ?>
    </div>
</section>
