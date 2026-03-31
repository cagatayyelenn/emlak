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
    SELECT i.*, y.ad_soyad as yonetici_ad, y.telefon as yonetici_tel, y.email as yonetici_email, y.gorsel as yonetici_gorsel
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

            <div class="flat-section-v4">
                <div class="container">
                    <div class="header-property-detail">
                        <div class="content-top d-flex justify-content-between align-items-center">
                            <h3 class="title link fw-8"><?php echo htmlspecialchars($ilan['baslik']); ?></h3>
                            <div class="box-price d-flex align-items-end">
                                <h3 class="fw-8"><?php echo number_format($ilan['fiyat'], 0, ',', '.'); ?> ₺</h3>
                                <?php if($ilan['durumu'] == 'Kiralık'): ?>
                                    <span class="body-1 text-variant-1">/aylık</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="content-bottom">
                            <div class="box-left">
                                <div class="info-box">
                                    <div class="label">Özellikler</div>
                                    <ul class="meta">
                                        <li class="meta-item">
                                            <i class="icon icon-bed"></i>
                                            <span class="text-variant-1">Oda:</span>
                                            <span class="fw-6"><?php echo htmlspecialchars($ilan['oda_sayisi']); ?></span>
                                        </li>
                                        <li class="meta-item">
                                            <i class="icon icon-bath"></i>
                                            <span class="text-variant-1">Banyo:</span>
                                            <span class="fw-6"><?php echo htmlspecialchars($ilan['banyo_sayisi']); ?></span>
                                        </li>
                                        <li class="meta-item">
                                            <i class="icon icon-sqft"></i>
                                            <span class="text-variant-1">m²:</span>
                                            <span class="fw-6"><?php echo htmlspecialchars($ilan['m2_brut']); ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="info-box">
                                    <div class="label">Konum</div>
                                    <p class="meta-item">
                                        <span class="icon icon-mapPin"></span>
                                        <span class="text-variant-1"><?php echo htmlspecialchars($ilan['il'] . ' / ' . $ilan['ilce'] . ' / ' . $ilan['mahalle']); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="flat-gallery-single">
                <?php 
                $gorsel_listesi = array_values($gorseller);
                for($i=0; $i<5; $i++): 
                    $img = isset($gorsel_listesi[$i]) ? 'admin/uploads/images/' . $gorsel_listesi[$i]['dosya_yolu'] : 'images/banner/banner-property-5.jpg';
                    $class = "item" . ($i+1) . " box-img";
                ?>
                    <?php if($i == 0): ?>
                        <div class="<?php echo $class; ?>">
                            <a href="<?php echo $img; ?>" class="d-block" data-fancybox="gallery">
                                <img src="<?php echo $img; ?>" alt="img-gallery">
                            </a>
                            <div class="box-btn">
                                <?php if(!empty($videolar)): 
                                    $video = reset($videolar);
                                    $vpath = $video['medya_tipi'] == 'video' ? 'admin/uploads/videos/' . $video['dosya_yolu'] : $video['dosya_yolu'];
                                ?>
                                <a href="<?php echo $vpath; ?>" data-fancybox="gallery2" class="box-icon">
                                    <span class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.125 8.75L17.0583 4.81667C17.1457 4.72937 17.2571 4.66993 17.3782 4.64586C17.4994 4.62179 17.625 4.63417 17.7391 4.68143C17.8532 4.72869 17.9508 4.80871 18.0195 4.91139C18.0882 5.01407 18.1249 5.1348 18.125 5.25833V14.7417C18.1249 14.8652 18.0882 14.9859 18.0195 15.0886C17.9508 15.1913 17.8532 15.2713 17.7391 15.3186C17.625 15.3658 17.4994 15.3782 17.3782 15.3541C17.2571 15.3301 17.1457 15.2706 17.0583 15.1833L13.125 11.25M3.75 15.625H11.25C11.7473 15.625 12.2242 15.4275 12.5758 15.0758C12.9275 14.7242 13.125 14.2473 13.125 13.75V6.25C13.125 5.75272 12.9275 5.27581 12.5758 4.92417C12.2242 4.57254 11.7473 4.375 11.25 4.375H3.75C3.25272 4.375 2.77581 4.57254 2.42417 4.92417C2.07254 5.27581 1.875 5.75272 1.875 6.25V13.75C1.875 14.2473 2.07254 14.7242 2.42417 15.0758C2.77581 15.4275 3.25272 15.625 3.75 15.625Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo $img; ?>" data-fancybox="gallery" class="tf-btn primary">
                                    Fotoğrafları Gör
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $img; ?>" class="<?php echo $class; ?>" data-fancybox="gallery">
                            <img src="<?php echo $img; ?>" alt="img-gallery">
                        </a>
                    <?php endif; ?>
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
                            <div class="widget-sidebar fixed-sidebar">
                                <div class="widget-box widget-contact-agent">
                                    <h6 class="title fw-6">Danışman Bilgileri</h6>
                                    <div class="agent-info">
                                        <div class="avatar">
                                            <?php if(!empty($ilan['yonetici_gorsel'])): ?>
                                                <img src="admin/uploads/<?php echo $ilan['yonetici_gorsel']; ?>" alt="agent">
                                            <?php else: ?>
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ilan['yonetici_ad'] ?? 'Danışman'); ?>&background=random" alt="agent">
                                            <?php endif; ?>
                                        </div>
                                        <div class="content">
                                            <h6 class="fw-6"><?php echo htmlspecialchars($ilan['yonetici_ad'] ?? 'Maxwell Emlak'); ?></h6>
                                            <p class="text-variant-1">Profesyonel Gayrimenkul Danışmanı</p>
                                        </div>
                                    </div>
                                    <div class="contact-agent-info mt-4">
                                        <a href="tel:<?php echo $ilan['yonetici_tel']; ?>" class="tf-btn primary w-100 mb-2">
                                            <i class="icon icon-phone2"></i> <?php echo htmlspecialchars($ilan['yonetici_tel'] ?? $site_set['telefon']); ?>
                                        </a>
                                        <a href="mailto:<?php echo $ilan['yonetici_email']; ?>" class="tf-btn btn-line w-100">
                                            <i class="icon icon-mail"></i> <?php echo htmlspecialchars($ilan['yonetici_email'] ?? $site_set['email']); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
