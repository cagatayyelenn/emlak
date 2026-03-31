<?php
// init_db.php
require_once __DIR__ . '/includes/database.php';

try {
    // Portföy Yöneticileri Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS portfoy_yoneticileri (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ad_soyad TEXT NOT NULL,
        telefon TEXT,
        eposta TEXT,
        olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // İlanlar Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS ilanlar (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        baslik TEXT NOT NULL,
        aciklama TEXT,
        fiyat REAL,
        portfoy_yoneticisi_id INTEGER,
        olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (portfoy_yoneticisi_id) REFERENCES portfoy_yoneticileri(id) ON DELETE SET NULL
    )");

    // İlan Medyaları Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS ilan_medya (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ilan_id INTEGER NOT NULL,
        medya_tipi TEXT NOT NULL, -- 'gorsel' veya 'video'
        dosya_yolu TEXT NOT NULL, -- dosya adı veya link
        sira INTEGER DEFAULT 0,
        olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE
    )");

    echo "Veritabanı tabloları başarıyla oluşturuldu!\n";
} catch (PDOException $e) {
    die("Hata oluştu: " . $e->getMessage());
}
?>
