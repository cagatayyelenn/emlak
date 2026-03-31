<?php
require_once 'includes/database.php';
require_once '../includes/slug.php';

// Mevcut ilanların slug değerlerini doldurmak için bir kerelik script
try {
    $stmt = $db->query("SELECT id, baslik FROM ilanlar WHERE slug IS NULL OR slug = ''");
    $ilanlar = $stmt->fetchAll();
    
    $count = 0;
    foreach ($ilanlar as $ilan) {
        $slug = createSlug($ilan['baslik']);
        
        // Benzersiz olması için kontrol et (basitçe her zaman id ekleyebiliriz veya kontrol edebiliriz)
        // Şimdilik sadece güncelleme yapalım
        $updateSt = $db->prepare("UPDATE ilanlar SET slug = ? WHERE id = ?");
        $updateSt->execute([$slug, $ilan['id']]);
        $count++;
    }
    
    echo "Başarıyla $count ilan için slug oluşturuldu.";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
