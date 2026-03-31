<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Ayarlar dizini kontrolü
$settings_dir = __DIR__ . '/uploads/settings';
if (!is_dir($settings_dir)) {
    mkdir($settings_dir, 0777, true);
}

// Mevcut ayarları çek
try {
    $stmt = $db->query("SELECT * FROM site_ayarlari LIMIT 1");
    $ayarlar = $stmt->fetch();
} catch (PDOException $e) {
    // Tablo henüz oluşturulmamış olabilir
    $ayarlar = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $site_baslik = $_POST['site_baslik'] ?? '';
        $site_aciklama = $_POST['site_aciklama'] ?? '';
        $site_anahtar_kelimeler = $_POST['site_anahtar_kelimeler'] ?? '';
        $google_analytics = $_POST['google_analytics'] ?? '';
        $google_search_console = $_POST['google_search_console'] ?? '';
        $iletisim_telefon = $_POST['iletisim_telefon'] ?? '';
        $iletisim_eposta = $_POST['iletisim_eposta'] ?? '';
        $iletisim_adres = $_POST['iletisim_adres'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $twitter = $_POST['twitter'] ?? '';
        $linkedin = $_POST['linkedin'] ?? '';

        $logo = $ayarlar['logo'] ?? '';
        $favicon = $ayarlar['favicon'] ?? '';

        // Logo Yükleme
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $new_logo_name = 'logo_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $settings_dir . '/' . $new_logo_name)) {
                if ($logo && file_exists($settings_dir . '/' . $logo)) {
                    @unlink($settings_dir . '/' . $logo);
                }
                $logo = $new_logo_name;
            }
        }

        // Favicon Yükleme
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
            $new_favicon_name = 'favicon_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $settings_dir . '/' . $new_favicon_name)) {
                if ($favicon && file_exists($settings_dir . '/' . $favicon)) {
                    @unlink($settings_dir . '/' . $favicon);
                }
                $favicon = $new_favicon_name;
            }
        }

        if ($ayarlar) {
            $update = $db->prepare("UPDATE site_ayarlari SET 
                site_baslik = ?, 
                site_aciklama = ?, 
                site_anahtar_kelimeler = ?, 
                logo = ?, 
                favicon = ?, 
                google_analytics = ?, 
                google_search_console = ?, 
                iletisim_telefon = ?, 
                iletisim_eposta = ?, 
                iletisim_adres = ?, 
                facebook = ?, 
                instagram = ?, 
                twitter = ?, 
                linkedin = ?
                WHERE id = ?");
            $update->execute([
                $site_baslik, $site_aciklama, $site_anahtar_kelimeler, 
                $logo, $favicon, $google_analytics, $google_search_console, 
                $iletisim_telefon, $iletisim_eposta, $iletisim_adres, 
                $facebook, $instagram, $twitter, $linkedin, 
                $ayarlar['id']
            ]);
        } else {
            $insert = $db->prepare("INSERT INTO site_ayarlari (
                site_baslik, site_aciklama, site_anahtar_kelimeler, 
                logo, favicon, google_analytics, google_search_console, 
                iletisim_telefon, iletisim_eposta, iletisim_adres, 
                facebook, instagram, twitter, linkedin
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $site_baslik, $site_aciklama, $site_anahtar_kelimeler, 
                $logo, $favicon, $google_analytics, $google_search_console, 
                $iletisim_telefon, $iletisim_eposta, $iletisim_adres, 
                $facebook, $instagram, $twitter, $linkedin
            ]);
        }

        header("Location: ayarlar.php?basari=1");
        exit;
    } catch (Exception $e) {
        $error_msg = "Ayar Güncelleme Hatası: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-12">
        <h4 class="mb-1 text-dark fw-bold">Genel Site Ayarları</h4>
        <p class="text-muted small">Sitenin SEO, marka ve iletişim bilgilerini buradan yönetebilirsiniz.</p>
    </div>
</div>

<?php if (isset($error_msg)): ?>
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error_msg ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['basari'])): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <i class="fa-solid fa-check-circle me-2"></i> Ayarlar başarıyla güncellendi!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<form action="ayarlar.php" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Sol Kolon: SEO ve Marka -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-search me-2"></i> SEO ve Genel Bilgiler</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Site Başlığı (Title)</label>
                        <input type="text" class="form-control" name="site_baslik" value="<?= htmlspecialchars($ayarlar['site_baslik'] ?? '') ?>" placeholder="Örn: Maxwell Emlak - Güvenilir Gayrimenkul">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Site Açıklaması (Description)</label>
                        <textarea class="form-control" name="site_aciklama" rows="3" placeholder="Siteniz hakkında kısa bir açıklama..."><?= htmlspecialchars($ayarlar['site_aciklama'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Anahtar Kelimeler (Keywords)</label>
                        <textarea class="form-control" name="site_anahtar_kelimeler" rows="2" placeholder="emlak, kiralık, satılık..."><?= htmlspecialchars($ayarlar['site_anahtar_kelimeler'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-success"><i class="fa-solid fa-code me-2"></i> Takip Kodları (Head/Body)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Google Analytics (UA / G-...) <i class="fa-solid fa-question-circle ms-1" title="Global Site Tag (gtag.js) kodunu buraya yapıştırın."></i></label>
                        <textarea class="form-control font-monospace text-muted" name="google_analytics" rows="4" style="font-size:0.85rem;"><?= htmlspecialchars($ayarlar['google_analytics'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-secondary small">Google Search Console (Meta Tag)</label>
                        <textarea class="form-control font-monospace text-muted" name="google_search_console" rows="2" style="font-size:0.85rem;"><?= htmlspecialchars($ayarlar['google_search_console'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-share-nodes me-2"></i> Sosyal Medya Linkleri</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-brands fa-facebook-f me-1"></i> Facebook</label>
                            <input type="url" class="form-control" name="facebook" value="<?= htmlspecialchars($ayarlar['facebook'] ?? '') ?>" placeholder="https://facebook.com/kullanici">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-brands fa-instagram me-1"></i> Instagram</label>
                            <input type="url" class="form-control" name="instagram" value="<?= htmlspecialchars($ayarlar['instagram'] ?? '') ?>" placeholder="https://instagram.com/kullanici">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-brands fa-twitter me-1"></i> Twitter (X)</label>
                            <input type="url" class="form-control" name="twitter" value="<?= htmlspecialchars($ayarlar['twitter'] ?? '') ?>" placeholder="https://twitter.com/kullanici">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-brands fa-linkedin-in me-1"></i> LinkedIn</label>
                            <input type="url" class="form-control" name="linkedin" value="<?= htmlspecialchars($ayarlar['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/kullanici">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Marka ve İletişim -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-palette me-2"></i> Marka Varlıkları</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-secondary small d-block mb-3">Site Logosu</label>
                        <div class="mb-3 p-3 bg-light rounded border d-flex align-items-center justify-content-center" style="min-height:100px;">
                            <?php if ($ayarlar['logo'] && file_exists($settings_dir . '/' . $ayarlar['logo'])): ?>
                                <img src="uploads/settings/<?= $ayarlar['logo'] ?>" alt="Logo" style="max-height:80px; max-width:100%;">
                            <?php else: ?>
                                <span class="text-muted small italic">Logo yüklenmemiş</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="logo" accept="image/*">
                    </div>
                    
                    <hr class="text-secondary opacity-25">

                    <div class="mb-0 text-center">
                        <label class="form-label fw-bold text-secondary small d-block mb-3">Favicon (32x32)</label>
                        <div class="mb-3 d-flex align-items-center justify-content-center">
                            <?php if ($ayarlar['favicon'] && file_exists($settings_dir . '/' . $ayarlar['favicon'])): ?>
                                <img src="uploads/settings/<?= $ayarlar['favicon'] ?>" alt="Favicon" style="width:32px; height:32px;">
                            <?php else: ?>
                                <div class="bg-light border rounded" style="width:32px; height:32px;"></div>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="favicon" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-address-book me-2"></i> İletişim Bilgileri</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Telefon Numarası</label>
                        <input type="text" class="form-control" name="iletisim_telefon" value="<?= htmlspecialchars($ayarlar['iletisim_telefon'] ?? '') ?>" placeholder="+90 5xx ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">E-posta Adresi</label>
                        <input type="email" class="form-control" name="iletisim_eposta" value="<?= htmlspecialchars($ayarlar['iletisim_eposta'] ?? '') ?>" placeholder="info@emlak.com">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-secondary small">Fiziksel Adres</label>
                        <textarea class="form-control" name="iletisim_adres" rows="3" placeholder="Adres detayları..."><?= htmlspecialchars($ayarlar['iletisim_adres'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold"><i class="fa-solid fa-save me-2"></i> Değişiklikleri Kaydet</button>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
