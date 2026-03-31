<?php
// includes/db.php - Front-end veritabanı bağlantı köprüsü

// Admin Klasörü içindeki veritabanı ayarlarını dahil et
require_once __DIR__ . '/../admin/includes/database.php';

// Site ayarlarını her sayfada kullanılabilir yapmak için burada çekelim
try {
    $site_stmt = $db->query("SELECT * FROM site_ayarlari LIMIT 1");
    $site_set = $site_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$site_set) {
        $site_set = [
            'site_baslik' => 'Maxwell Emlak Ofisi',
            'site_aciklama' => 'Gayrimenkul ve Emlak Danışmanlık Hizmetleri',
            'site_anahtar_kelimeler' => 'emlak, gayrimenkul, satılık, kiralık, daire',
            'logo' => null,
            'favicon' => null
        ];
    }
} catch (PDOException $e) {
    // Tablo henüz oluşturulmamışsa varsayılan değerler
    $site_set = [
        'site_baslik' => 'Maxwell Emlak Ofisi',
        'site_aciklama' => 'Gayrimenkul ve Emlak Danışmanlık Hizmetleri',
        'site_anahtar_kelimeler' => 'emlak, gayrimenkul, satılık, kiralık, daire',
        'logo' => null,
        'favicon' => null
    ];
}
?>
