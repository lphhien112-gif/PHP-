<?php
/** Trang chu — WorkshopHub (Danh sach Workshop) */
$workshops = [
    [
        'title' => 'Web Development',
        'img' => '/assets/img/banners/web-development.png',
        'desc' => 'Hoc cach xay dung ung dung web hien dai voi HTML, CSS va PHP.'
    ],
    [
        'title' => 'Data Science',
        'img' => '/assets/img/banners/data-science.png',
        'desc' => 'Kham pha the gioi du lieu, phan tich va truc quan hoa.'
    ],
    [
        'title' => 'UI/UX Design',
        'img' => '/assets/img/banners/ui-ux-design.png',
        'desc' => 'Thiet ke giao dien nguoi dung va trai nghiem tuyet voi.'
    ],
    [
        'title' => 'Mobile App',
        'img' => '/assets/img/banners/mobile-app.png',
        'desc' => 'Phat trien ung dung di dong cho ca iOS va Android.'
    ],
    [
        'title' => 'Cyber Security',
        'img' => '/assets/img/banners/cyber-security.png',
        'desc' => 'Bao mat he thong, mang va ung dung khoi cac moi de doa.'
    ]
];
?>
<div class="page-head">
    <div>
        <h1>Chon chu de <span style="color:var(--accent);">Workshop</span></h1>
        <p class="muted">Danh sach cac workshop hien co. Nhan "Dang ky tham gia" de giu cho.</p>
    </div>
</div>

<div class="workshop-grid">
    <?php foreach ($workshops as $w): ?>
    <div class="workshop-card">
        <div class="workshop-banner">
            <img src="<?= h($w['img']) ?>" alt="<?= h($w['title']) ?>" loading="lazy">
        </div>
        <div class="workshop-info">
            <h3><?= h($w['title']) ?></h3>
            <p><?= h($w['desc']) ?></p>
            <div class="workshop-actions">
                <a href="/workshop/details?topic=<?= urlencode($w['title']) ?>" class="btn btn-primary btn-block">Tim hieu them</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
