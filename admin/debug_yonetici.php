<?php
require_once 'includes/database.php';
try {
    $q = $db->query("DESCRIBE portfoy_yoneticileri");
    echo "<pre>";
    print_r($q->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
