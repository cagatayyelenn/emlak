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

                /* Read More styles */
                .desc-content { max-height: 120px; overflow: hidden; position: relative; transition: max-height 0.5s ease; }
                .desc-content.expanded { max-height: 2000px; }
                .desc-content:not(.expanded)::after { content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 50px; background: linear-gradient(transparent, #fff); pointer-events: none; }
                .btn-view-toggle { cursor: pointer; display: inline-flex; align-items: center; gap: 5px; color: #4361ee; font-weight: 700; margin-top: 15px; }

                @media (max-width: 991px) {
                    .gallery-grid-v2 { grid-template-columns: 1fr 1fr; grid-template-rows: 250px 150px; height: auto; }
                    .gallery-grid-v2 .item-main { grid-column: 1 / 3; grid-row: 1 / 2; }
                    .price-area-custom { text-align: left; margin-top: 15px; }
                }
            </style>

            <div class="container">
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
            </div>

            <section class="flat-section-v3 flat-property-detail">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <!-- İlan Bilgileri -->
                            <div class="single-property-element single-property-info">
                                <h5 class="title fw-6">İlan Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">İlan no:</span>
                                            <div class="content text-black-3">#<?php echo htmlspecialchars($ilan['ilan_no'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Emlak Tipi</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['emlak_tipi'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Oda Sayısı</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['oda_sayisi'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">m² (Brüt)</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['m2_brut'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">m² (Net)</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['m2_net'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Bina Yaşı</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['bina_yasi'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Bulunduğu Kat</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['bulundugu_kat'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Kat Sayısı</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['kat_sayisi'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Banyo Sayısı</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['banyo_sayisi'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Isıtma</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['isitma'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Mutfak</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['mutfak'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Balkon</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['balkon'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Otopark</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['otopark'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Eşyalı Mı?</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['esyali'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Asansör</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['asansor'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Kullanım Durumu</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['kullanim_durumu'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Krediye Uygun</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['krediye_uygun'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Tapu Durumu</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['tapu_durumu'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Site İçerisinde</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['site_icerisinde'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Site Adı</span>
                                            <div class="content text-black-3"><?php echo htmlspecialchars($ilan['site_adi'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <span class="label text-black-3">Aidat (₺)</span>
                                            <div class="content text-black-3"><?php echo number_format($ilan['aidat'] ?? 0, 0, ',', '.'); ?> ₺</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- İlan Bilgileri -->
                            
                            <!-- İlan Açıklaması -->
                            <!-- İlan Açıklaması -->
                            <div class="single-property-element single-property-desc">
                                <h5 class="fw-6 title">İlan Açıklaması </h5>
                                <div id="descContainer" class="desc-content">
                                    <p class="text-variant-1"><?php echo nl2br(htmlspecialchars($ilan['aciklama'])); ?></p>
                                </div>
                                <a id="toggleDesc" class="btn-view-toggle"><span class="text">Daha Fazlası</span> </a>
                            </div>
                            <!-- İlan Açıklaması -->

                            <!-- İlan Videosu -->
                            <?php if (count($videolar) > 0): ?>
                            <div class="single-property-element single-property-video">
                                <h5 class="title fw-6">Video</h5>
                                <div class="img-video">
                                    <?php 
                                    $video = reset($videolar); 
                                    $video_src = ($video['medya_tipi'] === 'video_url') ? $video['dosya_yolu'] : 'admin/uploads/videos/' . $video['dosya_yolu'];
                                    ?>
                                    <img src="images/banner/img-video.jpg" alt="img-video">
                                    <a href="<?php echo $video_src; ?>" data-fancybox="gallery2" class="btn-video">
                                        <span class="icon icon-play"></span></a>
                                </div>
                            </div>
                            <?php endif; ?>
                            <!-- İlan Videosu -->
                            
                            <!-- İlan Konumu -->
                            <?php if (!empty($ilan['harita_konumu'])): ?>
                            <div class="single-property-element single-property-map">
                                <h5 class="title fw-6">İlan Konumu</h5>
                                <iframe class="map"
                                    src=" <?php echo $ilan['harita_konumu']; ?>"
                                    height="478" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>                                
                            </div>
                            <?php endif; ?>
                            <!-- İlan Konumu -->

                            <!-- İlan Dosyaları -->
                            <?php if (!empty($ilan_dosyalari)): // Henüz veritabanında dosya alanı yok, bu yüzden gizli kalacak ?>
                            <div class="single-property-element single-property-attachments">
                                <h6 class="title fw-6">İlan İle İlgili Dosyalar</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="#" target="_blank" class="attachments-item">
                                            <div class="box-icon w-60">
                                                <img src="images/home/file-1.png" alt="file">
                                            </div>
                                            <span>Villa-Document.pdf</span>
                                            <i class="icon icon-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <!-- İlan Dosyaları -->

                        </div>
                        <!-- İlan Danışmanı -->
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
                        <!-- İlan Danışmanı -->
                    </div>
                </div>
            </section>
            <section class="flat-section" style="   padding-top: 0px !important;">
                <div class="container">
                    <div class="box-title">
                        <div class="text-center wow fadeInUp"> 
                            <h3 class="title mt-4">Diğer İlanlarımız</h3>
                        </div>
                    </div>
                    <div dir="ltr" class="wow fadeInUp swiper tf-sw-mobile" data-wow-delay=".2s" data-screen="767"
                        data-preview="1" data-space="15">
                        <div class="tf-layout-mobile-md xl-col-3 md-col-2 swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-7.jpg"
                                                    src="images/home/house-7.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>

                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png1.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-8.jpg"
                                                    src="images/home/house-8.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>

                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png2.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-9.jpg"
                                                    src="images/home/house-9.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>

                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png3.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-10.jpg"
                                                    src="images/home/house-10.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>

                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png4.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-11.jpg"
                                                    src="images/home/house-11.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>

                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png5.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="property-details-v1.html" class="images-group">
                                            <div class="images-style">
                                                <img class="lazyload" data-src="images/home/house-12.jpg"
                                                    src="images/home/house-12.jpg" alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary">Featured</li>
                                                    <li class="flag-tag style-1">For Sale</li>
                                                </ul>
                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                145 Brooklyn Ave, Califonia, New York
                                            </div>
                                        </a>

                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="property-details-v1.html" class="link">
                                                    Casa Lomas de Machalí Machas</a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Beds:</span>
                                                    <span class="fw-6">3</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Baths:</span>
                                                    <span class="fw-6">2</span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">Sqft:</span>
                                                    <span class="fw-6">1150</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="images/avatar/avt-png6.png" alt="avt">
                                                </div>
                                                <span>Arlene McCoy</span>
                                            </div>
                                            <h6 class="price">$7250,00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sw-pagination sw-pagination-mb text-center d-md-none d-block"></div>
                        <div class="text-center sec-btn">
                            <a href="ilanlar.php" class="tf-btn btn-view primary size-1 hover-btn-view">Tüm İlanları Görüntüle <span class="icon icon-arrow-right2"></span></a>
                        </div>
                    </div>
                </div>
            </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleDesc');
            const descContainer = document.getElementById('descContainer');
            
            if (toggleBtn && descContainer) {
                // Sadece metin çok uzunsa göster (Opsiyonel ama iyi olur)
                const checkHeight = () => {
                    if (descContainer.scrollHeight <= 120) {
                        toggleBtn.style.display = 'none';
                    } else {
                        toggleBtn.style.display = 'inline-flex';
                    }
                };
                checkHeight();

                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isExpanded = descContainer.classList.toggle('expanded');
                    const btnText = toggleBtn.querySelector('.text');
                    
                    if (isExpanded) {
                        btnText.textContent = 'Daha Az';
                    } else {
                        btnText.textContent = 'Daha Fazlası';
                        // Yukarıya kaydır
                        descContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }
        });
    </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
