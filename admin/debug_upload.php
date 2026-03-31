<?php
// debug_upload.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dir = __DIR__ . '/uploads/yoneticiler/';

echo "<h3>Upload Klasörü Kontrolü</h3>";
echo "Dizin: " . $dir . "<br>";

if (file_exists($dir)) {
    echo "Dizin mevcut: <span style='color:green;'>EVET</span><br>";
    if (is_writable($dir)) {
        echo "Yazma izni: <span style='color:green;'>EVET</span><br>";
    } else {
        echo "Yazma izni: <span style='color:red;'>HAYIR</span> (chmod 777 gerekebilir)<br>";
        // Yazma izni vermeye çalışalım (belki PHP izni vardır)
        @chmod($dir, 0777);
        if (is_writable($dir)) {
            echo "chmod sonrası yazma izni: <span style='color:green;'>EVET</span><br>";
        }
    }
} else {
    echo "Dizin mevcut: <span style='color:red;'>HAYIR</span> (Oluşturulmaya çalışılıyor...)<br>";
    if (mkdir($dir, 0777, true)) {
        echo "Dizin oluşturuldu: <span style='color:green;'>EVET</span><br>";
    } else {
        echo "Dizin oluşturulamadı: <span style='color:red;'>HAYIR</span><br>";
    }
}

echo "<h3>Dosya Yükleme Testi (Manuel)</h3>";
$test_file = $dir . "test.txt";
if (@file_put_contents($test_file, "test")) {
    echo "Deneme dosyası oluşturuldu: <span style='color:green;'>BAŞARILI</span><br>";
    @unlink($test_file);
} else {
    echo "Deneme dosyası oluşturulamadı: <span style='color:red;'>BAŞARISIZ</span><br>";
}

echo "<h3>$_FILES İçeriği (Eğer form submİt edildiyse)</h3>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";
