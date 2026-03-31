<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Basit istatistikler çekelim dedede
$ilan_sayisi = $db->query("SELECT COUNT(*) FROM ilanlar")->fetchColumn();
$yonetici_sayisi = $db->query("SELECT COUNT(*) FROM portfoy_yoneticileri")->fetchColumn();
$toplam_fiyat = $db->query("SELECT SUM(fiyat) FROM ilanlar")->fetchColumn();
$ortalama_fiyat = $ilan_sayisi > 0 ? $toplam_fiyat / $ilan_sayisi : 0;

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-1 text-dark fw-bold">Anasayfa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-primary"><i
                            class="fa-solid fa-house"></i> Emlak</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analytics Dashboard</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Toplam Değer / Red Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-danger text-white h-100 shadow-sm border-0 stat-card rounded-0">
            <div class="card-body p-4 d-flex justify-content-between">
                <div>
                    <h6 class="mb-2 fw-semibold">Toplam Portföy Değeri</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($toplam_fiyat, 0, ',', '.') ?> ₺</h3>
                    <small class="mt-3 d-block bg-white bg-opacity-25 px-2 py-1 rounded" style="font-size:0.75rem;"><i
                            class="fa-solid fa-arrow-up border-end border-light pe-1 me-1"></i> Yayındaki Tutar</small>
                </div>
                <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center mt-2 shadow-sm"
                    style="width:40px; height:40px;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <a href="ilanlar.php" class="stretched-link"></a>
        </div>
    </div>

    <!-- İlan Sayısı / Blue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100 shadow-sm border-0 stat-card rounded-0"
            style="background-color: #4361ee !important;">
            <div class="card-body p-4 d-flex justify-content-between">
                <div>
                    <h6 class="mb-2 fw-semibold">Toplam İlanlar</h6>
                    <h3 class="mb-0 fw-bold"><?= $ilan_sayisi ?></h3>
                    <small class="mt-3 d-block bg-white bg-opacity-25 px-2 py-1 rounded" style="font-size:0.75rem;"><i
                            class="fa-solid fa-building border-end border-light pe-1 me-1"></i> Aktif İlanlar</small>
                </div>
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mt-2 shadow-sm"
                    style="width:40px; height:40px;">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>
            <a href="ilanlar.php" class="stretched-link"></a>
        </div>
    </div>

    <!-- Ortalama Fiyat / Green Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-success text-white h-100 shadow-sm border-0 stat-card rounded-0"
            style="background-color: #2ecc71 !important;">
            <div class="card-body p-4 d-flex justify-content-between">
                <div>
                    <h6 class="mb-2 fw-semibold">Ortalama Fiyat</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($ortalama_fiyat, 0, ',', '.') ?> ₺</h3>
                    <small class="mt-3 d-block bg-white bg-opacity-25 px-2 py-1 rounded" style="font-size:0.75rem;"><i
                            class="fa-solid fa-calculator border-end border-light pe-1 me-1"></i> Tüm İlanlarda</small>
                </div>
                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center mt-2 shadow-sm"
                    style="width:40px; height:40px;">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Yöneticiler / Orange Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning text-white h-100 shadow-sm border-0 stat-card rounded-0"
            style="background-color: #ff9f43 !important;">
            <div class="card-body p-4 d-flex justify-content-between">
                <div>
                    <h6 class="mb-2 fw-semibold">Danışmanlar</h6>
                    <h3 class="mb-0 fw-bold"><?= $yonetici_sayisi ?></h3>
                    <small class="mt-3 d-block bg-white bg-opacity-25 px-2 py-1 rounded text-dark"
                        style="font-size:0.75rem;"><i class="fa-solid fa-users border-end border-dark pe-1 me-1"></i>
                        Aktif Danışman Kaydı</small>
                </div>
                <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center mt-2 shadow-sm"
                    style="width:40px; height:40px;">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>
            <a href="yoneticiler.php" class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- İstatistik Kutucukları -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100 rounded-0">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark m-0">Sistem İşlemleri Geçmişi</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-borderless text-center align-middle small text-muted">
                        <thead class="border-bottom">
                            <tr>
                                <th class="text-start">DURUM</th>
                                <th>SUNUCU</th>
                                <th>VERİTABANI</th>
                                <th>CPU</th>
                                <th>ERİŞİM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start text-dark fw-bold">Online Servis</td>
                                <td>Aktif</td>
                                <td>Bağlı <i class="fa-solid fa-circle-check text-success ms-1"></i></td>
                                <td>%3,2</td>
                                <td>43ms</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Kutusu -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100 rounded-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="fw-bold text-dark m-0">Profil</h6>
            </div>
            <div class="card-body text-center pt-2">
                <div class="rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm border"
                    style="width: 100px; height: 100px;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_isim'] ?? 'Admin') ?>&background=random"
                        class="rounded-circle img-fluid w-100 h-100">
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars(ucfirst($_SESSION['admin_isim'] ?? 'Admin Yöneticisi')) ?>
                </h5>
                <p class="text-muted small mb-3">Sistem Yöneticisi | Aktif</p>
                <a href="logout.php" class="btn btn-primary d-block w-100 rounded-pill px-4 shadow-sm"
                    style="background-color: #4361ee; border:none;"><i class="fa-solid fa-crown me-1"></i> Çıkış Yap</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>