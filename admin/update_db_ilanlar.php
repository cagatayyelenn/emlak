<?php
require_once __DIR__ . '/includes/database.php';

$columnsToAdd = [
    'il' => 'TEXT', 'ilce' => 'TEXT', 'mahalle' => 'TEXT', 'ilan_no' => 'TEXT',
    'ilan_tarihi' => 'DATE', 'emlak_tipi' => 'TEXT', 'm2_brut' => 'INTEGER',
    'm2_net' => 'INTEGER', 'oda_sayisi' => 'TEXT', 'bina_yasi' => 'TEXT',
    'bulundugu_kat' => 'TEXT', 'kat_sayisi' => 'INTEGER', 'isitma' => 'TEXT',
    'banyo_sayisi' => 'INTEGER', 'mutfak' => 'TEXT', 'balkon' => 'TEXT',
    'asansor' => 'TEXT', 'otopark' => 'TEXT', 'esyali' => 'TEXT',
    'kullanim_durumu' => 'TEXT', 'site_icerisinde' => 'TEXT', 'site_adi' => 'TEXT',
    'aidat' => 'REAL', 'krediye_uygun' => 'TEXT', 'tapu_durumu' => 'TEXT',
    'konum' => 'TEXT', 'harita_konumu' => 'TEXT'
];

$q = $db->query("PRAGMA table_info(ilanlar)");
$existing_columns = [];
foreach($q->fetchAll() as $col) {
    $existing_columns[] = $col['name'];
}

$added = 0;
foreach($columnsToAdd as $colName => $colType) {
    if (!in_array($colName, $existing_columns)) {
        try {
            $db->exec("ALTER TABLE ilanlar ADD COLUMN {$colName} {$colType}");
            $added++;
        } catch (PDOException $e) {
            echo "Hata ({$colName}): " . $e->getMessage() . "\n";
        }
    }
}

echo "İşlem Tamamlandı. Toplam {$added} yeni ilan özellik sütunu veritabanına başarıyla eklendi.\n";
?>
