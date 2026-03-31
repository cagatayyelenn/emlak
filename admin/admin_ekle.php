<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Kullanıcı adı kontrolü
        $check = $db->prepare("SELECT id FROM admins WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = "Bu kullanıcı adı zaten alınmış.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO admins (username, ad_soyad, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $ad_soyad, $hash]);
            header("Location: adminler.php?basari=1");
            exit;
        }
    } else {
        $error = "Lütfen tüm zorunlu alanları doldurun.";
    }
}

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-1 text-dark fw-bold">Yeni Yönetici Ekle</h4>
        <p class="text-muted small">Sistemi yönetmesi için yeni bir yönetici tanımlayın.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="adminler.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Geri Dön</a>
    </div>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?>
</div>
<?php endif; ?>

<form action="admin_ekle.php" method="POST">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-user-shield me-2"></i> Yönetici Bilgileri</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Ad Soyad</label>
                        <input type="text" class="form-control" name="ad_soyad" required placeholder="Yöneticinin tam ismi">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">Kullanıcı Adı</label>
                            <input type="text" class="form-control" name="username" required placeholder="admin_kullanici">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">Şifre</label>
                            <input type="password" class="form-control" name="password" required placeholder="******">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid shadow-sm">
                <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fa-solid fa-save me-2"></i> Yöneticiyi Kaydet</button>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
