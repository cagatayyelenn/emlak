<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once '../includes/slug.php';

if (!isset($_GET['id'])) {
    header("Location: ilanlar.php");
    exit;
}
$id = $_GET['id'];

// Upload dizinlerini kontrol et/oluştur
$upload_dirs = ['uploads', 'uploads/images', 'uploads/videos'];
foreach($upload_dirs as $dir) {
    if(!is_dir(__DIR__ . '/' . $dir)) mkdir(__DIR__ . '/' . $dir, 0777, true);
}

// AJAX içermeyen basit medya silme
if (isset($_GET['medya_sil'])) {
    $mid = $_GET['medya_sil'];
    $mstmt = $db->prepare("SELECT * FROM ilan_medya WHERE id = ? AND ilan_id = ?");
    $mstmt->execute([$mid, $id]);
    $m = $mstmt->fetch();
    if ($m) {
        if ($m['medya_tipi'] === 'gorsel') {
            @unlink(__DIR__ . '/uploads/images/' . $m['dosya_yolu']);
        } elseif ($m['medya_tipi'] === 'video') {
            @unlink(__DIR__ . '/uploads/videos/' . $m['dosya_yolu']);
        }
        $db->prepare("DELETE FROM ilan_medya WHERE id = ?")->execute([$mid]); 
    }
    header("Location: ilan_duzenle.php?id=$id&basari=1");
    exit;
}

// Mevcut resmi Vitrin yap
if (isset($_GET['vitrin_yap'])) {
    $db->prepare("UPDATE ilanlar SET vitrin_gorseli = ? WHERE id = ?")->execute([$_GET['vitrin_yap'], $id]);
    header("Location: ilan_duzenle.php?id=$id&basari=2");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = ['baslik', 'slug', 'sahibinden_link', 'durumu', 'aciklama', 'fiyat', 'portfoy_yoneticisi_id', 'il', 'ilce', 'mahalle', 'ilan_no', 'ilan_tarihi', 'emlak_tipi', 'm2_brut', 'm2_net', 'oda_sayisi', 'bina_yasi', 'bulundugu_kat', 'kat_sayisi', 'isitma', 'banyo_sayisi', 'mutfak', 'balkon', 'asansor', 'otopark', 'esyali', 'kullanim_durumu', 'site_icerisinde', 'site_adi', 'aidat', 'krediye_uygun', 'tapu_durumu', 'konum', 'harita_konumu'];
        
        $vals_assoc = []; 
        foreach($fields as $f) {
            if ($f == 'slug') continue;
            $val = $_POST[$f] ?? null;
            if($val === '') $val = null;
            $vals_assoc[$f] = $val;
        }
        
        // Slug oluştur/güncelle
        $vals_assoc['slug'] = createSlug($vals_assoc['baslik']);

        // Para formatı temizliği
        foreach(['fiyat', 'aidat'] as $pa) {
            if(isset($vals_assoc[$pa]) && $vals_assoc[$pa] !== null) {
                $temiz = str_replace('.', '', $vals_assoc[$pa]);
                $temiz = str_replace(',', '.', $temiz);
                $vals_assoc[$pa] = (float) $temiz;
            }
        }

        if (!empty($_POST['baslik'])) {
            $setQuery = implode('=?, ', $fields) . '=?'; 
            
            $vals = [];
            foreach ($fields as $f) {
                $vals[] = $vals_assoc[$f];
            }
            $vals[] = $id;

            $stmt = $db->prepare("UPDATE ilanlar SET $setQuery WHERE id=?");
            $stmt->execute($vals);

            // Yeni Görselleri yükle
            if (isset($_FILES['gorseller']) && !empty($_FILES['gorseller']['name'][0])) {
                $count = count($_FILES['gorseller']['name']);
                for ($i=0; $i<$count; $i++) {
                    if($_FILES['gorseller']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp = $_FILES['gorseller']['tmp_name'][$i];
                        $original_name = basename($_FILES['gorseller']['name'][$i]);
                        $clean_name = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $original_name);
                        $final_name = time() . '_' . rand(100,999) . '_' . $clean_name;
                        
                        $upload_path = __DIR__ . '/uploads/images/' . $final_name;
                        
                        if (move_uploaded_file($tmp, $upload_path)) {
                            $mstmt = $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'gorsel', ?)");
                            $mstmt->execute([$id, $final_name]);
                        }
                    }
                }
            }

            // Yeni Video yükle
            if (isset($_FILES['video_dosya']) && $_FILES['video_dosya']['error'] === UPLOAD_ERR_OK) {
                $vtmp = $_FILES['video_dosya']['tmp_name'];
                $vname = time() . '_' . rand(100,999) . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES['video_dosya']['name']));
                if (move_uploaded_file($vtmp, __DIR__ . '/uploads/videos/' . $vname)) {
                    $mstmt = $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'video', ?)");
                    $mstmt->execute([$id, $vname]);
                }
            } elseif (!empty($_POST['video_url'])) {
                $mstmt = $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'video_url', ?)");
                $mstmt->execute([$id, $_POST['video_url']]);
            }

            header("Location: ilan_duzenle.php?id=$id&basari=1");
            exit;
        }
    } catch (Exception $e) {
        $error_msg = "Sistem Hatası: " . $e->getMessage();
    }
}

$ilan = $db->prepare("SELECT * FROM ilanlar WHERE id = ?");
$ilan->execute([$id]);
$ilan = $ilan->fetch();
if (!$ilan) {
    header("Location: ilanlar.php");
    exit;
}

$medyalar = $db->prepare("SELECT * FROM ilan_medya WHERE ilan_id = ? ORDER BY id ASC");
$medyalar->execute([$id]);
$medyalar = $medyalar->fetchAll();

$yoneticiler = $db->query("SELECT * FROM portfoy_yoneticileri ORDER BY ad_soyad ASC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-12 d-flex justify-content-between">
        <h4 class="mb-1 text-dark fw-bold">İlanı Düzenle: <span class="text-primary">#<?= $ilan['ilan_no'] ?? $ilan['id'] ?></span></h4>
        <a href="ilanlar.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Tüm İlanlar</a>
    </div>
</div>

<?php if(isset($error_msg)): ?>
    <div class="alert alert-danger shadow-sm rounded-0 border-0 border-start border-5 border-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error_msg ?></div>
<?php endif; ?>

<?php if(isset($_GET['basari']) && $_GET['basari'] == 1): ?>
    <div class="alert alert-success shadow-sm rounded-0 border-0 border-start border-5 border-success"><i class="fa-solid fa-check-circle me-2"></i> İlan bilgileri başarıyla güncellendi.</div>
<?php elseif(isset($_GET['basari']) && $_GET['basari'] == 2): ?>
    <div class="alert alert-info shadow-sm rounded-0 border-0 border-start border-5 border-info"><i class="fa-solid fa-star text-warning me-2"></i> İlanın Vitrin (Kapak) görseli başarıyla değiştirildi.</div>
<?php endif; ?>

<form action="ilan_duzenle.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- SOL FORM ALANI -->
        <div class="col-xl-8 col-lg-7">
            
            <div class="card shadow-sm border-0 mb-4 rounded-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-primary m-0"><i class="fa-solid fa-circle-info me-2"></i>Temel Bilgiler</h6>
                </div>
                <div class="card-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">İlan Başlığı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="baslik" value="<?= htmlspecialchars($ilan['baslik'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Durumu <span class="text-danger">*</span></label>
                            <select class="form-select border-primary fw-bold text-primary" name="durumu">
                                <?php $drm = $ilan['durumu'] ?? 'Satılık'; ?>
                                <option value="Satılık" <?= $drm=='Satılık'?'selected':'' ?>>Satılık</option>
                                <option value="Kiralık" <?= $drm=='Kiralık'?'selected':'' ?>>Kiralık</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">İlan No</label>
                            <input type="text" class="form-control" name="ilan_no" value="<?= htmlspecialchars($ilan['ilan_no'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">Fiyat (₺)</label>
                            <input type="text" class="form-control price-format" name="fiyat" value="<?= isset($ilan['fiyat']) ? number_format($ilan['fiyat'], (floor($ilan['fiyat']) == $ilan['fiyat'] ? 0 : 2), ',', '.') : '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">İlan Tarihi</label>
                            <input type="date" class="form-control" name="ilan_tarihi" value="<?= htmlspecialchars($ilan['ilan_tarihi'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">Gayrimenkul Danışmanı</label>
                            <select class="form-select" name="portfoy_yoneticisi_id">
                                <option value="">-- Seçin --</option>
                                <?php foreach($yoneticiler as $y): ?>
                                    <option value="<?= $y['id'] ?>" <?= $y['id'] == $ilan['portfoy_yoneticisi_id'] ? 'selected' : '' ?>><?= htmlspecialchars($y['ad_soyad']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-secondary small">Açıklama</label>
                            <textarea class="form-control" name="aciklama" rows="4"><?= htmlspecialchars($ilan['aciklama'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-success m-0"><i class="fa-solid fa-list-check me-2"></i>Emlak Özellikleri</h6>
                </div>
                <div class="card-body px-4 py-4 bg-light">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Emlak Tipi</label>
                            <select class="form-select" name="emlak_tipi">
                                <?php $tip = $ilan['emlak_tipi'] ?? ''; ?>
                                <option value="Daire" <?= $tip=='Daire'?'selected':'' ?>>Daire</option>
                                <option value="Müstakil Ev" <?= $tip=='Müstakil Ev'?'selected':'' ?>>Müstakil Ev</option>
                                <option value="Villa" <?= $tip=='Villa'?'selected':'' ?>>Villa</option>
                                <option value="Arsa" <?= $tip=='Arsa'?'selected':'' ?>>Arsa</option>
                                <option value="İşyeri" <?= $tip=='İşyeri'?'selected':'' ?>>İşyeri</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Oda Sayısı</label>
                            <select class="form-select" name="oda_sayisi">
                                <?php $oda = $ilan['oda_sayisi'] ?? ''; ?>
                                <option value="Stüdyo (1+0)" <?= $oda=='Stüdyo (1+0)'?'selected':'' ?>>Stüdyo (1+0)</option>
                                <option value="1+1" <?= $oda=='1+1'?'selected':'' ?>>1+1</option>
                                <option value="2+1" <?= $oda=='2+1'?'selected':'' ?>>2+1</option>
                                <option value="3+1" <?= $oda=='3+1'?'selected':'' ?>>3+1</option>
                                <option value="4+1" <?= $oda=='4+1'?'selected':'' ?>>4+1</option>
                                <option value="5+1 ve üzeri" <?= $oda=='5+1 ve üzeri'?'selected':'' ?>>5+1 ve üzeri</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">m² (Brüt)</label>
                            <input type="number" class="form-control" name="m2_brut" value="<?= htmlspecialchars($ilan['m2_brut'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">m² (Net)</label>
                            <input type="number" class="form-control" name="m2_net" value="<?= htmlspecialchars($ilan['m2_net'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Bina Yaşı</label>
                            <select class="form-select" name="bina_yasi">
                                <?php $yas = $ilan['bina_yasi'] ?? ''; ?>
                                <option value="0" <?= $yas=='0'?'selected':'' ?>>0 (Yeni)</option>
                                <option value="1-5" <?= $yas=='1-5'?'selected':'' ?>>1-5 Arası</option>
                                <option value="6-10" <?= $yas=='6-10'?'selected':'' ?>>6-10 Arası</option>
                                <option value="11-15" <?= $yas=='11-15'?'selected':'' ?>>11-15 Arası</option>
                                <option value="16-20" <?= $yas=='16-20'?'selected':'' ?>>16-20 Arası</option>
                                <option value="21+" <?= $yas=='21+'?'selected':'' ?>>21 ve Üzeri</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Bulunduğu Kat</label>
                            <input type="text" class="form-control" name="bulundugu_kat" value="<?= htmlspecialchars($ilan['bulundugu_kat'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Kat Sayısı</label>
                            <input type="number" class="form-control" name="kat_sayisi" value="<?= htmlspecialchars($ilan['kat_sayisi'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Banyo Sayısı</label>
                            <input type="number" class="form-control" name="banyo_sayisi" value="<?= htmlspecialchars($ilan['banyo_sayisi'] ?? '1') ?>">
                        </div>
                        
                        <!-- Sahibinden Link -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold text-secondary small">Sahibinden.com İlan Linki</label>
                            <input type="url" class="form-control" name="sahibinden_link" value="<?= htmlspecialchars($ilan['sahibinden_link'] ?? '') ?>" placeholder="https://www.sahibinden.com/ilan/...">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Isıtma</label>
                            <select class="form-select" name="isitma">
                                <?php $is = $ilan['isitma'] ?? ''; ?>
                                <option value="Doğalgaz Kombi" <?= $is=='Doğalgaz Kombi'?'selected':'' ?>>Doğalgaz Kombi</option>
                                <option value="Merkezi Pay Ölçer" <?= $is=='Merkezi Pay Ölçer'?'selected':'' ?>>Merkezi Pay Ölçer</option>
                                <option value="Klima" <?= $is=='Klima'?'selected':'' ?>>Klima</option>
                                <option value="Yerden Isıtma" <?= $is=='Yerden Isıtma'?'selected':'' ?>>Yerden Isıtma</option>
                                <option value="Soba" <?= $is=='Soba'?'selected':'' ?>>Soba</option>
                                <option value="Yok" <?= $is=='Yok'?'selected':'' ?>>Yok</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Mutfak</label>
                            <select class="form-select" name="mutfak">
                                <?php $mt = $ilan['mutfak'] ?? ''; ?>
                                <option value="Kapalı" <?= $mt=='Kapalı'?'selected':'' ?>>Kapalı</option>
                                <option value="Açık (Amerikan)" <?= $mt=='Açık (Amerikan)'?'selected':'' ?>>Açık (Amerikan)</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Balkon</label>
                            <select class="form-select" name="balkon">
                                <option value="Var" <?= ($ilan['balkon'] ?? '')=='Var'?'selected':'' ?>>Var</option>
                                <option value="Yok" <?= ($ilan['balkon'] ?? '')=='Yok'?'selected':'' ?>>Yok</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Otopark</label>
                            <select class="form-select" name="otopark">
                                <?php $ot = $ilan['otopark'] ?? ''; ?>
                                <option value="Açık" <?= $ot=='Açık'?'selected':'' ?>>Açık</option>
                                <option value="Kapalı" <?= $ot=='Kapalı'?'selected':'' ?>>Kapalı</option>
                                <option value="Yok" <?= $ot=='Yok'?'selected':'' ?>>Yok</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-danger m-0"><i class="fa-solid fa-map-location-dot me-2"></i>Konum ve Adres</h6>
                </div>
                <div class="card-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">İl</label>
                            <input type="text" class="form-control" name="il" value="<?= htmlspecialchars($ilan['il'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">İlçe</label>
                            <input type="text" class="form-control" name="ilce" value="<?= htmlspecialchars($ilan['ilce'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small">Mahalle</label>
                            <input type="text" class="form-control" name="mahalle" value="<?= htmlspecialchars($ilan['mahalle'] ?? '') ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-secondary small">Açık Adres (Konum)</label>
                            <textarea class="form-control" name="konum" rows="2"><?= htmlspecialchars($ilan['konum'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-secondary small">Harita Konumu (Google Maps iframe Kodu vb.)</label>
                            <textarea class="form-control" name="harita_konumu" rows="2"><?= htmlspecialchars($ilan['harita_konumu'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mb-4 rounded-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-warning m-0"><i class="fa-solid fa-book-open text-warning me-2"></i>Durum & Donanım</h6>
                </div>
                <div class="card-body px-4 py-4 pb-2">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Eşyalı Mı?</label>
                            <select class="form-select" name="esyali">
                                <option value="Hayır" <?= ($ilan['esyali'] ?? '')=='Hayır'?'selected':'' ?>>Hayır</option>
                                <option value="Evet" <?= ($ilan['esyali'] ?? '')=='Evet'?'selected':'' ?>>Evet</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Asansör</label>
                            <select class="form-select" name="asansor">
                                <option value="Var" <?= ($ilan['asansor'] ?? '')=='Var'?'selected':'' ?>>Var</option>
                                <option value="Yok" <?= ($ilan['asansor'] ?? '')=='Yok'?'selected':'' ?>>Yok</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Kullanım Durumu</label>
                            <select class="form-select" name="kullanim_durumu">
                                <?php $kd = $ilan['kullanim_durumu'] ?? ''; ?>
                                <option value="Boş" <?= $kd=='Boş'?'selected':'' ?>>Boş</option>
                                <option value="Kiracılı" <?= $kd=='Kiracılı'?'selected':'' ?>>Kiracılı</option>
                                <option value="Mülk Sahibi" <?= $kd=='Mülk Sahibi'?'selected':'' ?>>Mülk Sahibi</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Krediye Uygun</label>
                            <select class="form-select" name="krediye_uygun">
                                <option value="Evet" <?= ($ilan['krediye_uygun']??'')=='Evet'?'selected':'' ?>>Evet</option>
                                <option value="Hayır" <?= ($ilan['krediye_uygun']??'')=='Hayır'?'selected':'' ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Tapu Durumu</label>
                            <select class="form-select" name="tapu_durumu">
                                <?php $tp = $ilan['tapu_durumu'] ?? ''; ?>
                                <option value="Kat Mülkiyetli" <?= $tp=='Kat Mülkiyetli'?'selected':'' ?>>Kat Mülkiyetli</option>
                                <option value="Kat İrtifaklı" <?= $tp=='Kat İrtifaklı'?'selected':'' ?>>Kat İrtifaklı</option>
                                <option value="Hisseli" <?= $tp=='Hisseli'?'selected':'' ?>>Hisseli</option>
                                <option value="Arsa Tapulu" <?= $tp=='Arsa Tapulu'?'selected':'' ?>>Arsa Tapulu</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Site İçerisinde</label>
                            <select class="form-select" name="site_icerisinde">
                                <option value="Hayır" <?= ($ilan['site_icerisinde']??'')=='Hayır'?'selected':'' ?>>Hayır</option>
                                <option value="Evet" <?= ($ilan['site_icerisinde']??'')=='Evet'?'selected':'' ?>>Evet</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Site Adı</label>
                            <input type="text" class="form-control" name="site_adi" value="<?= htmlspecialchars($ilan['site_adi'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-secondary small">Aidat (₺)</label>
                            <input type="text" class="form-control price-format" name="aidat" value="<?= !empty($ilan['aidat']) ? number_format($ilan['aidat'], (floor($ilan['aidat']) == $ilan['aidat'] ? 0 : 2), ',', '.') : '' ?>">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- SAĞ FORM ALANI: MEDYA VE BUTON -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 mb-4 rounded-0 position-sticky" style="top: 90px; z-index: 10;">
                <div class="card-header bg-primary text-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0"><i class="fa-solid fa-photo-film me-2 text-warning"></i>İlan Medyası & Kayıt</h6>
                </div>
                <div class="card-body bg-light">
                    <!-- Mevcut Dosyalar -->
                    <div class="mb-4 bg-white p-3 border rounded shadow-sm">
                        <label class="form-label fw-bold text-secondary border-bottom w-100 pb-2 mb-3"><i class="fa-solid fa-images me-2"></i>Mevcut Yüklemeler</label>
                        <div class="row g-2 mb-2">
                            <?php foreach($medyalar as $m): ?>
                                <div class="col-6 position-relative">
                                    <?php if($m['medya_tipi'] === 'gorsel'): ?>
                                        <img src="uploads/images/<?= htmlspecialchars($m['dosya_yolu']) ?>" class="img-fluid rounded border <?= (($ilan['vitrin_gorseli']??'') === $m['dosya_yolu']) ? 'border-primary border-3 shadow' : '' ?>" style="height:100px; width:100%; object-fit:cover;">
                                        
                                        <?php if(($ilan['vitrin_gorseli']??'') === $m['dosya_yolu']): ?>
                                            <span class="badge bg-primary position-absolute bottom-0 start-0 m-1 shadow-sm"><i class="fa-solid fa-star"></i> Kapak</span>
                                        <?php else: ?>
                                            <a href="ilan_duzenle.php?id=<?= $id ?>&vitrin_yap=<?= urlencode($m['dosya_yolu']) ?>" class="btn btn-warning btn-sm position-absolute bottom-0 start-0 m-1 fw-bold shadow-sm" style="font-size:0.65rem; padding: 0.2rem 0.4rem;">Kapak Yap</a>
                                        <?php endif; ?>
                                        
                                    <?php elseif($m['medya_tipi'] === 'video'): ?>
                                        <div class="bg-dark text-white text-center rounded d-flex align-items-center justify-content-center" style="height:100px;">
                                            <i class="fa-solid fa-video fa-2x"></i>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars($m['dosya_yolu']) ?>" target="_blank" class="btn btn-sm btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="height:100px !important;">
                                            <i class="fa-brands fa-youtube fs-3 mb-1"></i> Link
                                        </a>
                                    <?php endif; ?>
                                    <a href="ilan_duzenle.php?id=<?= $id ?>&medya_sil=<?= $m['id'] ?>" class="btn btn-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:25px; height:25px; padding:0;"><i class="fa-solid fa-times" style="font-size:0.8rem;"></i></a>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($medyalar)): ?>
                                <div class="col-12 text-muted small text-center py-4 bg-light rounded border border-dashed" style="border-style:dashed !important;">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 text-secondary"></i><br>Henüz eklentili medya yok.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                
                    <div class="mb-4 bg-white p-3 border rounded shadow-sm">
                        <label class="form-label fw-bold text-secondary border-bottom w-100 pb-2 mb-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Yeni Medya İlave Et</label>
                        <div class="mb-3">
                            <span class="d-block small fw-bold mb-1 text-muted">Alandan Fotoğraf Seçin</span>
                            <input class="form-control form-control-sm border-primary" type="file" name="gorseller[]" multiple accept="image/*" id="gorselInput">
                            <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                        
                        <div class="mb-3 border-top pt-3">
                            <span class="d-block small fw-bold mb-1 text-muted">Video Modülü (.mp4)</span>
                            <input class="form-control form-control-sm" type="file" name="video_dosya" accept="video/*">
                        </div>

                        <div class="mb-1">
                            <span class="d-block small fw-bold mb-1 text-muted">Harici Video URL (Youtube)</span>
                            <input type="url" class="form-control form-control-sm bg-light" name="video_url" placeholder="https://...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg shadow-sm fw-bold mb-2"><i class="fa-solid fa-save me-2"></i>Tüm Değişiklikleri Kaydet</button>
                    <a href="ilanlar.php" class="btn btn-outline-secondary w-100 fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Vazgeç ve Çık</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('gorselInput').addEventListener('change', function(e) {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        if(file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail shadow-sm p-0 m-0';
                img.style.width = '60px';
                img.style.height = '60px';
                img.style.objectFit = 'cover';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
<script>
// Para Birimi Formatlayıcı (Thousands Separator)
document.querySelectorAll('.price-format').forEach(input => {
    input.addEventListener('input', function(e) {
        let val = this.value.replace(/[^\d,]/g, '');
        let parts = val.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        if (parts.length > 1) {
            this.value = parts[0] + ',' + parts[1].substring(0, 2);
        } else {
            this.value = parts[0];
        }
    });
});
</script>
