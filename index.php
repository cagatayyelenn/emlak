<?php
// index.php
require_once __DIR__ . '/includes/db.php';

// Öne çıkan ilanları çek (Son eklenen 12 ilan - Sekmeler için daha fazla veri)
$ilan_stmt = $db->query("
    SELECT i.*, y.ad_soyad as yonetici_ad 
    FROM ilanlar i 
    LEFT JOIN portfoy_yoneticileri y ON i.portfoy_yoneticisi_id = y.id 
    WHERE i.yayin_durumu = 'Aktif'
    ORDER BY i.id DESC 
    LIMIT 12
");
$ilanlar = $ilan_stmt->fetchAll(PDO::FETCH_ASSOC);

// Mevcut emlak tiplerini çek (Sadece ilanı olanlar)
$tipler_stmt = $db->query("SELECT DISTINCT emlak_tipi FROM ilanlar WHERE yayin_durumu = 'Aktif' AND emlak_tipi IS NOT NULL AND emlak_tipi != ''");
$emlak_tipleri = $tipler_stmt->fetchAll(PDO::FETCH_COLUMN);

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
                                                        <input type="text" class="form-control"
                                                            placeholder="İl, İlçe veya Mahalle" name="konum">
                                                        <a href="#" class="icon icon-location"></a>
                                                    </div>
                                                </div>
                                                <div class="form-group-3 form-style">
                                                    <label>Anahtar Kelime</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Havuzlu, Bahçeli vb." name="q">
                                                </div>
                                            </div>
                                            <div class="box-btn-advanced">
                                                <button type="submit" class="tf-btn btn-search primary">Ara <i
                                                        class="icon icon-search"></i> </button>
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

        <!-- Filter Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="tab-filter-container d-flex justify-content-center flex-wrap gap-12">
                    <button class="tab-filter-item active" data-filter="all">Tümü</button>
                    <?php foreach ($emlak_tipleri as $tip): ?>
                        <button class="tab-filter-item"
                            data-filter="<?php echo htmlspecialchars($tip); ?>"><?php echo htmlspecialchars($tip); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row mt-4" id="property-list">
            <?php if (empty($ilanlar)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Henüz ilan eklenmemiş.</p>
                </div>
            <?php else: ?>
                <?php foreach ($ilanlar as $ilan): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4 property-card-item"
                        data-type="<?php echo htmlspecialchars($ilan['emlak_tipi']); ?>">
                        <div class="homelengo-box">
                            <div class="archive-top">
                                <a href="ilan/<?php echo $ilan['slug']; ?>" class="images-group">
                                    <div class="images-style">
                                        <?php if (!empty($ilan['vitrin_gorseli'])): ?>
                                            <img src="admin/uploads/images/<?php echo $ilan['vitrin_gorseli']; ?>" alt="img"
                                                class="lazyload">
                                        <?php else: ?>
                                            <img src="images/home/house-1.jpg" alt="img" class="lazyload">
                                        <?php endif; ?>
                                    </div>
                                    <div class="top">
                                        <ul class="d-flex gap-6">
                                            <li class="flag-tag primary"><?php echo htmlspecialchars($ilan['durumu']); ?></li>
                                            <li class="flag-tag style-1"><?php echo htmlspecialchars($ilan['emlak_tipi']); ?>
                                            </li>
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
                                        <?php echo htmlspecialchars($ilan['il'] . ' / ' . $ilan['ilce']); ?>
                                    </div>
                                </a>
                            </div>
                            <div class="archive-bottom">
                                <div class="content-top">
                                    <h6 class="text-capitalize"><a href="ilan/<?php echo $ilan['slug']; ?>"
                                            class="link"><?php echo htmlspecialchars($ilan['baslik']); ?></a></h6>
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
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ilan['yonetici_ad'] ?? 'Emlak'); ?>&background=random"
                                                alt="avt">
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

<!-- Service  -->
<section class="flat-section">
    <div class="container">
        <div class="box-title text-center wow fadeInUp">
            <div class="text-subtitle text-primary">Hizmetlerimiz</div>
            <h3 class="mt-4 title">WaxWell Olarak Ne yapıyoruz?</h3>
        </div>
        <div class="tf-grid-layout md-col-3 wow fadeInUp" data-wow-delay=".2s">
            <div class="box-service">
                <div class="image">
                    <img class="lazyload" data-src="images/service/home-1.png" src="images/service/home-1.png"
                        alt="image-location">
                </div>
                <div class="content">
                    <h5 class="title">Yeni bir ev satın alın veya Kiralayın</h5>
                    <p class="description">Hayalinizdeki eve zahmetsizce ulaşın. Sorunsuz bir satın almaveya kiralama
                        deneyimi için çeşitli gayrimenkulleri ve uzman rehberliğini inceleyin.</p>
                </div>
            </div>
            <div class="box-service">
                <div class="image">
                    <img class="lazyload" data-src="images/service/home-2.png" src="images/service/home-2.png"
                        alt="image-location">
                </div>
                <div class="content">
                    <h5 class="title">Evinizi Satın</h5>
                    <p class="description">Uzman rehberliği ve etkili stratejilerle güvenle satış yapın, mülkünüzün en
                        iyi özelliklerini başarılı bir satış için sergileyin.</p>
                </div>
            </div>
            <div class="box-service">
                <div class="image">
                    <img class="lazyload" data-src="images/service/home-3.png" src="images/service/home-3.png"
                        alt="image-location">
                </div>
                <div class="content">
                    <h5 class="title">Evinizi Kiralayın</h5>
                    <p class="description">Eşsiz yaşam tarzı ihtiyaçlarınıza tam olarak uyacak şekilde tasarlanmış
                        çeşitli ilanları inceleyin.</p>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- End Service -->


<!-- Branding Section -->
<style>
    .flat-section-branding {
        padding: 100px 0;
        background: #fff;
        overflow: hidden;
    }

    .branding-title {
        font-size: 48px;
        font-weight: 400;
        color: #999;
        margin-bottom: 60px;
        text-align: center;
        font-family: 'Inter', sans-serif;
    }

    .branding-title b {
        color: #1a1a1a;
        font-weight: 800;
    }

    .chevron-container {
        display: flex;
        justify-content: center;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        gap: 10px;
    }

    .chevron-item {
        position: relative;
        width: 25%;
        height: 350px;
        overflow: hidden;
        transition: all 0.5s ease;
    }

    .chevron-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(100%);
        transition: filter 0.5s ease, transform 0.5s ease;
    }

    .chevron-item:hover img {
        filter: grayscale(0%);
        transform: scale(1.1);
    }

    /* Chevron Shapes using clip-path */
    .chevron-item {
        clip-path: polygon(15% 0, 70% 0, 100% 50%, 70% 100%, 15% 100%, 50% 50%);
        margin-left: -8%;
    }

    .chevron-item:first-child {
        margin-left: 0;
    }

    .branding-footer-text {
        max-width: 800px;
        margin: 60px auto 0;
        text-align: center;
        font-size: 24px;
        line-height: 1.5;
        color: #1a1a1a;
        font-weight: 600;
    }

    .branding-footer-text .highlight {
        color: #999;
        font-weight: 500;
    }

    .branding-footer-text b {
        font-weight: 800;
    }

    @media (max-width: 991px) {
        .chevron-container {
            flex-direction: column;
            gap: 20px;
            height: auto;
        }

        .chevron-item {
            width: 100%;
            height: 250px;
            margin-left: 0 !important;
            clip-path: none !important;
            border-radius: 20px;
        }

        .branding-title {
            font-size: 32px;
        }

        .branding-footer-text {
            font-size: 18px;
            padding: 0 20px;
        }
    }

    /* Tab Filter Styles */
    .tab-filter-container {
        margin-bottom: 20px;
    }

    .tab-filter-item {
        border: none;
        background: #f7f7f7;
        color: #1a1a1a;
        padding: 12px 28px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .tab-filter-item:hover {
        background: #eee;
    }

    .tab-filter-item.active {
        background: #4361ee;
        color: #fff;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .property-card-item {
        transition: all 0.4s ease;
        display: block;
    }

    /* JS filters with display: none for better layout management instead of just opacity */
    /* Partner Logo Styles */
    .partner-item {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 80px;
        padding: 10px;
    }

    .partner-item img {
        max-height: 50px;
        max-width: 100%;
        width: auto;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.4s ease;
    }

    .partner-item:hover img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
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
            Bu, kimlikle ilgili. İlerlemeyle ilgili. Sıkışıp kaldığınız yerden kurtulmakla ilgili. Sadece bir yer
            aramıyorsunuz. <span class="highlight">Uyum arıyorsunuz. Biz de size bunu bulmanızda yardımcı
                oluyoruz.</span>
        </div>
    </div>
</section>
<section class="flat-section flat-agents">
    <div class="container">
        <div class="box-title text-center wow fadeInUp">

            <h3 class="title mt-4">Danışman Ekibimiz</h3>
        </div>
        <div dir="ltr" class="swiper tf-sw-mobile-1" data-screen="575" data-preview="1" data-space="15">
            <div class="tf-layout-mobile-sm xl-col-4 sm-col-2 swiper-wrapper">
                <div class="swiper-slide">
                    <div class="box-agent hover-img wow fadeInUp" data-wow-delay=".2s">
                        <a href="#" class="box-img img-style">
                            <img class="lazyload" data-src="images/agents/agent-1.jpg" src="images/agents/agent-1.jpg"
                                alt="image-agent">
                            <ul class="agent-social">
                                <li><span class="icon icon-facebook"></span></li>
                                <li><span class="icon icon-x"></span></li>
                                <li><span class="icon icon-linkedin"></span></li>
                                <li><span class="icon icon-instargram"></span></li>
                            </ul>
                        </a>
                        <div class="content">
                            <div class="info">
                                <h5><a class="link" href="#">Chris Patt</a></h5>
                                <p class="text-variant-1">Administrative Staff</p>
                            </div>
                            <div class="box-icon">
                                <span class="icon icon-phone"></span>
                                <span class="icon icon-mail"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="box-agent hover-img wow fadeInUp" data-wow-delay=".3s">
                        <a href="#" class="box-img img-style">
                            <img class="lazyload" data-src="images/agents/agent-2.jpg" src="images/agents/agent-2.jpg"
                                alt="image-agent">
                            <ul class="agent-social">
                                <li><span class="icon icon-facebook"></span></li>
                                <li><span class="icon icon-x"></span></li>
                                <li><span class="icon icon-linkedin"></span></li>
                                <li><span class="icon icon-instargram"></span></li>
                            </ul>
                        </a>
                        <div class="content">
                            <div class="info">
                                <h5><a class="link" href="#">Esther Howard</a></h5>
                                <p class="text-variant-1">Administrative Staff</p>
                            </div>
                            <div class="box-icon">
                                <span class="icon icon-phone"></span>
                                <span class="icon icon-mail"></span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="box-agent hover-img wow fadeInUp" data-wow-delay=".4s">
                        <a href="#" class="box-img img-style">
                            <img class="lazyload" data-src="images/agents/agent-3.jpg" src="images/agents/agent-3.jpg"
                                alt="image-agent">
                            <ul class="agent-social">
                                <li><span class="icon icon-facebook"></span></li>
                                <li><span class="icon icon-x"></span></li>
                                <li><span class="icon icon-linkedin"></span></li>
                                <li><span class="icon icon-instargram"></span></li>
                            </ul>
                        </a>
                        <div class="content">
                            <div class="info">
                                <h5><a class="link" href="#">Darrell Steward</a></h5>
                                <p class="text-variant-1">Administrative Staff</p>
                            </div>
                            <div class="box-icon">
                                <span class="icon icon-phone"></span>
                                <span class="icon icon-mail"></span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="box-agent hover-img wow fadeInUp" data-wow-delay=".5s">
                        <a href="#" class="box-img img-style">
                            <img class="lazyload" data-src="images/agents/agent-4.jpg" src="images/agents/agent-4.jpg"
                                alt="image-agent">
                            <ul class="agent-social">
                                <li><span class="icon icon-facebook"></span></li>
                                <li><span class="icon icon-x"></span></li>
                                <li><span class="icon icon-linkedin"></span></li>
                                <li><span class="icon icon-instargram"></span></li>
                            </ul>
                        </a>
                        <div class="content">
                            <div class="info">
                                <h5><a class="link" href="#"> Robert Fox</a></h5>
                                <p class="text-variant-1">Administrative Staff</p>
                            </div>
                            <div class="box-icon">
                                <span class="icon icon-phone"></span>
                                <span class="icon icon-mail"></span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sw-pagination sw-pagination-mb-1 text-center d-sm-none d-block"></div>
        </div>

    </div>
</section>
<!-- End Agents -->
<!-- partner -->
<section class="flat-section pt-0">
    <div class="container2">
        <h6 class="mb-20 text-center text-capitalize text-black-4">Kurumsal Emlak Firmaları Üyesiyiz</h6>
        <div dir="ltr" class="swiper tf-sw-partner" data-preview="3" data-tablet="3" data-mobile-sm="2" data-mobile="2"
            data-space="15" data-space-md="30" data-space-lg="30">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="partner-item">
                        <img src="images/sahibinden_logo.png" alt="sahibinden">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="partner-item">
                        <img src="images/hepsiemlak-logo.svg" alt="hepsiemlak">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="partner-item">
                        <img src="images/emlakjet_logo.svg" alt="emlakjet">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.tab-filter-item');
        const propertyCards = document.querySelectorAll('.property-card-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Buton aktiflik durumunu güncelle
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');

                // Kartları filtrele
                propertyCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');

                    if (filterValue === 'all' || cardType === filterValue) {
                        card.style.display = 'block';
                        // Küçük bir gecikme ile opaklık animasyonu verilebilir
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 400); // CSS transition süresiyle uyumlu
                    }
                });
            });
        });
    });
</script>