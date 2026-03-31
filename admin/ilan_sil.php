<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Görselleri ve Videoları fiziksel sunucu dizininden sil
    $medyalar = $db->prepare("SELECT dosya_yolu, medya_tipi FROM ilan_medya WHERE ilan_id = ?");
    $medyalar->execute([$id]);
    foreach ($medyalar->fetchAll() as $medya) {
        if ($medya['medya_tipi'] === 'gorsel') {
            $yol = __DIR__ . '/uploads/images/' . $medya['dosya_yolu'];
        } elseif ($medya['medya_tipi'] === 'video') {
            $yol = __DIR__ . '/uploads/videos/' . $medya['dosya_yolu'];
        } else {
            $yol = '';
        }
        if ($yol && file_exists($yol)) {
            @unlink($yol);
        }
    }
    
    // foreign_key ON DELETE CASCADE sayesinde ilan silinince medyalar veritabanından otomatik silinir.
    $stmt = $db->prepare("DELETE FROM ilanlar WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: ilanlar.php?basari=1");
        exit;
    }
}

header("Location: ilanlar.php?hata=1");
exit;
?>
