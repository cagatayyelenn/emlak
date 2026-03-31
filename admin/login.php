<?php
require_once 'includes/database.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
    $sifre = $_POST['sifre'] ?? '';

    if (!empty($kullanici_adi) && !empty($sifre)) {
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$kullanici_adi]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($sifre, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_isim'] = $admin['username'];
            header("Location: index.php");
            exit;
        } else {
            $hata = 'Hatalı kullanıcı adı veya şifre!';
        }
    } else {
        $hata = 'Lütfen tüm alanları doldurun.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - Maxwell Emlak Ofisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-box { width: 100%; max-width: 400px; padding: 40px; background: white; border-radius: 0px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="login-box mx-3">
    <div class="text-center mb-4">
        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
            <i class="fa-solid fa-house-user fa-2x"></i>
        </div>
        <h4 class="fw-bold text-dark">Maxwell Emlak Ofisi</h4>
        <p class="text-muted small">Lütfen yönetici bilgilerinizi girin.</p>
    </div>
    
    <?php if ($hata): ?>
        <div class="alert alert-danger text-center"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $hata ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label fw-bold text-secondary">Kullanıcı Adı</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" name="kullanici_adi" class="form-control border-start-0" required autofocus placeholder="örn: admin">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold text-secondary">Şifre</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" name="sifre" class="form-control border-start-0" required placeholder="••••••">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fs-5 fw-bold shadow-sm"><i class="fa-solid fa-right-to-bracket me-1"></i> Giriş Yap</button>
    </form>
</div>

</body>
</html>
