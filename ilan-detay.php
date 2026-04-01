<?php
// ilan-detay.php
require_once __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if ($id <= 0 && empty($slug)) {
    header("Location: ilanlar.php");
    exit;
}

// İlan detaylarını çek
$where = $slug ? "i.slug = ?" : "i.id = ?";
$param = $slug ? $slug : $id;

$stmt = $db->prepare("
    SELECT i.*, y.ad_soyad as yonetici_ad, y.telefon as yonetici_tel, y.eposta as yonetici_email, y.profil_resmi as yonetici_gorsel
    FROM ilanlar i 
    LEFT JOIN portfoy_yoneticileri y ON i.portfoy_yoneticisi_id = y.id 
    WHERE $where
");
$stmt->execute([$param]);
$ilan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    echo "İlan bulunamadı.";
    exit;
}

// İlan medyalarını çek
$medya_stmt = $db->prepare("SELECT * FROM ilan_medya WHERE ilan_id = ? ORDER BY id ASC");
$medya_stmt->execute([$ilan['id']]);
$medyalar = $medya_stmt->fetchAll(PDO::FETCH_ASSOC);

$gorseller = array_filter($medyalar, function($m) { return $m['medya_tipi'] === 'gorsel'; });
$videolar = array_filter($medyalar, function($m) { return in_array($m['medya_tipi'], ['video', 'video_url']); });
require_once __DIR__ . '/includes/header.php';
?>
            <style>
                .property-breadcrumb { padding: 20px 0; font-size: 14px; color: #666; }
                .property-breadcrumb a { color: #666; text-decoration: none; transition: color 0.2s; }
                .property-breadcrumb a:hover { color: #4361ee; }
                .property-breadcrumb span { margin: 0 10px; color: #ccc; }
                
                .header-property-custom { margin-bottom: 30px; }
                .header-property-custom .title-area h2 { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
                .header-property-custom .location { color: #666; font-size: 16px; display: flex; align-items: center; gap: 8px; }
                .header-property-custom .location i { color: #4361ee; }
                .header-property-custom .location a { color: #666; text-decoration: underline; font-weight: 600; margin-left: 5px; }
                
                .price-area-custom { text-align: right; }
                .price-area-custom .price { font-size: 36px; font-weight: 800; color: #1a1a1a; line-height: 1; }
                .price-area-custom .price-sqft { color: #666; font-size: 16px; margin-top: 8px; font-weight: 500; }

                .gallery-grid-v2 { display: grid; grid-template-columns: 2fr 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 12px; height: 500px; margin-bottom: 40px; }
                .gallery-grid-v2 .grid-item { position: relative; overflow: hidden; border-radius: 16px; }
                .gallery-grid-v2 .grid-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
                .gallery-grid-v2 .grid-item:hover img { transform: scale(1.05); }
                .gallery-grid-v2 .item-main { grid-column: 1 / 2; grid-row: 1 / 3; }
                
                .show-all-overlay { position: absolute; bottom: 20px; right: 20px; background: white; padding: 10px 20px; border-radius: 12px; font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 10px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #eee; z-index: 10; text-decoration: none; font-size: 14px; }
                .show-all-overlay:hover { background: #f8f9fa; }

                @media (max-width: 991px) {
                    .gallery-grid-v2 { grid-template-columns: 1fr 1fr; grid-template-rows: 250px 150px; height: auto; }
                    .gallery-grid-v2 .item-main { grid-column: 1 / 3; grid-row: 1 / 2; }
                    .price-area-custom { text-align: left; margin-top: 15px; }
                }
            </style>

            
                <div class="property-breadcrumb">
                    <a href="index.php">Anasayfa</a> <span>/</span> 
                    <a href="ilanlar.php">İlanlar</a> <span>/</span> 
                    <a href="ilanlar.php?durum=<?php echo urlencode($ilan['durumu']); ?>"><?php echo htmlspecialchars($ilan['durumu']); ?></a> <span>/</span> 
                    <a href="ilanlar.php?tip=<?php echo urlencode($ilan['emlak_tipi']); ?>"><?php echo htmlspecialchars($ilan['emlak_tipi']); ?></a> <span>/</span> 
                    <b><?php echo htmlspecialchars($ilan['baslik']); ?></b>
                </div>
                
                <div class="flat-section-v4">
                    <div class="container">
                        <div class="header-property-detail">
                            <div class="content-top d-flex justify-content-between align-items-center">
                                <h2 class="title link fw-8"><?php echo htmlspecialchars($ilan['baslik']); ?></h2>
                                <div class="box-price d-flex align-items-end">
                                    <h3 class="fw-8"><?php echo number_format($ilan['fiyat'], 0, ',', '.'); ?> ₺</h3>
                                    
                                </div>
                            </div>
                            <div class="content-bottom">
                                <div class="box-left"> 
                                    <div class="info-box">
                                        <div class="label">Adres</div>
                                        <p class="meta-item">
                                            <span class="icon icon-mapPin"></span>
                                            <span class="text-variant-1"><?php echo htmlspecialchars($ilan['ilce'] . ', ' . $ilan['il'] . ', Türkiye'); ?>k</span>
                                            
                                        </p>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                 

                <section class="gallery-grid-v2">
                    <?php 
                    $gorsel_listesi = array_values($gorseller);
                    for($i=0; $i<5; $i++): 
                        $img = isset($gorsel_listesi[$i]) ? 'admin/uploads/images/' . $gorsel_listesi[$i]['dosya_yolu'] : 'https://placehold.co/800x600?text=Yakında+Gelecek';
                        $class = $i == 0 ? "item-main" : "";
                    ?>
                        <div class="grid-item <?php echo $class; ?>">
                            <a href="<?php echo $img; ?>" data-fancybox="gallery">
                                <img src="<?php echo $img; ?>" alt="Property Image <?php echo $i+1; ?>">
                            </a>
                            <?php if($i == 4): ?>
                                <a href="<?php echo $img; ?>" data-fancybox="gallery" class="show-all-overlay">
                                    <i class="fa-solid fa-images"></i> Tüm Fotoğrafları Gör
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                    
                    <?php 
                    // Geri kalan resimleri Fancybox galerisine gizli olarak ekle
                    for($i=5; $i<count($gorsel_listesi); $i++): 
                        $img = 'admin/uploads/images/' . $gorsel_listesi[$i]['dosya_yolu'];
                    ?>
                        <a href="<?php echo $img; ?>" data-fancybox="gallery" class="d-none"></a>
                    <?php endfor; ?>
                </section>
             

            <section class="flat-section-v3 flat-property-detail">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="single-property-element single-property-desc">
                                <h5 class="fw-6 title">Açıklama</h5>
                                <div class="text-variant-1">
                                    <?php echo nl2br(htmlspecialchars($ilan['aciklama'])); ?>
                                </div>
                            </div>
                            <div class="single-property-element single-property-overview">
                                <h6 class="title fw-6">Genel Bakış</h6>
                                <ul class="info-box">
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-house-line"></i></a>
                                        <div class="content">
                                            <span class="label">İlan No:</span>
                                            <span><?php echo htmlspecialchars($ilan['ilan_no']); ?></span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-sliders-horizontal"></i></a>
                                        <div class="content">
                                            <span class="label">Emlak Tipi:</span>
                                            <span><?php echo htmlspecialchars($ilan['emlak_tipi']); ?></span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-bed1"></i></a>
                                        <div class="content">
                                            <span class="label">Oda Sayısı:</span>
                                            <span><?php echo htmlspecialchars($ilan['oda_sayisi']); ?></span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-bathtub"></i></a>
                                        <div class="content">
                                            <span class="label">Banyo Sayısı:</span>
                                            <span><?php echo htmlspecialchars($ilan['banyo_sayisi']); ?></span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-crop"></i></a>
                                        <div class="content">
                                            <span class="label">Metrekare:</span>
                                            <span><?php echo htmlspecialchars($ilan['m2_brut']); ?> m²</span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <a href="#" class="box-icon w-52"><i class="icon icon-hammer"></i></a>
                                        <div class="content">
                                            <span class="label">Bina Yaşı:</span>
                                            <span><?php echo htmlspecialchars($ilan['bina_yasi']); ?></span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="single-property-element single-property-feature">
                                <h6 class="title fw-6">İlan Detayları</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-feature">
                                            <li class="item">
                                                <span class="text-variant-1">Isıtma:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['isitma']); ?></span>
                                            </li>
                                            <li class="item">
                                                <span class="text-variant-1">Bulunduğu Kat:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['bulundugu_kat']); ?></span>
                                            </li>
                                            <li class="item">
                                                <span class="text-variant-1">Kat Sayısı:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['kat_sayisi']); ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-feature">
                                            <li class="item">
                                                <span class="text-variant-1">Eşyalı:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['esyali']); ?></span>
                                            </li>
                                            <li class="item">
                                                <span class="text-variant-1">Krediye Uygun:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['krediye_uygun']); ?></span>
                                            </li>
                                            <li class="item">
                                                <span class="text-variant-1">Site İçerisinde:</span>
                                                <span class="fw-6"><?php echo htmlspecialchars($ilan['site_icerisinde']); ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <?php if(!empty($ilan['harita_konumu'])): ?>
                            <div class="single-property-element single-property-map">
                                <h6 class="title fw-6">Konum</h6>
                                <div class="map-box">
                                     <?php echo $ilan['harita_konumu']; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="single-sidebar fixed-sidebar">
                                <div class="widget-box single-property-contact">
                                    <h5 class="title fw-6">İlan Danışman Bilgileri</h5>
                                    <div class="box-avatar">
                                        <div class="avatar avt-100 round" style="    border-radius: 50%;">
                                           <?php if(!empty($ilan['yonetici_gorsel'])): ?>
                                                <img src="admin/uploads/yoneticiler/<?php echo $ilan['yonetici_gorsel']; ?>" alt="agent">
                                            <?php else: ?>
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ilan['yonetici_ad'] ?? 'Danışman'); ?>&background=random" alt="agent">
                                            <?php endif; ?>
                                        </div>
                                        <div class="info">
                                            <h6 class="name"><?php echo htmlspecialchars($ilan['yonetici_ad'] ?? 'Maxwell Emlak'); ?></h6>
                                            <ul class="list">
                                                <li class="d-flex align-items-center gap-4 text-variant-1"><i
                                                        class="icon icon-phone"></i><?php echo htmlspecialchars($ilan['yonetici_tel'] ?? $site_set['telefon']); ?></li>
                                                <li class="d-flex align-items-center gap-4 text-variant-1"><i
                                                        class="icon icon-mail"></i><?php echo htmlspecialchars($ilan['yonetici_email'] ?? $site_set['iletisim_eposta']); ?></li>
                                            </ul>
                                        </div>
                                    </div> 
                                </div>

                                <div class="widget-box single-property-contact">
                                    <h5 class="title fw-6">İlan İletişim Bilgileri</h5> 
                                     <div class="contact-agent-info mt-4">
                                        <a href="tel:<?php echo $ilan['yonetici_tel']; ?>" class="tf-btn primary w-100 mb-2">
                                            <i class="icon icon-phone2"></i> <?php echo htmlspecialchars($ilan['yonetici_tel'] ?? $site_set['telefon']); ?>
                                        </a>
                                        <a href="mailto:<?php echo $ilan['yonetici_email']; ?>" class="tf-btn btn-line w-100 <?php echo !empty($ilan['sahibinden_link']) ? 'mb-2' : ''; ?>">
                                            <i class="icon icon-mail"></i> <?php echo htmlspecialchars($ilan['yonetici_email'] ?? $site_set['iletisim_eposta']); ?>
                                        </a>
                                        <?php if(!empty($ilan['sahibinden_link'])): ?>
                                            <a href="<?php echo $ilan['sahibinden_link']; ?>" target="_blank" class="tf-btn primary w-100" style="background-color: #ffdb00; color: #333; border-color: #ffdb00;">
                                                Sahibinden'de Görüntüle
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                 
                                <div class="widget-box single-property-whychoose">
                                    <h5 class="title fw-6">Neden Bizi Seçmelisiniz?</h5>
                                    <ul class="box-whychoose">
                                        <li class="item-why">
                                            <i class="icon icon-secure"></i>
                                            Güvenli Satış
                                        </li>
                                        <li class="item-why">
                                            <i class="icon icon-guarantee"></i>
                                            En İyi Fiyat Garantisi
                                        </li>
                                        <li class="item-why">
                                            <i class="icon icon-booking"></i>
                                            Kolay İşlem Süreci
                                        </li>
                                        <li class="item-why">
                                            <i class="icon icon-support"></i>
                                            Her Zaman Ulaşılabilirlik
                                        </li>
                                    </ul>
                                </div>
                                 
                            </div>
                        </div>
                    </div>
                </div>
            </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
