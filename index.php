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

            <!-- Branding Section -->
            <style>
                .flat-section-branding { padding: 100px 0; background: #fff; overflow: hidden; }
                .branding-title { font-size: 48px; font-weight: 400; color: #999; margin-bottom: 60px; text-align: center; font-family: 'Inter', sans-serif; }
                .branding-title b { color: #1a1a1a; font-weight: 800; }
                
                .chevron-container { display: flex; justify-content: center; width: 100%; max-width: 1200px; margin: 0 auto; gap: 10px; }
                .chevron-item { position: relative; width: 25%; height: 350px; overflow: hidden; transition: all 0.5s ease; }
                .chevron-item img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(100%); transition: filter 0.5s ease, transform 0.5s ease; }
                .chevron-item:hover img { filter: grayscale(0%); transform: scale(1.1); }
                
                /* Chevron Shapes using clip-path */
                .chevron-item { clip-path: polygon(15% 0, 70% 0, 100% 50%, 70% 100%, 15% 100%, 50% 50%); margin-left: -8%; }
                .chevron-item:first-child { margin-left: 0; }

                .branding-footer-text { max-width: 800px; margin: 60px auto 0; text-align: center; font-size: 24px; line-height: 1.5; color: #1a1a1a; font-weight: 600; }
                .branding-footer-text .highlight { color: #999; font-weight: 500; }
                .branding-footer-text b { font-weight: 800; }

                @media (max-width: 991px) {
                    .chevron-container { flex-direction: column; gap: 20px; height: auto; }
                    .chevron-item { width: 100%; height: 250px; margin-left: 0 !important; clip-path: none !important; border-radius: 20px; }
                    .branding-title { font-size: 32px; }
                    .branding-footer-text { font-size: 18px; padding: 0 20px; }
                }
            </style>

            <section class="flat-section-branding">
                <div class="container">
                    <h2 class="branding-title wow fadeInUp">Bu sadece gayrimenkulle ilgili <b>değil .</b></h2>
                    
                    <div class="chevron-container wow fadeInUp" data-wow-delay=".2s">
                        <div class="chevron-item cv-1">
                            <img src="images/branding/woman.png" alt="Identity">
                        </div>
                        <div class="chevron-item cv-2">
                            <img src="images/branding/bedroom.png" alt="Progress">
                        </div>
                        <div class="chevron-item cv-3">
                            <img src="images/branding/living_room.png" alt="Freedom">
                        </div>
                        <div class="chevron-item cv-4">
                            <img src="images/branding/man.png" alt="Alignment">
                        </div>
                    </div>

                    <div class="branding-footer-text wow fadeInUp" data-wow-delay=".4s">
                        Bu, kimlikle ilgili. İlerlemeyle ilgili. Sıkışıp kaldığınız yerden kurtulmakla ilgili. Sadece bir yer aramıyorsunuz. <span class="highlight">Uyum arıyorsunuz. Biz de size bunu bulmanızda yardımcı oluyoruz.</span>
                    </div>
                </div>
            </section>

            <!-- Footer Branding Section & Replacement -->
            <style>
                .footer_wrapper__9GQwi { background-color: #141414; padding: 80px 0 40px; color: #fff; font-family: 'Inter', sans-serif; }
                .footer_newsletter-container__POI_T { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 60px; flex-wrap: wrap; gap: 40px; }
                .footer_newsletter-title__bRCRZ { font-size: 32px; font-weight: 800; margin-bottom: 24px; }
                .footer_input-container__K2c_A { display: flex; border-bottom: 1px solid #333; padding-bottom: 8px; max-width: 450px; width: 100%; transition: border-color 0.3s; }
                .footer_input-container__K2c_A:focus-within { border-color: #fff; }
                .text-input_input__cs4B0 { background: transparent; border: none; color: #fff; font-size: 18px; width: 100%; outline: none; }
                .footer_newsletter-submit-btn__HrC3v { background: transparent; border: none; color: #fff; width: 24px; cursor: pointer; }
                
                .footer_contacts__HFiAl { display: flex; gap: 40px; }
                .footer_contact-label__gYKsP { font-size: 11px; text-transform: uppercase; color: #7c818b; margin-bottom: 8px; font-weight: 600; }
                .footer_contact-value__e1jbK a { color: #fff; text-decoration: none; font-weight: 700; font-size: 15px; }
                
                .footer_links__vib46 { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 60px; }
                .footer_nav__XkBHY { display: flex; flex-direction: column; }
                .footer_nav-link__LFUNG { font-size: 48px; font-weight: 900; color: #fff; text-decoration: none; line-height: 1.1; transition: opacity 0.3s; }
                .footer_nav-link__LFUNG:hover { opacity: 0.6; }
                .footer_socials__4JfcA { display: flex; flex-direction: column; gap: 8px; text-align: right; }
                .footer_social-link__2uQBq { color: #fff; text-decoration: none; font-weight: 700; font-size: 16px; transition: opacity 0.3s; }
                .footer_social-link__2uQBq:hover { opacity: 0.6; }

                .footer_logo__5ncK8 { margin: 40px 0; width: 100%; text-align: center; overflow: hidden; }
                .huge-typography-logo { 
                    font-size: 18vw; 
                    font-weight: 900; 
                    color: #fff; 
                    line-height: 0.8; 
                    letter-spacing: -0.05em; 
                    margin-bottom: -0.1em;
                    transform: scaleY(1.1);
                    display: inline-block;
                    user-select: none;
                }

                .footer_copyright-container__yt1ht { 
                    display: flex; 
                    justify-content: space-between; 
                    align-items: center; 
                    padding-top: 30px; 
                    border-top: 1px solid #222; 
                    font-size: 12px; 
                    color: #7c818b;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .footer_sublinks__Pj_ed { display: flex; gap: 15px; flex-wrap: wrap; }
                .footer_sublinks__Pj_ed a { color: #7c818b; text-decoration: none; }
                .footer_sublinks__Pj_ed a:hover { color: #fff; }

                @media (max-width: 991px) {
                    .footer_nav-link__LFUNG { font-size: 32px; }
                    .footer_links__vib46 { flex-direction: column; align-items: flex-start; gap: 30px; }
                    .footer_socials__4JfcA { text-align: left; }
                    .footer_copyright-container__yt1ht { flex-direction: column; align-items: flex-start; }
                }
            </style>

<?php require_once __DIR__ . '/includes/footer_new.php'; ?>
