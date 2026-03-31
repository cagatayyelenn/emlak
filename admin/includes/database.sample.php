<?php
/**
 * Veritabanı Yapılandırma Şablonu
 * Bu dosyayı 'database.php' adıyla kopyalayın ve bilgileri doldurun.
 */
$db_host = 'localhost';
$db_name = 'veritabanı_adı';
$db_user = 'kullanıcı_adı';
$db_pass = 'şifre';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası!");
}
?>
