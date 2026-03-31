<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/database.php';

try {
    $stmt = $db->prepare("INSERT INTO portfoy_yoneticileri (ad_soyad) VALUES ('Test User')");
    $result = $stmt->execute();
    echo "Result: " . ($result ? "Success" : "Failed") . "<br>";
    echo "ID: " . $db->lastInsertId() . "<br>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Current DB File: " . $db_file . "<br>";
echo "DB File Exists: " . (file_exists($db_file) ? "Yes" : "No") . "<br>";
echo "DB File Writable: " . (is_writable($db_file) ? "Yes" : "No") . "<br>";
echo "Dir Writable: " . (is_writable(dirname($db_file)) ? "Yes" : "No") . "<br>";
?>
