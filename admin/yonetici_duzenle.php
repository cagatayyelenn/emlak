<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: yoneticiler.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $eposta = $_POST['eposta'] ?? '';
    $profil_resmi = $_POST['current_profil_resmi'] ?? null;

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
            // Eski resmi sil
            if ($profil_resmi && file_exists($upload_dir . $profil_resmi)) {
                @unlink($upload_dir . $profil_resmi);
            }
            $profil_resmi = $name;
        }
    }

    if (!empty($ad_soyad)) {
        $stmt = $db->prepare("UPDATE portfoy_yoneticileri SET ad_soyad = ?, telefon = ?, eposta = ?, profil_resmi = ? WHERE id = ?");
        if ($stmt->execute([$ad_soyad, $telefon, $eposta, $profil_resmi, $id])) {
            header("Location: yoneticiler.php?basari=1");
            exit;
        }
    }
}

$stmt = $db->prepare("SELECT * FROM portfoy_yoneticileri WHERE id = ?");
$stmt->execute([$id]);
$yonetici = $stmt->fetch();

if (!$yonetici) {
    header("Location: yoneticiler.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark">Yöneticiyi Düzenle</h4>
            <a href="yoneticiler.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Geri Dön</a>
        </div>

        <div class="card shadow-sm border-0 rounded-0">
            <div class="card-body p-4">
                <form action="yonetici_duzenle.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="current_profil_resmi" value="<?= $yonetici['profil_resmi'] ?>">
                    
                    <div class="mb-4 text-center">
                        <label class="form-label d-block fw-bold text-secondary small mb-3">Profil Resmi</label>
                        <div class="position-relative d-inline-block">
                            <?php 
                                $avatar_url = !empty($yonetici['profil_resmi']) 
                                    ? 'uploads/yoneticiler/' . $yonetici['profil_resmi'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($yonetici['ad_soyad']) . '&background=f4f7fa&color=4361ee';
                            ?>
                            <img id="avatarPreview" src="<?= $avatar_url ?>" class="rounded-circle border shadow-sm" style="width:120px; height:120px; object-fit:cover;">
                            <label for="resimInput" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow" style="width:35px; height:35px;"><i class="fa-solid fa-camera" style="margin-top:5px;"></i></label>
                            <input type="file" class="d-none" id="resimInput" name="profil_resmi" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ad_soyad" class="form-label fw-bold text-secondary small">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="ad_soyad" name="ad_soyad" value="<?= htmlspecialchars($yonetici['ad_soyad']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefon" class="form-label fw-bold text-secondary small">Telefon</label>
                            <input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($yonetici['telefon']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eposta" class="form-label fw-bold text-secondary small">E-Posta</label>
                            <input type="email" class="form-control" id="eposta" name="eposta" value="<?= htmlspecialchars($yonetici['eposta']) ?>">
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold shadow-sm text-white"><i class="fa-solid fa-save me-2"></i> Bilgileri Güncelle</button>
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
