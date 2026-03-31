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

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark">Yeni Yönetici Ekle</h4>
            <a href="yoneticiler.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Geri Dön</a>
        </div>

        <div class="card shadow-sm border-0 rounded-0">
            <div class="card-body p-4">
                <form action="yonetici_ekle.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                        <label for="resimInput" class="form-label d-block fw-bold text-secondary small mb-3">Profil Resmi</label>
                        <div class="position-relative d-inline-block">
                            <img id="avatarPreview" src="https://ui-avatars.com/api/?name=Admin&background=f4f7fa&color=4361ee" class="rounded-circle border shadow-sm" style="width:120px; height:120px; object-fit:cover;">
                            <label for="resimInput" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow" style="width:35px; height:35px;"><i class="fa-solid fa-camera" style="margin-top:5px;"></i></label>
                            <input type="file" class="d-none" id="resimInput" name="profil_resmi" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>

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
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i> Yöneticiyi Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
