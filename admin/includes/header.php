<?php
// includes/header.php
require_once 'includes/database.php';
try {
    $site_stmt = $db->query("SELECT * FROM site_ayarlari LIMIT 1");
    $site_set = $site_stmt->fetch();
} catch (PDOException $e) {
    // Tablo henüz oluşturulmamış olabilir, sessizce devam et
    $site_set = [];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_set['site_baslik'] ?? 'Maxwell Emlak Ofisi') ?></title>
    
    <?php if(!empty($site_set['favicon'])): ?>
    <link rel="icon" type="image/x-icon" href="uploads/settings/<?= $site_set['favicon'] ?>">
    <?php endif; ?>

    <!-- SEO Meta -->
    <meta name="description" content="<?= htmlspecialchars($site_set['site_aciklama'] ?? '') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($site_set['site_anahtar_kelimeler'] ?? '') ?>">

    <!-- Google Search Console -->
    <?= $site_set['google_search_console'] ?? '' ?>

    <!-- Google Analytics -->
    <?= $site_set['google_analytics'] ?? '' ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-white border-end shadow-sm" id="sidebar-wrapper">
        <div class="sidebar-heading bg-primary text-white py-3 px-4 fs-4 fw-bold d-flex align-items-center shadow-sm" style="height: 70px; background-color: #4361ee !important;">
            <i class="fa-solid fa-building me-2 text-warning fs-3"></i><span>Maxwell Emlak</span>
        </div>
        <div class="list-group list-group-flush mt-3 px-1">
            <a href="index.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-house me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">Anasayfa</span>
            </a>
            
            <hr class="text-secondary opacity-25 my-3 mx-4">
            
            <div class="px-4 mb-2 small text-muted fw-bold" style="font-size:0.80rem;">İLANLAR</div>
            <a href="ilanlar.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-list me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">İlan listesi</span>
            </a>
            <a href="ilan_ekle.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-plus me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">İlan ekle</span>
            </a>
            
            <hr class="text-secondary opacity-25 my-3 mx-4">
            
            <div class="px-4 mb-2 small text-muted fw-bold" style="font-size:0.80rem;">PORTFÖY</div>
            <a href="yoneticiler.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-users me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">Portföy listesi</span>
            </a>
            <a href="yonetici_ekle.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-user-plus me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">Portföy ekle</span>
            </a>

            <hr class="text-secondary opacity-25 my-3 mx-4">
            
            <div class="px-4 mb-2 small text-muted fw-bold" style="font-size:0.80rem;">SAYFA YÖNETİMİ</div>
            <a href="sayfalar.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-file-lines me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">Tüm sayfalar</span>
            </a>

            <hr class="text-secondary opacity-25 my-3 mx-4">
            
            <div class="px-4 mb-2 small text-muted fw-bold" style="font-size:0.80rem;">SİSTEM</div>
            <a href="ayarlar.php" class="list-group-item list-group-item-action border-0 d-flex align-items-center px-4">
                <i class="fa-solid fa-gear me-3 fs-5" style="color:#6c757d; width:25px; text-align:center;"></i>
                <span class="fw-semibold">Site Ayarları</span>
            </a>
        </div>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper" class="flex-grow-1" style="background-color: #f4f7fa;">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-2 mb-4" style="height: 70px;">
            <div class="d-flex align-items-center w-100 justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light d-md-none me-3" id="menu-toggle"><i class="fa-solid fa-bars text-secondary"></i></button>
                    <span class="d-none d-md-block fw-bold text-secondary"><?= htmlspecialchars($site_set['site_baslik'] ?? 'Maxwell Emlak Ofisi') ?></span>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-md-flex align-items-center bg-light px-3 py-1 rounded-pill">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_isim'] ?? 'Admin') ?>&background=e9ecef&color=4361ee" class="rounded-circle shadow-sm" style="width:30px;height:30px;">
                        <span class="text-secondary fw-bold ms-2 small"><?= htmlspecialchars($_SESSION['admin_isim'] ?? 'Admin') ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-danger btn-sm px-3 py-2 fw-bold rounded-pill shadow-sm d-flex align-items-center"><i class="fa-solid fa-power-off me-2"></i> Çıkış Yap</a>
                </div>
            </div>
        </nav>
        <div class="container-fluid px-4 main-container pb-5">

