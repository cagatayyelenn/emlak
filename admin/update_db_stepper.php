<?php
require_once __DIR__ . '/includes/database.php';
$added = 0;
// Durumu
try { $db->exec("ALTER TABLE ilanlar ADD COLUMN durumu TEXT DEFAULT 'Satılık'"); $added++; echo "sutun:durumu eklendi. "; } catch(Exception $e) {}
// Vitrin Gorseli
try { $db->exec("ALTER TABLE ilanlar ADD COLUMN vitrin_gorseli TEXT"); $added++; echo "sutun:vitrin_gorseli eklendi."; } catch(Exception $e) {}
echo "\nKritik veritabanı yama işlemi tamamlandı. Yeni sütunlar ({$added}) eklendi.";
?>
