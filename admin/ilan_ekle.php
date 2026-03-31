<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

$dirs = ['uploads', 'uploads/images', 'uploads/videos'];
foreach($dirs as $dir) {
    if(!is_dir(__DIR__ . '/' . $dir)) mkdir(__DIR__ . '/' . $dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['baslik', 'durumu', 'aciklama', 'fiyat', 'portfoy_yoneticisi_id', 'il', 'ilce', 'mahalle', 'ilan_no', 'ilan_tarihi', 'emlak_tipi', 'm2_brut', 'm2_net', 'oda_sayisi', 'bina_yasi', 'bulundugu_kat', 'kat_sayisi', 'isitma', 'banyo_sayisi', 'mutfak', 'balkon', 'asansor', 'otopark', 'esyali', 'kullanim_durumu', 'site_icerisinde', 'site_adi', 'aidat', 'krediye_uygun', 'tapu_durumu', 'konum', 'harita_konumu'];
    
    $vals = [];
    foreach($fields as $f) {
        $val = $_POST[$f] ?? null;
        if($val === '') $val = null;
        $vals[$f] = $val;
    }

    // Para formatını (örn: 1.450.000,50) veritabanına hazır hale gertir (1450000.50)
    foreach(['fiyat', 'aidat'] as $pa) {
        if(isset($vals[$pa]) && $vals[$pa] !== null) {
            $temiz = str_replace('.', '', $vals[$pa]);
            $temiz = str_replace(',', '.', $temiz);
            $vals[$pa] = (float) $temiz;
        }
    }

    if (!empty($vals['baslik'])) {
        try {
            $columns = implode(', ', $fields);
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            
            $stmt = $db->prepare("INSERT INTO ilanlar ($columns) VALUES ($placeholders)");
            $stmt->execute(array_values($vals));
            $ilan_id = $db->lastInsertId();

            $ana_gorsel_isim = $_POST['ana_gorsel_isim'] ?? '';
            $vitrin_gorseli_saved = null;

            // Görselleri kaydet
            if (isset($_FILES['gorseller']) && !empty($_FILES['gorseller']['name'][0])) {
                $count = count($_FILES['gorseller']['name']);
                for ($i=0; $i<$count; $i++) {
                    if($_FILES['gorseller']['error'][$i] === UPLOAD_ERR_OK) {
                        $orijinal_isim = $_FILES['gorseller']['name'][$i];
                        $tmp = $_FILES['gorseller']['tmp_name'][$i];
                        $name = time() . '_' . rand(100,999) . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($orijinal_isim));
                        
                        if (move_uploaded_file($tmp, __DIR__ . '/uploads/images/' . $name)) {
                            $mstmt = $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'gorsel', ?)");
                            $mstmt->execute([$ilan_id, $name]);
                            
                            // Ana görsel yakalandı!
                            if ($orijinal_isim === $ana_gorsel_isim && !$vitrin_gorseli_saved) {
                                $vitrin_gorseli_saved = $name;
                            }
                        }
                    }
                }
            }
            
            // Vitrin görselini ilan tablosuna işle
            if ($vitrin_gorseli_saved) {
                 $db->prepare("UPDATE ilanlar SET vitrin_gorseli = ? WHERE id = ?")->execute([$vitrin_gorseli_saved, $ilan_id]);
            }

            // Video İşlemleri
            if (isset($_FILES['video_dosya']) && $_FILES['video_dosya']['error'] === UPLOAD_ERR_OK) {
                $vtmp = $_FILES['video_dosya']['tmp_name'];
                $vname = time() . '_' . rand(100,999) . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES['video_dosya']['name']));
                if (move_uploaded_file($vtmp, __DIR__ . '/uploads/videos/' . $vname)) {
                    $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'video', ?)")->execute([$ilan_id, $vname]);
                }
            } elseif (!empty($_POST['video_url'])) { 
                $db->prepare("INSERT INTO ilan_medya (ilan_id, medya_tipi, dosya_yolu) VALUES (?, 'video_url', ?)")->execute([$ilan_id, $_POST['video_url']]);
            }

            header("Location: ilanlar.php?basari=1");
            exit;
            
        } catch (PDOException $e) {
            die("<div style='padding:50px; font-family:sans-serif; text-align:center;'>
                <h1 style='color:red;'>SQL Kayıt Hatası (500 Error Engellendi)</h1>
                <p>Veritabanı işlemi sırasında bir hata oluştu. Muhtemelen yeni eklenen veritabanı sütunları (örn: durumu, vitrin_gorseli) canlı sunucudaki SQLite dosyanızda bulunmuyor.</p>
                <div style='background:#f4f4f4; padding:20px; border-radius: 0px; margin-top:20px; text-align:left;'>
                    <strong>Hata Kod Çıktısı:</strong><br><br>
                    <code>" . htmlspecialchars($e->getMessage()) . "</code>
                </div>
                <p style='margin-top:30px;'>Bu sorunu çözmek için lütfen adres çubuğunuza <br><b>seninsiteniz.com/update_db_stepper.php</b> yazıp tek seferlik çalıştırın ve tekrar deneyin.</p>
            </div>");
        }
    }
}

$yoneticiler = $db->query("SELECT * FROM portfoy_yoneticileri ORDER BY ad_soyad ASC")->fetchAll();
require_once 'includes/header.php';
?>

<!-- BS Stepper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">
<style>
/* Flash Able uyumlu modern stepper tasarımı */
.bs-stepper .step-trigger { padding: 20px; text-decoration: none !important; }
.bs-stepper .bs-stepper-circle { background-color: #e9ecef; color: #6c757d; width: 40px; height: 40px; }
.bs-stepper .active .bs-stepper-circle { background-color: #4361ee; color: #fff; box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3); }
.bs-stepper .active .bs-stepper-label { font-weight: 700; color: #4361ee; }
.bs-stepper .step-trigger:hover { background-color: transparent !important; }
.stepper-content-box { min-height: 400px; }
</style>

<div class="row align-items-center mb-4">
    <div class="col-md-12 d-flex justify-content-between">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Yeni İlan Sihirbazı</h4>
            <span class="text-muted small">Adım adım eksiksiz emlak girişi</span>
        </div>
        <a href="ilanlar.php" class="btn btn-outline-secondary btn-sm fw-bold align-self-start"><i class="fa-solid fa-arrow-left me-1"></i> İptal</a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5 rounded-0">
    <div class="card-body p-0">
        <div id="ilanStepper" class="bs-stepper">
            <div class="bs-stepper-header border-bottom bg-light" role="tablist">
                <div class="step" data-target="#step-1">
                    <button type="button" class="step-trigger" role="tab" aria-controls="step-1" id="step1-trigger">
                        <span class="bs-stepper-circle">1</span>
                        <span class="bs-stepper-label d-none d-md-inline-block">Temel Bilgiler</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#step-2">
                    <button type="button" class="step-trigger" role="tab" aria-controls="step-2" id="step2-trigger" disabled>
                        <span class="bs-stepper-circle">2</span>
                        <span class="bs-stepper-label d-none d-md-inline-block">Emlak Özellikleri</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#step-3">
                    <button type="button" class="step-trigger" role="tab" aria-controls="step-3" id="step3-trigger" disabled>
                        <span class="bs-stepper-circle">3</span>
                        <span class="bs-stepper-label d-none d-md-inline-block">Harita & Açıklama</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#step-4">
                    <button type="button" class="step-trigger" role="tab" aria-controls="step-4" id="step4-trigger" disabled>
                        <span class="bs-stepper-circle">4</span>
                        <span class="bs-stepper-label d-none d-md-inline-block">Medya & Kayıt</span>
                    </button>
                </div>
            </div>

            <div class="bs-stepper-content p-4 p-md-5 stepper-content-box">
                <form id="ilanEkleForm" action="ilan_ekle.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                    
                    <!-- ADIM 1 -->
                    <div id="step-1" class="content" role="tabpanel" aria-labelledby="step1-trigger">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-info-circle me-2"></i>Aşama 1: Temel Konum ve Fiyatlandırma</h5>
                        <div class="row">
                            <div class="col-md-9 mb-4">
                                <label class="form-label fw-bold text-secondary small">İlan Başlığı <span class="text-danger">*</span></label>
                                <input type="text" id="baslikInput" class="form-control form-control-lg" name="baslik" placeholder="Örn: Deniz Manzaralı Lüks 3+1">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label fw-bold text-secondary small">Durumu <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg border-primary" name="durumu">
                                    <option value="Satılık">Satılık</option>
                                    <option value="Kiralık">Kiralık</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">Fiyat (₺) <span class="text-danger">*</span></label>
                                <input type="text" id="fiyatInput" class="form-control price-format" name="fiyat" placeholder="Örn: 3.500.000">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">İl <span class="text-danger">*</span></label>
                                <input type="text" id="ilInput" class="form-control" name="il">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">İlçe <span class="text-danger">*</span></label>
                                <input type="text" id="ilceInput" class="form-control" name="ilce">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">Mahalle</label>
                                <input type="text" class="form-control" name="mahalle">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">Emlak Tipi</label>
                                <select class="form-select" name="emlak_tipi">
                                    <option value="Daire">Daire</option>
                                    <option value="Müstakil Ev">Müstakil Ev</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Arsa">Arsa</option>
                                    <option value="İşyeri">İşyeri</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold text-secondary small">Portföy Yöneticisi</label>
                                <select class="form-select" name="portfoy_yoneticisi_id">
                                    <option value="">-- Atanmadı --</option>
                                    <?php foreach($yoneticiler as $y): ?>
                                        <option value="<?= $y['id'] ?>"><?= htmlspecialchars($y['ad_soyad']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-primary px-5 fw-bold btn-lg" onclick="validateStep1()">İleri <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- ADIM 2 -->
                    <div id="step-2" class="content" role="tabpanel" aria-labelledby="step2-trigger">
                        <h5 class="fw-bold mb-4 text-success"><i class="fa-solid fa-house-chimney me-2"></i>Aşama 2: Fiziksel Özellikler ve Donanım</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Oda Sayısı</label>
                                <select class="form-select" name="oda_sayisi">
                                    <option value="Stüdyo (1+0)">Stüdyo (1+0)</option>
                                    <option value="1+1">1+1</option>
                                    <option value="2+1" selected>2+1</option>
                                    <option value="3+1">3+1</option>
                                    <option value="4+1">4+1</option>
                                    <option value="5+1 ve üzeri">5+1 ve üzeri</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">m² (Brüt)</label>
                                <input type="number" class="form-control" name="m2_brut">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">m² (Net)</label>
                                <input type="number" class="form-control" name="m2_net">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Bina Yaşı</label>
                                <select class="form-select" name="bina_yasi">
                                    <option value="0">0 (Yeni)</option>
                                    <option value="1-5">1-5 Arası</option>
                                    <option value="6-10">6-10 Arası</option>
                                    <option value="11-15">11-15 Arası</option>
                                    <option value="16-20">16-20 Arası</option>
                                    <option value="21+">21 ve Üzeri</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Bulunduğu Kat</label>
                                <input type="text" class="form-control" name="bulundugu_kat" placeholder="Örn: 3">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Kat Sayısı</label>
                                <input type="number" class="form-control" name="kat_sayisi">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Isıtma</label>
                                <select class="form-select" name="isitma">
                                    <option value="Doğalgaz Kombi">Doğalgaz Kombi</option>
                                    <option value="Merkezi Pay Ölçer">Merkezi Pay Ölçer</option>
                                    <option value="Klima">Klima</option>
                                    <option value="Yerden Isıtma">Yerden Isıtma</option>
                                    <option value="Soba">Soba</option>
                                    <option value="Yok">Yok</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Banyo Sayısı</label>
                                <input type="number" class="form-control" name="banyo_sayisi" value="1">
                            </div>
                            <!-- Ek Özellikler -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Asansör</label>
                                <select class="form-select" name="asansor"><option value="Var">Var</option><option value="Yok">Yok</option></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Balkon</label>
                                <select class="form-select" name="balkon"><option value="Var">Var</option><option value="Yok">Yok</option></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Eşyalı Mı?</label>
                                <select class="form-select" name="esyali"><option value="Hayır">Hayır</option><option value="Evet">Evet</option></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Tesis Durumu</label>
                                <select class="form-select" name="site_icerisinde"><option value="Hayır">Bireysel Bina</option><option value="Evet">Site İçerisinde</option></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Aidat (₺)</label>
                                <input type="text" class="form-control price-format" name="aidat" placeholder="Örn: 1.500">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Krediye Uygun</label>
                                <select class="form-select" name="krediye_uygun"><option value="Evet">Evet</option><option value="Hayır">Hayır</option></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-secondary small">Tapu Durumu</label>
                                <select class="form-select" name="tapu_durumu">
                                    <option value="Kat Mülkiyetli">Kat Mülkiyetli</option><option value="Kat İrtifaklı">Kat İrtifaklı</option><option value="Arsa Tapulu">Arsa Tapulu</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary fw-bold btn-lg" onclick="stepper.previous()"><i class="fa-solid fa-arrow-left me-2"></i> Geri Dön</button>
                            <button type="button" class="btn btn-primary px-5 fw-bold btn-lg" onclick="stepper.next()">İleri <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- ADIM 3 -->
                    <div id="step-3" class="content" role="tabpanel" aria-labelledby="step3-trigger">
                        <h5 class="fw-bold mb-4 text-warning"><i class="fa-solid fa-map-location-dot me-2"></i>Aşama 3: Açıklama ve Harita Konumu</h5>
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold text-secondary">Açık Adres (Navigasyon için)</label>
                                <textarea class="form-control" name="konum" rows="2" placeholder="Sokak, Cadde, Bina No..."></textarea>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold text-secondary">Google Maps Harita Kodu (iframe)</label>
                                <textarea class="form-control text-muted bg-light" name="harita_konumu" rows="2" placeholder="<iframe src='...'></iframe>"></textarea>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold text-secondary">İlan Temsili Açıklaması <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="aciklamaInput" name="aciklama" rows="5" placeholder="İlan ile ilgili potansiyel müşterilere sunacağınız detaylar..."></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary fw-bold btn-lg" onclick="stepper.previous()"><i class="fa-solid fa-arrow-left me-2"></i> Geri Dön</button>
                            <button type="button" class="btn btn-primary px-5 fw-bold btn-lg" onclick="stepper.next()">İleri <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- ADIM 4 -->
                    <div id="step-4" class="content" role="tabpanel" aria-labelledby="step4-trigger">
                        <h5 class="fw-bold mb-4 text-info"><i class="fa-solid fa-photo-film me-2"></i>Aşama 4: Harika Görseller Yükleyin</h5>
                        
                        <div class="alert alert-primary shadow-sm border-0 border-start border-primary border-4 mb-4">
                            <strong><i class="fa-solid fa-star text-warning"></i> İpucu:</strong> Gelişmiş medya yöneticisi ile çoklu resim seçebilir, üstlerindeki "Vitrin Yap" butonuna basarak ilk görünecek resmi belirleyebilirsiniz!
                        </div>

                        <!-- Gizli Alanlar -->
                        <input type="hidden" name="ana_gorsel_isim" id="anaGorselIsim">
                        <input type="file" name="gorseller[]" id="realGorselInput" multiple style="display:none;">

                        <div class="row">
                            <div class="col-lg-8 border-end pe-4">
                                <label class="form-label fw-bold fs-5 text-dark mb-3">Fotoğraflar Ekle</label>
                                <div class="border border-2 border-dashed rounded p-4 text-center bg-light" style="border-style: dashed !important; border-color: #adb5bd !important;">
                                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-2"></i>
                                    <h5>Sürükle bırak veya bilgisayardan seç</h5>
                                    <button type="button" class="btn btn-dark mt-2" onclick="document.getElementById('fakeGorselInput').click()"><i class="fa-solid fa-folder-open me-2"></i> Resimleri Seç</button>
                                    <input type="file" id="fakeGorselInput" multiple accept="image/*" style="display:none;">
                                </div>
                                
                                <!-- Önizleme Alanı -->
                                <div id="previewContainer" class="d-flex flex-wrap gap-3 mt-4"></div>
                            </div>
                            
                            <div class="col-lg-4 ps-4">
                                <label class="form-label fw-bold fs-5 text-dark mb-3">Video Ekle (Opsiyonel)</label>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Video Dosyası (.mp4)</label>
                                    <input class="form-control" type="file" name="video_dosya" accept="video/*">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small text-center w-100 border-bottom pb-2">Veya Video Linki Kullan</label>
                                    <input type="url" class="form-control bg-light" name="video_url" placeholder="YouTube, Vimeo vb.">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                            <button type="button" class="btn btn-outline-secondary fw-bold btn-lg" onclick="stepper.previous()"><i class="fa-solid fa-arrow-left me-2"></i> Geri Dön</button>
                            <button type="submit" class="btn btn-success px-5 fw-bold btn-lg shadow-sm" style="font-size:1.2rem;"><i class="fa-solid fa-rocket me-2"></i> İlanı Yayına Al</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 (Kayıt Sonrası Gösterim İçin) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- BS Stepper JS -->
<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
<script>
// Stepper Init
var stepperElem = document.querySelector('#ilanStepper');
var stepper = new Stepper(stepperElem, {
    linear: true,
    animation: true
});

// Adım 1 Basit Form Doğrulaması
function validateStep1() {
    let baslik = document.getElementById('baslikInput').value;
    let fiyat = document.getElementById('fiyatInput').value;
    let il = document.getElementById('ilInput').value;
    let ilce = document.getElementById('ilceInput').value;
    if(baslik=='' || fiyat=='' || il=='' || ilce=='') {
        Swal.fire('Eksik Bilgi', 'Lütfen yıldızlı (*) alanları doldurun.', 'warning');
    } else {
        stepper.next();
    }
}

// Adım 3 Form Doğrulaması
function validateStep3() {
    let aciklama = document.getElementById('aciklamaInput').value;
    if(aciklama.trim() == '') {
        Swal.fire('Eksik Bilgi', 'Lütfen İlan Açıklaması alanını doldurun.', 'warning');
    } else {
        stepper.next();
    }
}

// Son Form Gönderme Doğrulaması
function validateForm(e) {
    let baslik = document.getElementById('baslikInput').value;
    let fiyat = document.getElementById('fiyatInput').value;
    
    if(baslik=='' || fiyat=='') {
        e.preventDefault();
        Swal.fire('Hata!', 'Zorunlu alanlar eksik. Lütfen geri dönüp bilgileri kontrol edin.', 'error');
        return false;
    }
    return true;
}

// Para Birimi Formatlayıcı (Thousands Separator)
document.querySelectorAll('.price-format').forEach(input => {
    input.addEventListener('input', function(e) {
        let val = this.value.replace(/[^\d,]/g, ''); // Sayı ve virgül dışındakileri sil
        let parts = val.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        if (parts.length > 1) {
            this.value = parts[0] + ',' + parts[1].substring(0, 2);
        } else {
            this.value = parts[0];
        }
    });
});

// Gelişmiş Medya & Vitrin Seçici Algoritması
let dt = new DataTransfer();
let anaGorselInput = document.getElementById('anaGorselIsim');

document.getElementById('fakeGorselInput').addEventListener('change', function(e) {
    for(let file of this.files) {
        dt.items.add(file);
    }
    syncAndRender();
    this.value = ''; // Input'u temizle
});

function syncAndRender() {
    // Hidden Input'a kopyala
    document.getElementById('realGorselInput').files = dt.files;
    
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    // Geçerli bir ana görsel var mı?
    let currentMain = anaGorselInput.value;
    let mainFound = false;
    for (let i = 0; i < dt.files.length; i++) {
        if (dt.files[i].name === currentMain) mainFound = true;
    }
    // Yoksa ilk yükleneni ana görsel yap
    if (!mainFound && dt.files.length > 0) {
        anaGorselInput.value = dt.files[0].name;
    }
    currentMain = anaGorselInput.value;

    // Resimleri Çizdir
    for (let i = 0; i < dt.files.length; i++) {
        let file = dt.files[i];
        let reader = new FileReader();
        
        let wrapper = document.createElement('div');
        let cardStyle = file.name === currentMain ? 'border-success border-3 scale-up shadow' : 'border-muted border shadow-sm';
        wrapper.className = `card p-1 text-center bg-white ${cardStyle}`;
        wrapper.style.width = '140px';
        wrapper.style.transition = 'all 0.2s';
        
        let img = document.createElement('img');
        img.className = 'card-img-top rounded mb-2';
        img.style.height = '100px';
        img.style.objectFit = 'cover';
        
        // Vitrin Badge
        let badge = document.createElement('span');
        badge.className = 'badge bg-success position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm';
        badge.innerHTML = '<i class="fa-solid fa-star"></i> Vitrin';
        
        // Kaldır Butonu
        let btnRemove = document.createElement('button');
        btnRemove.className = 'btn btn-outline-danger btn-sm mb-1 fw-bold';
        btnRemove.innerHTML = '<i class="fa-solid fa-trash"></i> Sil';
        btnRemove.type = 'button';
        btnRemove.onclick = function() {
            dt.items.remove(i);
            syncAndRender();
        };

        // Vitrin Yap Butonu
        let btnMakeMain = document.createElement('button');
        btnMakeMain.className = 'btn btn-primary btn-sm fw-bold border-0';
        btnMakeMain.style.backgroundColor = '#4361ee';
        btnMakeMain.innerHTML = 'Vitrin Yap';
        btnMakeMain.type = 'button';
        btnMakeMain.onclick = function() {
            anaGorselInput.value = file.name;
            syncAndRender();
        };

        reader.onload = function(e) {
            img.src = e.target.result;
            wrapper.appendChild(img);
            if(file.name === currentMain) {
                wrapper.appendChild(badge);
                wrapper.appendChild(btnRemove); // Sadece sil butonu
            } else {
                wrapper.appendChild(btnMakeMain);
                wrapper.appendChild(btnRemove);
            }
            container.appendChild(wrapper);
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
