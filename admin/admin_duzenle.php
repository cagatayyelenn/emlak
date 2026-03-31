<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    header("Location: adminler.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kullanıcı adı kontrolü
    $check = $db->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
    $check->execute([$username, $id]);
    if ($check->fetch()) {
        $error = "Bu kullanıcı adı zaten başka bir yönetici tarafından kullanılıyor.";
    } else {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE admins SET username = ?, ad_soyad = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $ad_soyad, $hash, $id]);
        } else {
            $stmt = $db->prepare("UPDATE admins SET username = ?, ad_soyad = ? WHERE id = ?");
            $stmt->execute([$username, $ad_soyad, $id]);
        }
        header("Location: adminler.php?basari=1");
        exit;
    }
}

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-1 text-dark fw-bold">Yönetici Düzenle</h4>
        <p class="text-muted small"><?= htmlspecialchars($admin['username']) ?> bilgilerini güncelleyin.</p>
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

<form action="admin_duzenle.php?id=<?= $id ?>" method="POST">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-user-shield me-2"></i> Yönetici Bilgileri</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Ad Soyad</label>
                        <input type="text" class="form-control" name="ad_soyad" value="<?= htmlspecialchars($admin['ad_soyad'] ?? '') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">Kullanıcı Adı</label>
                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                            <input type="password" class="form-control" name="password" placeholder="******">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid shadow-sm">
                <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fa-solid fa-save me-2"></i> Değişiklikleri Kaydet</button>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
