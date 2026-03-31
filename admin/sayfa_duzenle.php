<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: sayfalar.php");
    exit;
}

$id = $_GET['id'];
$sayfa = $db->prepare("SELECT * FROM sayfalar WHERE id = ?");
$sayfa->execute([$id]);
$sayfa = $sayfa->fetch();

if (!$sayfa) {
    header("Location: sayfalar.php");
    exit;
}

$mesaj = "";
$hata = "";

// --- FORM İŞLEMLERİ (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. ADIM: Sayfa Temel Bilgilerini Güncelle
    if (isset($_POST['update_sayfa'])) {
        $baslik = $_POST['baslik'];
        $icerik = $_POST['icerik']; // Klasik metin alanı (fallback)
        if (!empty($baslik)) {
            $stmt = $db->prepare("UPDATE sayfalar SET baslik = ?, icerik = ? WHERE id = ?");
            $stmt->execute([$baslik, $icerik, $id]);
            $mesaj = "Sayfa temel bilgileri güncellendi.";
            $sayfa['baslik'] = $baslik;
            $sayfa['icerik'] = $icerik;
        }
    }

    // 2. ADIM: Yeni Blok Ekle
    if (isset($_POST['add_block'])) {
        $tip = $_POST['blok_tipi'];
        $sira_query = $db->prepare("SELECT MAX(sira) as maks FROM sayfa_bloklari WHERE sayfa_id = ?");
        $sira_query->execute([$id]);
        $sira = ($sira_query->fetch()['maks'] ?? 0) + 1;

        $ins = $db->prepare("INSERT INTO sayfa_bloklari (sayfa_id, blok_tipi, baslik, sira) VALUES (?, ?, ?, ?)");
        $ins->execute([$id, $tip, 'Yeni ' . ucfirst($tip) . ' Bölümü', $sira]);
        $mesaj = "Yeni bölüm başarıyla eklendi.";
    }

    // 3. ADIM: Blok Güncelle
    if (isset($_POST['update_block'])) {
        $bid = $_POST['block_id'];
        $b_baslik = $_POST['b_baslik'];
        $b_alt_baslik = $_POST['b_alt_baslik'];
        $b_icerik = $_POST['b_icerik'];
        $b_buton_metni = $_POST['b_buton_metni'];
        $b_buton_link = $_POST['b_buton_link'];
        $b_ikon = $_POST['b_ikon'];

        // Görsel Yükleme
        $gorsel_yolu = $_POST['current_gorsel'];
        if (isset($_FILES['b_gorsel']) && $_FILES['b_gorsel']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['b_gorsel']['tmp_name'];
            $name = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES['b_gorsel']['name']));
            if (move_uploaded_file($tmp, __DIR__ . '/uploads/pages/' . $name)) {
                $gorsel_yolu = $name;
            }
        }

        $upd = $db->prepare("UPDATE sayfa_bloklari SET baslik = ?, alt_baslik = ?, icerik = ?, buton_metni = ?, buton_link = ?, ikon = ?, gorsel_yolu = ? WHERE id = ? AND sayfa_id = ?");
        if ($upd->execute([$b_baslik, $b_alt_baslik, $b_icerik, $b_buton_metni, $b_buton_link, $b_ikon, $gorsel_yolu, $bid, $id])) {
            $mesaj = "Bölüm içeriği başarıyla güncellendi.";
        }
    }

    // 4. ADIM: Blok Sil
    if (isset($_POST['delete_block'])) {
        $bid = $_POST['block_id'];
        $del = $db->prepare("DELETE FROM sayfa_bloklari WHERE id = ? AND sayfa_id = ?");
        $del->execute([$bid, $id]);
        $mesaj = "Bölüm kaldırıldı.";
    }

    // 5. ADIM: Sıralama (Yukarı/Aşağı)
    if (isset($_POST['move_block'])) {
        $bid = $_POST['block_id'];
        $dir = $_POST['direction'];
        $curr_sira = $_POST['current_sira'];

        if ($dir === 'up') {
            $target = $db->prepare("SELECT id, sira FROM sayfa_bloklari WHERE sayfa_id = ? AND sira < ? ORDER BY sira DESC LIMIT 1");
        } else {
            $target = $db->prepare("SELECT id, sira FROM sayfa_bloklari WHERE sayfa_id = ? AND sira > ? ORDER BY sira ASC LIMIT 1");
        }
        $target->execute([$id, $curr_sira]);
        $t = $target->fetch();
        if ($t) {
            $db->prepare("UPDATE sayfa_bloklari SET sira = ? WHERE id = ?")->execute([$t['sira'], $bid]);
            $db->prepare("UPDATE sayfa_bloklari SET sira = ? WHERE id = ?")->execute([$curr_sira, $t['id']]);
        }
    }
}

// Blokları Getir
$bloklar = $db->prepare("SELECT * FROM sayfa_bloklari WHERE sayfa_id = ? ORDER BY sira ASC");
$bloklar->execute([$id]);
$bloklar = $bloklar->fetchAll();

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Modüler Sayfa Oluşturucu</h4>
            <span class="text-muted small"><strong><?= htmlspecialchars($sayfa['baslik']) ?></strong> sayfasını Elementor mantığıyla bölümler halinde tasarlayın.</span>
        </div>
        <a href="sayfalar.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Geri Dön</a>
    </div>
</div>

<?php if($mesaj): ?>
    <div class="alert alert-success shadow-sm rounded-0 border-0 border-start border-5 border-success mb-4"><i class="fa-solid fa-check-circle me-2"></i> <?= $mesaj ?></div>
<?php endif; ?>

<div class="row">
    <!-- SOL TARAF: MEVCUT BLOKLAR -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-primary m-0"><i class="fa-solid fa-layer-group me-2"></i>Sayfa Bölümleri (Bloklar)</h6>
            </div>
            <div class="card-body p-0">
                <?php if(empty($bloklar)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="fa-solid fa-puzzle-piece fa-3x mb-3 opacity-25"></i>
                        <p>Henüz bir bölüm eklenmemiş. Sağ taraftaki menüden yeni bir blok ekleyerek başlayın.</p>
                    </div>
                <?php endif; ?>

                <div class="accordion accordion-flush" id="blocksAccordion">
                    <?php foreach($bloklar as $b): ?>
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header d-flex align-items-center bg-white pe-3" id="heading<?= $b['id'] ?>">
                                <div class="d-flex flex-column ms-3 me-2 py-2">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="block_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_sira" value="<?= $b['sira'] ?>">
                                        <button type="submit" name="move_block" value="1" class="btn btn-link p-0 text-muted" title="Yukarı Taşı"><input type="hidden" name="direction" value="up"><i class="fa-solid fa-chevron-up shadow-none"></i></button>
                                        <button type="submit" name="move_block" value="1" class="btn btn-link p-0 text-muted" title="Aşağı Taşı"><input type="hidden" name="direction" value="down"><i class="fa-solid fa-chevron-down shadow-none"></i></button>
                                    </form>
                                </div>
                                <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $b['id'] ?>" aria-expanded="false">
                                    <span class="badge bg-light text-primary border me-3"><?= strtoupper($b['blok_tipi']) ?></span>
                                    <?= htmlspecialchars($b['baslik'] ?: 'Başlıksız Bölüm') ?>
                                </button>
                                <form method="POST" onsubmit="return confirm('Bu bölümü silmek istediğinize emin misiniz?')" class="ms-auto">
                                    <input type="hidden" name="block_id" value="<?= $b['id'] ?>">
                                    <button type="submit" name="delete_block" class="btn btn-outline-danger btn-sm border-0 rounded-circle"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </h2>
                            <div id="collapse<?= $b['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $b['id'] ?>" data-bs-parent="#blocksAccordion">
                                <div class="accordion-body bg-light p-4">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="block_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_gorsel" value="<?= $b['gorsel_yolu'] ?>">
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Bölüm Başlığı</label>
                                                <input type="text" class="form-control" name="b_baslik" value="<?= htmlspecialchars($b['baslik']) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Alt Başlık (Opsiyonel)</label>
                                                <input type="text" class="form-control" name="b_alt_baslik" value="<?= htmlspecialchars($b['alt_baslik']) ?>">
                                            </div>
                                            
                                            <?php if($b['blok_tipi'] === 'hero' || $b['blok_tipi'] === 'metin_gorsel'): ?>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Görsel / Arka Plan</label>
                                                    <input type="file" class="form-control" name="b_gorsel">
                                                    <?php if($b['gorsel_yolu']): ?>
                                                        <div class="mt-2 text-primary small"><i class="fa-solid fa-image me-1"></i> <?= $b['gorsel_yolu'] ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if($b['blok_tipi'] === 'ozellikler'): ?>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">İkon (FontAwesome Sınıfı)</label>
                                                    <input type="text" class="form-control" name="b_ikon" value="<?= htmlspecialchars($b['ikon']) ?>" placeholder="fa-solid fa-house">
                                                </div>
                                            <?php endif; ?>

                                            <div class="col-12">
                                                <label class="form-label small fw-bold">İçerik Metni</label>
                                                <textarea class="form-control" name="b_icerik" rows="4"><?= htmlspecialchars($b['icerik']) ?></textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Buton Metni (Opsiyonel)</label>
                                                <input type="text" class="form-control" name="b_buton_metni" value="<?= htmlspecialchars($b['buton_metni']) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Buton Link (Opsiyonel)</label>
                                                <input type="text" class="form-control" name="b_buton_link" value="<?= htmlspecialchars($b['buton_link']) ?>">
                                            </div>

                                            <div class="col-12 mt-4">
                                                <button type="submit" name="update_block" class="btn btn-primary px-4 fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i> Bölümü Güncelle</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SAĞ TARAF: YENİ BLOK EKLE VE TEMEL BİLGİLER -->
    <div class="col-lg-4">
        <!-- TEMEL BİLGİLER -->
        <div class="card shadow-sm border-0 rounded-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-dark m-0"><i class="fa-solid fa-gear me-2 text-secondary"></i>Temel Ayarlar</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sayfa Başlığı</label>
                        <input type="text" class="form-control p-2" name="baslik" value="<?= htmlspecialchars($sayfa['baslik']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Slug (Benzersiz Bağlantı)</label>
                        <input type="text" class="form-control bg-light p-2" value="<?= $sayfa['slug'] ?>" readonly>
                    </div>
                    <div class="mb-3 d-none">
                        <label class="form-label small fw-bold">Yedek İçerik (Eski Sistem)</label>
                        <textarea class="form-control" name="icerik" rows="3"><?= htmlspecialchars($sayfa['icerik']) ?></textarea>
                    </div>
                    <button type="submit" name="update_sayfa" class="btn btn-dark w-100 fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i> Başlığı Kaydet</button>
                </form>
            </div>
        </div>

        <!-- BÖLÜM EKLE -->
        <div class="card shadow-sm border-0 rounded-0">
            <div class="card-header bg-primary text-white py-3 border-0">
                <h6 class="fw-bold m-0"><i class="fa-solid fa-plus-circle me-2"></i>Yeni Bölüm Ekle</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php 
                    $bilesenler = [
                        'hero' => ['icon' => 'fa-image', 'title' => 'Giriş/Kapak (Hero)', 'desc' => 'Görsel üzerine başlık ve buton.'],
                        'metin_gorsel' => ['icon' => 'fa-columns', 'title' => 'Metin & Görsel', 'desc' => 'Yan yana içerik ve resim.'],
                        'ozellikler' => ['icon' => 'fa-list-check', 'title' => 'Özellik Listesi', 'desc' => 'İkonlu kısa açıklamalar.'],
                        'metin_icerik' => ['icon' => 'fa-paragraph', 'title' => 'Düz Metin Bloğu', 'desc' => 'Geniş açıklama alanları.'],
                        'harita' => ['icon' => 'fa-map-location-dot', 'title' => 'Harita/İletişim', 'desc' => 'Alt kısım iletişim detayları.']
                    ];
                    foreach($bilesenler as $tip => $info):
                    ?>
                        <form method="POST">
                            <input type="hidden" name="blok_tipi" value="<?= $tip ?>">
                            <button type="submit" name="add_block" class="list-group-item list-group-item-action py-3 px-4 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fa-solid <?= $info['icon'] ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small"><?= $info['title'] ?></div>
                                        <div class="text-muted extra-small" style="font-size: 0.75rem;"><?= $info['desc'] ?></div>
                                    </div>
                                    <i class="fa-solid fa-plus ms-auto text-muted small"></i>
                                </div>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
