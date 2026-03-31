<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Tüm adminleri çek
$adminler = $db->query("SELECT * FROM admins ORDER BY id DESC text-end")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-1 text-dark fw-bold">Yönetici Listesi</h4>
        <p class="text-muted small">Sisteme giriş yetkisi olan yöneticileri buradan yönetebilirsiniz.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="admin_ekle.php" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-user-shield me-2"></i> Yeni Yönetici Ekle
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 text-secondary small fw-bold px-4 py-3">YÖNETİCİ BİLGİLERİ</th>
                        <th class="border-0 text-secondary small fw-bold py-3">KULLANICI ADI</th>
                        <th class="border-0 text-secondary small fw-bold py-3">KAYIT TARİHİ</th>
                        <th class="border-0 text-secondary small fw-bold text-end px-4 py-3">İŞLEMLER</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adminler as $a): ?>
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($a['ad_soyad'] ?? $a['username']) ?>&background=random" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($a['ad_soyad'] ?? 'İsimsiz Yönetici') ?></div>
                                    <div class="text-muted small">Sistem Yöneticisi</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border fw-medium px-3 py-2 rounded-pill">
                                <i class="fa-solid fa-at me-1"></i> <?= htmlspecialchars($a['username']) ?>
                            </span>
                        </td>
                        <td class="text-secondary small">
                            <?= date('d.m.Y H:i', strtotime($a['olusturma_tarihi'] ?? 'now')) ?>
                        </td>
                        <td class="text-end px-4">
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <a href="admin_duzenle.php?id=<?= $a['id'] ?>" class="btn btn-white btn-sm px-3" title="Düzenle">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </a>
                                <?php if ($a['username'] !== 'admin'): // Ana admin silinemez ?>
                                <a href="admin_sil.php?id=<?= $a['id'] ?>" class="btn btn-white btn-sm px-3" title="Sil" onclick="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?')">
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </a>
                                <?php else: ?>
                                <button class="btn btn-white btn-sm px-3 opacity-25" disabled title="Ana yönetici silinemez">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
