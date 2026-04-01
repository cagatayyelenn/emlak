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
                                        <a href="#harita">Haritada Gör</a>
                                    </p>
                                </div>
                            </div>

                            <ul class="icon-box">
                                <li><a href="#" class="item">
                                        <svg class="icon" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M15.75 6.1875C15.75 4.32375 14.1758 2.8125 12.234 2.8125C10.7828 2.8125 9.53625 3.657 9 4.86225C8.46375 3.657 7.21725 2.8125 5.76525 2.8125C3.825 2.8125 2.25 4.32375 2.25 6.1875C2.25 11.6025 9 15.1875 9 15.1875C9 15.1875 15.75 11.6025 15.75 6.1875Z"
                                                stroke="#A3ABB0" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </a></li>
                                <li><a href="#" class="item">
                                        <svg class="icon" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5.625 15.75L2.25 12.375M2.25 12.375L5.625 9M2.25 12.375H12.375M12.375 2.25L15.75 5.625M15.75 5.625L12.375 9M15.75 5.625H5.625"
                                                stroke="#A3ABB0" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </a></li>
                                <li><a href="#" class="item">
                                        <svg class="icon" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5.41251 8.18022C5.23091 7.85345 4.94594 7.59624 4.60234 7.44895C4.25874 7.30167 3.87596 7.27265 3.51408 7.36645C3.1522 7.46025 2.83171 7.67157 2.60293 7.96722C2.37414 8.26287 2.25 8.62613 2.25 8.99997C2.25 9.37381 2.37414 9.73706 2.60293 10.0327C2.83171 10.3284 3.1522 10.5397 3.51408 10.6335C3.87596 10.7273 4.25874 10.6983 4.60234 10.551C4.94594 10.4037 5.23091 10.1465 5.41251 9.81972M5.41251 8.18022C5.54751 8.42322 5.62476 8.70222 5.62476 8.99997C5.62476 9.29772 5.54751 9.57747 5.41251 9.81972M5.41251 8.18022L12.587 4.19472M5.41251 9.81972L12.587 13.8052M12.587 4.19472C12.6922 4.39282 12.8358 4.56797 13.0095 4.70991C13.1832 4.85186 13.3834 4.95776 13.5985 5.02143C13.8135 5.08509 14.0392 5.10523 14.2621 5.08069C14.4851 5.05614 14.7009 4.98739 14.897 4.87846C15.093 4.76953 15.2654 4.62261 15.404 4.44628C15.5427 4.26995 15.6448 4.06775 15.7043 3.85151C15.7639 3.63526 15.7798 3.40931 15.751 3.18686C15.7222 2.96442 15.6494 2.74994 15.5368 2.55597C15.3148 2.17372 14.9518 1.89382 14.5256 1.77643C14.0995 1.65904 13.6443 1.71352 13.2579 1.92818C12.8715 2.14284 12.5848 2.50053 12.4593 2.92436C12.3339 3.34819 12.3797 3.80433 12.587 4.19472ZM12.587 13.8052C12.4794 13.999 12.4109 14.2121 12.3856 14.4323C12.3603 14.6525 12.3787 14.8756 12.4396 15.0887C12.5005 15.3019 12.6028 15.5009 12.7406 15.6746C12.8784 15.8482 13.0491 15.9929 13.2429 16.1006C13.4367 16.2082 13.6498 16.2767 13.87 16.302C14.0902 16.3273 14.3133 16.3089 14.5264 16.248C14.7396 16.1871 14.9386 16.0848 15.1122 15.947C15.2858 15.8092 15.4306 15.6385 15.5383 15.4447C15.7557 15.0534 15.8087 14.5917 15.6857 14.1612C15.5627 13.7307 15.2737 13.3668 14.8824 13.1493C14.491 12.9319 14.0293 12.8789 13.5989 13.0019C13.1684 13.1249 12.8044 13.4139 12.587 13.8052Z"
                                                stroke="#A3ABB0" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a></li>
                                <li><a href="#" class="item">
                                        <svg class="icon" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5.04 10.3718C4.86 10.3943 4.68 10.4183 4.5 10.4438M5.04 10.3718C7.66969 10.0418 10.3303 10.0418 12.96 10.3718M5.04 10.3718L4.755 13.5M12.96 10.3718C13.14 10.3943 13.32 10.4183 13.5 10.4438M12.96 10.3718L13.245 13.5L13.4167 15.3923C13.4274 15.509 13.4136 15.6267 13.3762 15.7378C13.3388 15.8489 13.2787 15.951 13.1996 16.0376C13.1206 16.1242 13.0244 16.1933 12.9172 16.2407C12.8099 16.288 12.694 16.3125 12.5767 16.3125H5.42325C4.92675 16.3125 4.53825 15.8865 4.58325 15.3923L4.755 13.5M4.755 13.5H3.9375C3.48995 13.5 3.06072 13.3222 2.74426 13.0057C2.42779 12.6893 2.25 12.2601 2.25 11.8125V7.092C2.25 6.28125 2.826 5.58075 3.62775 5.46075C4.10471 5.3894 4.58306 5.32764 5.0625 5.2755M13.2435 13.5H14.0618C14.2834 13.5001 14.5029 13.4565 14.7078 13.3718C14.9126 13.287 15.0987 13.1627 15.2555 13.006C15.4123 12.8493 15.5366 12.6632 15.6215 12.4585C15.7063 12.2537 15.75 12.0342 15.75 11.8125V7.092C15.75 6.28125 15.174 5.58075 14.3723 5.46075C13.8953 5.38941 13.4169 5.32764 12.9375 5.2755M12.9375 5.2755C10.3202 4.99073 7.67978 4.99073 5.0625 5.2755M12.9375 5.2755V2.53125C12.9375 2.0655 12.5595 1.6875 12.0938 1.6875H5.90625C5.4405 1.6875 5.0625 2.0655 5.0625 2.53125V5.2755M13.5 7.875H13.506V7.881H13.5V7.875ZM11.25 7.875H11.256V7.881H11.25V7.875Z"
                                                stroke="#A3ABB0" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a></li>

                            </ul>

                        </div>
                    </div>
                </div>
            </div>
                <div class="header-property-custom">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="title-area">
                                <h2><?php echo htmlspecialchars($ilan['baslik']); ?></h2>
                                <div class="location">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?php echo htmlspecialchars($ilan['ilce'] . ', ' . $ilan['il'] . ', Türkiye'); ?>
                                    <a href="#map-section">Haritada Gör</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="price-area-custom">
                                <div class="price"><?php echo number_format($ilan['fiyat'], 0, ',', '.'); ?> ₺</div>
                                <?php if($ilan['m2_brut'] > 0): ?>
                                    <div class="price-sqft"><?php echo number_format($ilan['fiyat'] / $ilan['m2_brut'], 0, ',', '.'); ?> ₺ / m²</div>
                                <?php endif; ?>
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
