<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Önce resmi bul ve sil
    $resim_stmt = $db->prepare("SELECT profil_resmi FROM portfoy_yoneticileri WHERE id = ?");
    $resim_stmt->execute([$id]);
    $yonetici = $resim_stmt->fetch();
    if ($yonetici && !empty($yonetici['profil_resmi'])) {
        $dosya = __DIR__ . '/uploads/yoneticiler/' . $yonetici['profil_resmi'];
        if (file_exists($dosya)) {
            @unlink($dosya);
        }
    }
    
    $stmt = $db->prepare("DELETE FROM portfoy_yoneticileri WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: yoneticiler.php?basari=1");
        exit;
    }
}

header("Location: yoneticiler.php?hata=1");
exit;
?>
