<?php
require_once 'includes/database.php';

try {
    // 1. Portföy Yöneticileri tablosuna profil_resmi sütunu ekle
    $check = $db->query("SHOW COLUMNS FROM portfoy_yoneticileri LIKE 'profil_resmi'");
    if (!$check->fetch()) {
        $db->exec("ALTER TABLE portfoy_yoneticileri ADD COLUMN profil_resmi VARCHAR(255) AFTER eposta");
        echo "<span style='color:green;'>✔ <b>portfoy_yoneticileri</b> tablosuna <b>profil_resmi</b> sütunu başarıyla eklendi.</span><br>";
    } else {
        echo "<span style='color:#e62236;'>ℹ <b>profil_resmi</b> sütunu zaten mevcut.</span><br>";
    }

    echo "<br><b style='color:green;'>İşlem tamamlandı! Şimdi yönetici düzenleme sayfasını tekrar deneyebilirsin.</b>";
    
    // Test amaçlı tablo yapısını tekrar göster
    echo "<hr>Yeni Tablo Yapısı:<pre>";
    $q = $db->query("DESCRIBE portfoy_yoneticileri");
    print_r($q->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

} catch (Exception $e) {
    echo "<b style='color:red;'>Hata oluştu:</b> " . $e->getMessage();
}
