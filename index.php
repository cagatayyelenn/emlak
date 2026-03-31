<?php
// index.php
require_once __DIR__ . '/includes/db.php';

// Öne çıkan ilanları çek (Son eklenen 6 ilan)
$ilan_stmt = $db->query("
    SELECT i.*, y.ad_soyad as yonetici_ad 
    FROM ilanlar i 
    LEFT JOIN portfoy_yoneticileri y ON i.portfoy_yoneticisi_id = y.id 
    ORDER BY i.id DESC 
    LIMIT 6
");
$ilanlar = $ilan_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

            <!-- Slider -->
            <section class="flat-slider home-1">
                <div class="container relative">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="slider-content">
                                <div class="heading text-center">
                                    <h1 class="title-large text-white animationtext slide">
                                        Hayalinizdeki
                                        <span class="tf-text s1 cd-words-wrapper">
                                            <span class="item-text is-visible">Evi Bulun</span>
                                            <span class="item-text is-hidden">Yaşamı Seçin</span>
                                        </span>
                                    </h1>
                                    <p class="subtitle text-white body-2 wow fadeInUp" data-wow-delay=".2s">
                                        <?php echo htmlspecialchars($site_set['site_aciklama']); ?>
                                    </p>
                                </div>
                                <div class="flat-tab flat-tab-form">
                                    <ul class="nav-tab-form style-1 justify-content-center" role="tablist">
                                        <li class="nav-tab-item" role="presentation">
                                            <a href="#forSale" class="nav-link-item active" data-bs-toggle="tab">Satılık</a>
                                        </li>
                                        <li class="nav-tab-item" role="presentation">
                                            <a href="#forRent" class="nav-link-item" data-bs-toggle="tab">Kiralık</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" role="tabpanel">
                                            <div class="form-sl">
                                                <form action="ilanlar.php" method="GET">
                                                    <div class="wd-find-select">
                                                        <div class="inner-group">
                                                            <div class="form-group-1 search-form form-style">
                                                                <label>Tür</label>
                                                                <div class="group-select">
                                                                    <select name="tip" class="select_js">
                                                                        <option value="">Tümü</option>
                                                                        <option value="Daire">Daire</option>
                                                                        <option value="Villa">Villa</option>
                                                                        <option value="Arsa">Arsa</option>
                                                                        <option value="İşyeri">İşyeri</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group-2 form-style">
                                                                <label>Konum</label>
                                                                <div class="group-ip">
                                                                    <input type="text" class="form-control" placeholder="İl, İlçe veya Mahalle" name="konum">
                                                                    <a href="#" class="icon icon-location"></a>
                                                                </div>
                                                            </div>
                                                            <div class="form-group-3 form-style">
                                                                <label>Anahtar Kelime</label>
                                                                <input type="text" class="form-control" placeholder="Havuzlu, Bahçeli vb." name="q">
                                                            </div>
                                                        </div>
                                                        <div class="box-btn-advanced">
                                                            <button type="submit" class="tf-btn btn-search primary">Ara <i class="icon icon-search"></i> </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overlay"></div>
            </section>
            <!-- End Slider -->

            <!-- Recommended -->
            <section class="flat-section flat-recommended">
                <div class="container">
                    <div class="box-title text-center wow fadeInUp">
                        <div class="text-subtitle text-primary">Öne Çıkan Gayrimenkuller</div>
                        <h3 class="mt-4 title">Sizin İçin Önerilenler</h3>
                    </div>
                    
                    <div class="row mt-5">
                        <?php if(empty($ilanlar)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Henüz ilan eklenmemiş.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($ilanlar as $ilan): ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="homelengo-box">
                                    <div class="archive-top">
                                        <a href="ilan/<?php echo $ilan['slug']; ?>" class="images-group">
                                            <div class="images-style">
                                                <?php if(!empty($ilan['vitrin_gorseli'])): ?>
                                                    <img src="admin/uploads/images/<?php echo $ilan['vitrin_gorseli']; ?>" alt="img" class="lazyload">
                                                <?php else: ?>
                                                    <img src="images/home/house-1.jpg" alt="img" class="lazyload">
                                                <?php endif; ?>
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-6">
                                                    <li class="flag-tag primary"><?php echo htmlspecialchars($ilan['durumu']); ?></li>
                                                    <li class="flag-tag style-1"><?php echo htmlspecialchars($ilan['emlak_tipi']); ?></li>
                                                </ul>
                                            </div>
                                            <div class="bottom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 7C10 7.53043 9.78929 8.03914 9.41421 8.41421C9.03914 8.78929 8.53043 9 8 9C7.46957 9 6.96086 8.78929 6.58579 8.41421C6.21071 8.03914 6 7.53043 6 7C6 6.46957 6.21071 5.96086 6.58579 5.58579C6.96086 5.21071 7.46957 5 8 5C8.53043 5 9.03914 5.21071 9.41421 5.58579C9.78929 5.96086 10 6.46957 10 7Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M13 7C13 11.7613 8 14.5 8 14.5C8 14.5 3 11.7613 3 7C3 5.67392 3.52678 4.40215 4.46447 3.46447C5.40215 2.52678 6.67392 2 8 2C9.32608 2 10.5979 2.52678 11.5355 3.46447C12.4732 4.40215 13 5.67392 13 7Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <?php echo htmlspecialchars($ilan['il'] . ' / ' . $ilan['ilce']); ?>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="archive-bottom">
                                        <div class="content-top">
                                            <h6 class="text-capitalize"><a href="ilan/<?php echo $ilan['slug']; ?>" class="link"><?php echo htmlspecialchars($ilan['baslik']); ?></a></h6>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span class="text-variant-1">Oda:</span>
                                                    <span class="fw-6"><?php echo $ilan['oda_sayisi']; ?></span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-bath"></i>
                                                    <span class="text-variant-1">Banyo:</span>
                                                    <span class="fw-6"><?php echo $ilan['banyo_sayisi']; ?></span>
                                                </li>
                                                <li class="item">
                                                    <i class="icon icon-sqft"></i>
                                                    <span class="text-variant-1">m²:</span>
                                                    <span class="fw-6"><?php echo $ilan['m2_brut']; ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="content-bottom">
                                            <div class="d-flex gap-8 align-items-center">
                                                <div class="avatar avt-40 round">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ilan['yonetici_ad'] ?? 'Emlak'); ?>&background=random" alt="avt">
                                                </div>
                                                <span><?php echo htmlspecialchars($ilan['yonetici_ad'] ?? 'Maxwell Emlak'); ?></span>
                                            </div>
                                            <h6 class="price"><?php echo number_format($ilan['fiyat'], 0, ',', '.'); ?> ₺</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <!-- End Recommended -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
