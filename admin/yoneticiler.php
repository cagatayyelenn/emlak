<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$yoneticiler = $db->query("SELECT * FROM portfoy_yoneticileri ORDER BY id DESC")->fetchAll();
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-1 text-dark fw-bold">Danışman Listesi</h4>
        <p class="text-muted small">Tüm gayrimenkul danışmanlarını buradan yönetebilirsiniz.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="yonetici_ekle.php" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i> Yeni Danışman Ekle
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if(isset($_GET['basari'])): ?>
            <div class="alert alert-success">İşlem başarıyla tamamlandı.</div>
        <?php endif; ?>
        <?php if(isset($_GET['hata'])): ?>
            <div class="alert alert-danger">İşlem sırasında bir hata oluştu.</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 text-secondary small fw-bold">DANIŞMAN BİLGİLERİ</th>
                        <th class="border-0 text-secondary small fw-bold">İLETİŞİM</th>
                        <th class="border-0 text-secondary small fw-bold">E-POSTA</th>
                        <th class="border-0 text-secondary small fw-bold">KAYIT TARİHİ</th>
                        <th class="border-0 text-secondary small fw-bold text-end">İŞLEMLER</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($yoneticiler) > 0): ?>
                        <?php foreach($yoneticiler as $y): ?>
                        <tr>
                            <td>
                                <?php 
                                    $avatar_url = !empty($y['profil_resmi']) 
                                        ? 'uploads/yoneticiler/' . $y['profil_resmi'] 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($y['ad_soyad']) . '&background=random&color=fff';
                                ?>
                                <img src="<?= $avatar_url ?>" class="rounded-circle shadow-sm" style="width:50px; height:50px; object-fit:cover;" alt="<?= htmlspecialchars($y['ad_soyad']) ?>">
                            </td>
                            <td><span class="fw-bold text-dark"><?= htmlspecialchars($y['ad_soyad']) ?></span></td>
                            <td><i class="fa-solid fa-phone me-1 text-muted small"></i> <?= htmlspecialchars($y['telefon'] ?? '-') ?></td>
                            <td><i class="fa-solid fa-envelope me-1 text-muted small"></i> <?= htmlspecialchars($y['eposta'] ?? '-') ?></td>
                            <td class="text-end">
                                <a href="yonetici_duzenle.php?id=<?= $y['id'] ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen text-white"></i></a>
                                <a href="yonetici_sil.php?id=<?= $y['id'] ?>" class="btn btn-sm btn-danger btn-delete"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Kayıtlı portföy yöneticisi bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
