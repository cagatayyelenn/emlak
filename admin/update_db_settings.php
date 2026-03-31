<?php
/**
 * Site Ayarları Tablosunu Oluşturma Script'i
 */
require_once __DIR__ . '/includes/database.php';

echo "<div style='font-family:sans-serif; padding:40px; text-align:center;'>";
echo "<h2 style='color:#4361ee;'>Site Ayarları Tablosu Kurulumu</h2>";
echo "<div style='background:#f4f4f4; border-radius: 0px; padding:20px; display:inline-block; text-align:left; border:1px solid #ddd;'>";

try {
    // Site Ayarları Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS site_ayarlari (
        id INT AUTO_INCREMENT PRIMARY KEY,
        site_baslik VARCHAR(255),
        site_aciklama TEXT,
        site_anahtar_kelimeler TEXT,
        logo VARCHAR(255),
        favicon VARCHAR(255),
        google_analytics TEXT,
        google_search_console TEXT,
        iletisim_telefon VARCHAR(50),
        iletisim_eposta VARCHAR(100),
        iletisim_adres TEXT,
        facebook VARCHAR(255),
        instagram VARCHAR(255),
        twitter VARCHAR(255),
        linkedin VARCHAR(255),
        guncellenme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<span style='color:green;'>✔ <b>site_ayarlari</b> tablosu hazır.</span><br>";

    // Varsayılan Ayarları ekle
    $check = $db->query("SELECT id FROM site_ayarlari LIMIT 1");
    if (!$check->fetch()) {
        $ins = $db->prepare("INSERT INTO site_ayarlari (site_baslik, site_aciklama, site_anahtar_kelimeler) VALUES (?, ?, ?)");
        $ins->execute([
            'Maxwell Emlak Ofisi',
            'Gayrimenkul ve Emlak Danışmanlık Hizmetleri',
            'emlak, gayrimenkul, kiralık, satılık, daire, arsa'
        ]);
        echo "<span style='color:#e62236;'>ℹ Varsayılan site ayarları oluşturuldu.</span><br>";
    }

    echo "</div>";
    echo "<h3 style='margin-top:20px; color:#198754;'>Başarılı! Tablo hazır.</h3>";
    echo "<a href='index.php' style='background:#4361ee; color:white; padding:10px 20px; text-decoration:none; border-radius: 0px;'>Panele Dön</a>";

} catch (PDOException $e) {
    echo "</div>";
    echo "<h3 style='margin-top:20px; color:#dc3545;'>Hata Oluştu!</h3>";
    echo "<div style='text-align:left; background:#fff5f5; padding:20px; border:1px solid #feb2b2; border-radius: 0px; color:#c53030;'>";
    echo "<code>" . htmlspecialchars($e->getMessage()) . "</code>";
    echo "</div>";
}

echo "</div>";
?>
