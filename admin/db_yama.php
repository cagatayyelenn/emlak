<?php
require_once __DIR__ . '/includes/database.php';

$fields = [
    'durumu' => "TEXT DEFAULT 'Satılık'",
    'il' => "TEXT",
    'ilce' => "TEXT",
    'mahalle' => "TEXT",
    'ilan_no' => "TEXT",
    'ilan_tarihi' => "TEXT",
    'emlak_tipi' => "TEXT",
    'm2_brut' => "INTEGER",
    'm2_net' => "INTEGER",
    'oda_sayisi' => "TEXT",
    'bina_yasi' => "TEXT",
    'bulundugu_kat' => "TEXT",
    'kat_sayisi' => "INTEGER",
    'isitma' => "TEXT",
    'banyo_sayisi' => "INTEGER",
    'mutfak' => "TEXT",
    'balkon' => "TEXT",
    'asansor' => "TEXT",
    'otopark' => "TEXT",
    'esyali' => "TEXT",
    'kullanim_durumu' => "TEXT",
    'site_icerisinde' => "TEXT",
    'site_adi' => "TEXT",
    'aidat' => "REAL",
    'krediye_uygun' => "TEXT",
    'tapu_durumu' => "TEXT",
    'konum' => "TEXT",
    'harita_konumu' => "TEXT",
    'vitrin_gorseli' => "TEXT"
];

$eklenen = 0;
echo "<div style='font-family:sans-serif; padding:40px; text-align:center;'>";
echo "<h2 style='color:#4361ee;'>Sistem Veritabanı Senkronizasyon (Yama) Aracı</h2>";
echo "<div style='background:#f4f4f4; border-radius:10px; padding:20px; display:inline-block; text-align:left;'>";

foreach($fields as $k => $type) {
    try {
        $db->exec("ALTER TABLE ilanlar ADD COLUMN $k $type");
        $eklenen++;
        echo "<span style='color:green;'>✔ <b>$k</b> sütunu aşılandı.</span><br>";
    } catch(Exception $e) {
        // Zaten var hatalarını görmezden gel
    }
}

echo "</div>";
echo "<h3 style='margin-top:20px; color:#198754;'>İşlem Tamamlandı! Toplam $eklenen eksik sütun giderildi!</h3>";
echo "<p>Şimdi bu sekmeyi kapatıp İlan Ekle sayfanıza dönerek dilediğinizce kayıt yapabilirsiniz.</p>";
echo "</div>";
?>
