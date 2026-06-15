<?php
/**
 * Trang chi tiet Workshop — WorkshopHub
 * Bien nhan vao: $ws (mang chua toan bo chi tiet workshop)
 */
?>
<div class="ws-detail-page">

    <!-- ===== HERO BANNER ===== -->
    <div class="ws-detail-hero">
        <img src="<?= h($ws['banner']) ?>" alt="<?= h($ws['title']) ?> Banner" class="ws-hero-bg">
        <div class="ws-hero-overlay"></div>
        <div class="ws-hero-content">
            <a href="/" class="ws-back-link">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Tat ca Workshop
            </a>
            <h1><?= h($ws['title']) ?></h1>
            <p class="ws-tagline"><?= h($ws['tagline']) ?></p>
        </div>
    </div>

    <!-- ===== QUICK STATS BAR ===== -->
    <div class="ws-stats-bar">
        <div class="ws-stat-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
            <div>
                <span class="ws-stat-label">Thoi luong</span>
                <span class="ws-stat-value"><?= h($ws['duration']) ?></span>
            </div>
        </div>
        <div class="ws-stat-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            <div>
                <span class="ws-stat-label">Cap do</span>
                <span class="ws-stat-value"><?= h($ws['level']) ?></span>
            </div>
        </div>
        <div class="ws-stat-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            <div>
                <span class="ws-stat-label">Si so toi da</span>
                <span class="ws-stat-value"><?= h($ws['capacity']) ?> hoc vien</span>
            </div>
        </div>
        <div class="ws-stat-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
            <div>
                <span class="ws-stat-label">Giang vien</span>
                <span class="ws-stat-value"><?= h($ws['instructor']['name']) ?></span>
            </div>
        </div>
    </div>

    <!-- ===== MAIN CONTENT: 2 columns ===== -->
    <div class="ws-content-grid">

        <!-- LEFT: main content -->
        <div class="ws-main-col">

            <!-- OVERVIEW -->
            <section class="ws-section">
                <h2 class="ws-section-title">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    Gioi thieu tong quan
                </h2>
                <p class="ws-overview-text"><?= h($ws['overview']) ?></p>
            </section>

            <!-- BANNER IMAGE SECTION -->
            <section class="ws-section">
                <div class="ws-feature-img">
                    <img src="<?= h($ws['banner']) ?>" alt="<?= h($ws['title']) ?> illustration" loading="lazy">
                </div>
            </section>

            <!-- MODULES (Timeline-style) -->
            <section class="ws-section">
                <h2 class="ws-section-title">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
                    Noi dung chuong trinh
                </h2>
                <div class="ws-modules">
                    <?php foreach ($ws['modules'] as $i => $mod): ?>
                    <div class="ws-module">
                        <div class="ws-module-number"><?= $i + 1 ?></div>
                        <div class="ws-module-body">
                            <h3><?= h($mod['name']) ?></h3>
                            <p><?= h($mod['desc']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- PREREQUISITES -->
            <section class="ws-section">
                <h2 class="ws-section-title">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                    Yeu cau dau vao
                </h2>
                <ul class="ws-prereq-list">
                    <?php foreach ($ws['prerequisites'] as $req): ?>
                    <li>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="ws-check-icon"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        <?= h($req) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </section>

        </div>

        <!-- RIGHT: sidebar -->
        <div class="ws-side-col">

            <!-- REGISTER CTA CARD -->
            <div class="ws-card ws-cta-card">
                <div class="ws-cta-header">San sang tham gia?</div>
                <p class="ws-cta-desc">So luong cho ngoi co han. Hay dang ky ngay de dam bao cho cua ban.</p>
                <a href="/registrations/create?topic=<?= urlencode($ws['title']) ?>" class="btn btn-primary btn-block ws-btn-register">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Dang ky ngay
                </a>
                <a href="/" class="btn btn-ghost btn-block">Quay lai danh sach</a>
            </div>

            <!-- TOOLS -->
            <div class="ws-card">
                <h3 class="ws-card-title">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                    Cong cu su dung
                </h3>
                <div class="ws-tools-list">
                    <?php foreach ($ws['tools'] as $tool): ?>
                    <span class="ws-tool-chip"><?= h($tool) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PROJECT -->
            <div class="ws-card ws-project-card">
                <h3 class="ws-card-title">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                    Du an thuc hanh
                </h3>
                <h4 class="ws-project-name"><?= h($ws['project']['name']) ?></h4>
                <p class="ws-project-desc"><?= h($ws['project']['desc']) ?></p>
            </div>

            <!-- INSTRUCTOR -->
            <div class="ws-card ws-instructor-card">
                <h3 class="ws-card-title">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Giang vien
                </h3>
                <div class="ws-instructor-info">
                    <div class="ws-instructor-avatar"><?= h(mb_substr($ws['instructor']['name'], 4, 1)) ?></div>
                    <div>
                        <div class="ws-instructor-name"><?= h($ws['instructor']['name']) ?></div>
                        <div class="ws-instructor-role"><?= h($ws['instructor']['role']) ?></div>
                        <div class="ws-instructor-org"><?= h($ws['instructor']['org']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
