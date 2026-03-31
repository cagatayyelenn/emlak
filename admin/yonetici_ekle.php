<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $eposta = $_POST['eposta'] ?? '';
    $profil_resmi = null;

    // Resim Yükleme İşlemi
    if (isset($_FILES['profil_resmi']) && $_FILES['profil_resmi']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/yoneticiler/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp = $_FILES['profil_resmi']['tmp_name'];
        $ext = pathinfo($_FILES['profil_resmi']['name'], PATHINFO_EXTENSION);
        $name = time() . '_' . rand(100, 999) . '.' . $ext;
        
        if (move_uploaded_file($tmp, $upload_dir . $name)) {
            $profil_resmi = $name;
        }
    }

    if (!empty($ad_soyad)) {
        $stmt = $db->prepare("INSERT INTO portfoy_yoneticileri (ad_soyad, telefon, eposta, profil_resmi) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$ad_soyad, $telefon, $eposta, $profil_resmi])) {
            header("Location: yoneticiler.php?basari=1");
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-12">
        <h4 class="mb-1 text-dark fw-bold">Yeni Danışman Ekle</h4>
        <p class="text-muted small">Portföylerinizle ilgilenecek yeni bir gayrimenkul danışmanı tanımlayın.</p>
    </div>
</div>

<form action="yonetici_ekle.php" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-user-plus me-2"></i> Danışman Bilgileri</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="ad_soyad" class="form-label fw-bold text-secondary small">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="ad_soyad" name="ad_soyad" placeholder="Örn: Ahmet Yılmaz" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefon" class="form-label fw-bold text-secondary small">Telefon</label>
                            <input type="text" class="form-control" id="telefon" name="telefon" placeholder="05XX XXX XX XX">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eposta" class="form-label fw-bold text-secondary small">E-Posta</label>
                            <input type="email" class="form-control" id="eposta" name="eposta" placeholder="örnek@mail.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image me-2"></i> Profil Resmi</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3 p-3 bg-light rounded border d-flex align-items-center justify-content-center" style="min-height:150px;">
                        <span class="text-muted small italic">Resim seçilmedi</span>
                    </div>
                    <input type="file" class="form-control form-control-sm" name="profil_resmi" accept="image/*">
                    <p class="text-muted mt-2 small">En iyi görünüm için kare resim kullanın.</p>
                </div>
            </div>
            
            <div class="d-grid shadow-sm mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fa-solid fa-save me-2"></i> Danışmanı Kaydet</button>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
