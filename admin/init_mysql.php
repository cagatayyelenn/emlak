<?php
/**
 * MySQL Veritabanı Kurulum Script'i
 * Bu dosya ilk kez çalıştırıldığında gerekli tüm tabloları MySQL üzerinde oluşturur.
 */
require_once __DIR__ . '/includes/database.php';

echo "<div style='font-family:sans-serif; padding:40px; text-align:center;'>";
echo "<h2 style='color:#4361ee;'>Sistem Veritabanı (MySQL) Kurulum Aracı</h2>";
echo "<div style='background:#f4f4f4; border-radius: 0px; padding:20px; display:inline-block; text-align:left; border:1px solid #ddd;'>";

try {
    // 1. Portföy Yöneticileri Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS portfoy_yoneticileri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ad_soyad VARCHAR(255) NOT NULL,
        telefon VARCHAR(50),
        eposta VARCHAR(100),
        profil_resmi VARCHAR(255),
        olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<span style='color:green;'>✔ <b>portfoy_yoneticileri</b> tablosu hazır.</span><br>";

    // 2. İlanlar Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS ilanlar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        baslik VARCHAR(255) NOT NULL,
        aciklama TEXT,
        fiyat DECIMAL(15,2),
        portfoy_yoneticisi_id INT,
        olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        durumu VARCHAR(50) DEFAULT 'Satılık',
        il VARCHAR(100),
        ilce VARCHAR(100),
        mahalle VARCHAR(100),
        ilan_no VARCHAR(50),
        ilan_tarihi VARCHAR(50),
        emlak_tipi VARCHAR(100),
        m2_brut INT,
        m2_net INT,
        oda_sayisi VARCHAR(50),
        bina_yasi VARCHAR(50),
        bulundugu_kat VARCHAR(50),
        kat_sayisi INT,
        isitma VARCHAR(100),
        banyo_sayisi INT,
        mutfak VARCHAR(100),
        balkon VARCHAR(50),
        asansor VARCHAR(50),
        otopark VARCHAR(50),
        esyali VARCHAR(50),
        kullanim_durumu VARCHAR(100),
        site_icerisinde VARCHAR(50),
        site_adi VARCHAR(255),
        aidat DECIMAL(15,2),
        krediye_uygun VARCHAR(50),
        tapu_durumu VARCHAR(100),
        konum TEXT,
        harita_konumu TEXT,
        vitrin_gorseli VARCHAR(255),
        slug VARCHAR(255) UNIQUE,
        sahibinden_link TEXT,
        yayin_durumu VARCHAR(50) DEFAULT 'Aktif',
        INDEX (portfoy_yoneticisi_id),
        FOREIGN KEY (portfoy_yoneticisi_id) REFERENCES portfoy_yoneticileri(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    
    // Eksik sütunları (slug vb.) kontrol et ve ekle (Var olan tabloyu güncellemek için)
    $check_slug = $db->query("SHOW COLUMNS FROM ilanlar LIKE 'slug'");
    if (!$check_slug->fetch()) {
        $db->exec("ALTER TABLE ilanlar ADD COLUMN slug VARCHAR(255) UNIQUE AFTER vitrin_gorseli");
        echo "<span style='color:#e62236;'>ℹ <b>ilanlar</b> tablosuna <b>slug</b> sütunu eklendi.</span><br>";
    }

    $check_sh_link = $db->query("SHOW COLUMNS FROM ilanlar LIKE 'sahibinden_link'");
    if (!$check_sh_link->fetch()) {
        $db->exec("ALTER TABLE ilanlar ADD COLUMN sahibinden_link TEXT AFTER slug");
        echo "<span style='color:#e62236;'>ℹ <b>ilanlar</b> tablosuna <b>sahibinden_link</b> sütunu eklendi.</span><br>";
    }
    
    echo "<span style='color:green;'>✔ <b>ilanlar</b> tablosu hazır.</span><br>";

    // 3. İlan Medyaları Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS ilan_medya (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ilan_id INT NOT NULL,
        medya_tipi VARCHAR(50) NOT NULL,
        dosya_yolu VARCHAR(255) NOT NULL,
        sira INT DEFAULT 0,
        olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (ilan_id),
        FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<span style='color:green;'>✔ <b>ilan_medya</b> tablosu hazır.</span><br>";

    // 4. Adminler Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        ad_soyad VARCHAR(100),
        olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // admins tablosu için sütun kontrolü
    $check_admin_name = $db->query("SHOW COLUMNS FROM admins LIKE 'ad_soyad'");
    if (!$check_admin_name->fetch()) {
        $db->exec("ALTER TABLE admins ADD COLUMN ad_soyad VARCHAR(100) AFTER password");
        $db->exec("UPDATE admins SET ad_soyad = 'Sistem Yöneticisi' WHERE username = 'admin'");
        echo "<span style='color:#e62236;'>ℹ <b>admins</b> tablosuna <b>ad_soyad</b> sütunu eklendi.</span><br>";
    }
    echo "<span style='color:green;'>✔ <b>admins</b> tablosu hazır.</span><br>";

    // 5. Sayfalar Tablosu (Hakkımızda, İletişim vb.)
    $db->exec("CREATE TABLE IF NOT EXISTS sayfalar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        baslik VARCHAR(255) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        icerik LONGTEXT,
        guncellenme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<span style='color:green;'>✔ <b>sayfalar</b> tablosu hazır.</span><br>";

    // Varsayılan Sayfaları ekle
    $sayfalar_seed = [
        ['baslik' => 'Hakkımızda', 'slug' => 'hakkimizda', 'icerik' => 'Maxwell Emlak Ofisi olarak 20 yılı aşkın tecrübemizle...'],
        ['baslik' => 'İletişim', 'slug' => 'iletisim', 'icerik' => 'Adres: İstanbul, Türkiye<br>Telefon: +90 555 123 45 67']
    ];

    foreach ($sayfalar_seed as $s) {
        $check = $db->prepare("SELECT id FROM sayfalar WHERE slug = ?");
        $check->execute([$s['slug']]);
        if (!$check->fetch()) {
            $ins = $db->prepare("INSERT INTO sayfalar (baslik, slug, icerik) VALUES (?, ?, ?)");
            $ins->execute([$s['baslik'], $s['slug'], $s['icerik']]);
            echo "<span style='color:#e62236;'>ℹ '{$s['baslik']}' sayfası oluşturuldu.</span><br>";
        }
    }

    // 6. Sayfa Blokları Tablosu (Elementor Mantığı)
    $db->exec("CREATE TABLE IF NOT EXISTS sayfa_bloklari (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sayfa_id INT NOT NULL,
        blok_tipi VARCHAR(50) NOT NULL, -- 'hero', 'metin_gorsel', 'ekip', 'ozellikler', 'harita'
        baslik VARCHAR(255),
        alt_baslik VARCHAR(255),
        icerik LONGTEXT,
        gorsel_yolu VARCHAR(255),
        buton_metni VARCHAR(100),
        buton_link VARCHAR(255),
        ikon VARCHAR(50),
        sira INT DEFAULT 0,
        FOREIGN KEY (sayfa_id) REFERENCES sayfalar(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<span style='color:green;'>✔ <b>sayfa_bloklari</b> tablosu hazır.</span><br>";

    // 7. Site Ayarları Tablosu
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
        sahibinden_url VARCHAR(255),
        logo_beyaz VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // site_ayarlari tablosu için sütun kontrolleri
    $check_logo_beyaz = $db->query("SHOW COLUMNS FROM site_ayarlari LIKE 'logo_beyaz'");
    if (!$check_logo_beyaz->fetch()) {
        $db->exec("ALTER TABLE site_ayarlari ADD COLUMN logo_beyaz VARCHAR(255) AFTER logo");
        echo "<span style='color:#e62236;'>ℹ <b>site_ayarlari</b> tablosuna <b>logo_beyaz</b> sütunu eklendi.</span><br>";
    }
    echo "<span style='color:green;'>✔ <b>site_ayarlari</b> tablosu hazır.</span><br>";

    // Varsayılan Site Ayarlarını ekle
    $check_settings = $db->query("SELECT id FROM site_ayarlari LIMIT 1");
    if (!$check_settings->fetch()) {
        $db->exec("INSERT INTO site_ayarlari (site_baslik, site_aciklama) VALUES ('Emlak Sitesi', 'En güncel ilanlar...') ");
        echo "<span style='color:#e62236;'>ℹ Varsayılan site ayarları oluşturuldu.</span><br>";
    }

    // Varsayılan Admini ekle
    $stmt = $db->prepare("SELECT id FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hash = password_hash("123456", PASSWORD_DEFAULT);
        $insert = $db->prepare("INSERT INTO admins (username, password, ad_soyad) VALUES (?, ?, ?)");
        $insert->execute(["admin", $hash, "Sistem Yöneticisi"]);
        echo "<span style='color:#e62236;'>ℹ Varsayılan yönetici kullanıcı (admin/123456) oluşturuldu.</span><br>";
    }

    echo "</div>";
    echo "<h3 style='margin-top:20px; color:#198754;'>Başarılı! Veritabanı kurulumu tamamlandı.</h3>";
    echo "<p>Artık tüm işlemlerinizi MySQL üzerinden gerçekleştirebilirsiniz.</p>";
    echo "<a href='index.php' style='background:#4361ee; color:white; padding:10px 20px; text-decoration:none; border-radius: 0px;'>Panele Git</a>";

} catch (PDOException $e) {
    echo "</div>";
    echo "<h3 style='margin-top:20px; color:#dc3545;'>Hata Oluştu!</h3>";
    echo "<div style='text-align:left; background:#fff5f5; padding:20px; border:1px solid #feb2b2; border-radius: 0px; color:#c53030;'>";
    echo "<code>" . htmlspecialchars($e->getMessage()) . "</code>";
    echo "</div>";
    echo "<p>Lütfen <b>includes/database.php</b> bilgilerini kontrol edin ve tekrar deneyin.</p>";
}

echo "</div>";
?>
