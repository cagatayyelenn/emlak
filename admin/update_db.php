<?php
require_once __DIR__ . '/includes/database.php';

try {
    // Adminler tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

    // Varsayılan admini ekleyelim (Eğer önceden yoksa)
    $stmt = $db->prepare("SELECT id FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hash = password_hash("123456", PASSWORD_DEFAULT);
        $insert = $db->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $insert->execute(["admin", $hash]);
    }

    echo "Veritabanına 'admins' tablosu eklendi ve varsayılan yönetici (Kullanıcı: admin, Şifre: 123456) oluşturuldu.\n";
} catch (PDOException $e) {
    die("Hata oluştu: " . $e->getMessage());
}
?>
