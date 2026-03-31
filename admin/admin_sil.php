<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

$id = $_GET['id'] ?? 0;

// Ana admin silinemez (admin kullanıcısı)
$stmt = $db->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if ($admin && $admin['username'] !== 'admin') {
    $del = $db->prepare("DELETE FROM admins WHERE id = ?");
    $del->execute([$id]);
}

header("Location: adminler.php");
exit;
