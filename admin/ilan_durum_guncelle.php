<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $durum = $_POST['durum'] ?? '';

    if ($id > 0 && in_array($durum, ['Aktif', 'Satıldı', 'Kiralandı'])) {
        try {
            $stmt = $db->prepare("UPDATE ilanlar SET yayin_durumu = ? WHERE id = ?");
            $result = $stmt->execute([$durum, $id]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'İlan durumu güncellendi.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz veri.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
}
?>
