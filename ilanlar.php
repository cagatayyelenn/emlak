<?php
// ilanlar.php
require_once __DIR__ . '/includes/db.php';

// Filtreleme mantığı
$where = [];
$params = [];

// Ana sayfa arama formundan gelenler
if (!empty($_GET['tip'])) {
    $where[] = "emlak_tipi = ?";
    $params[] = $_GET['tip'];
}

if (!empty($_GET['konum'])) {
    $where[] = "(il LIKE ? OR ilce LIKE ? OR mahalle LIKE ?)";
    $params[] = "%" . $_GET['konum'] . "%";
    $params[] = "%" . $_GET['konum'] . "%";
    $params[] = "%" . $_GET['konum'] . "%";
}

if (!empty($_GET['q'])) {
    $where[] = "(baslik LIKE ? OR aciklama LIKE ?)";
    $params[] = "%" . $_GET['q'] . "%";
    $params[] = "%" . $_GET['q'] . "%";
}

// Sidebardan gelebilecek ek filtreler (örnek)
if (!empty($_GET['durum'])) {
    $where[] = "durumu = ?";
    $params[] = $_GET['durum'];
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$ilan_stmt = $db->prepare("
    SELECT i.*, y.ad_soyad as yonetici_ad 
    FROM ilanlar i 
    LEFT JOIN portfoy_yoneticileri y ON i.portfoy_yoneticisi_id = y.id 
    $where_sql
    ORDER BY i.id DESC
");
$ilan_stmt->execute($params);
$ilanlar = $ilan_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

            <section class="flat-section flat-recommended flat-sidebar">
                <div class="container">
                    <div class="box-title-listing">
                        <div class="box-left">
                            <h3 class="fw-8">Gayrimenkul Listesi</h3>
                            <p class="text">Toplam <?php echo count($ilanlar); ?> sonuç bulundu.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="widget-sidebar fixed-sidebar">
                                <div class="flat-tab flat-tab-form widget-filter-search widget-box">
                                    <ul class="nav-tab-form" role="tablist">
                                        <li class="nav-tab-item" role="presentation">
                                            <a href="ilanlar.php?durum=Kiralık" class="nav-link-item <?php echo ($_GET['durum'] ?? '') == 'Kiralık' ? 'active' : ''; ?>">Kiralık</a>
                                        </li>
                                        <li class="nav-tab-item" role="presentation">
                                            <a href="ilanlar.php?durum=Satılık" class="nav-link-item <?php echo ($_GET['durum'] ?? '') == 'Satılık' ? 'active' : ''; ?>">Satılık</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content mt-4">
                                        <div class="tab-pane fade active show" role="tabpanel">
                                            <div class="form-sl">
                                                <form action="ilanlar.php" method="GET">
                                                    <div class="wd-filter-select">
                                                        <div class="inner-group">
                                                            <div class="box">
                                                                <div class="form-style mb-3">
                                                                    <input type="text" class="form-control" placeholder="Anahtar kelime..." name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                                                                </div>
                                                                <div class="form-style mb-3">
                                                                    <div class="group-ip ip-icon">
                                                                        <input type="text" class="form-control" placeholder="Konum" name="konum" value="<?php echo htmlspecialchars($_GET['konum'] ?? ''); ?>">
                                                                        <a href="#" class="icon-right icon-location"></a>
                                                                    </div>
                                                                </div>
                                                                <div class="form-style mb-3">
                                                                    <select name="tip" class="form-control">
                                                                        <option value="">Emlak Tipi</option>
                                                                        <option value="Daire" <?php echo ($_GET['tip'] ?? '') == 'Daire' ? 'selected' : ''; ?>>Daire</option>
                                                                        <option value="Villa" <?php echo ($_GET['tip'] ?? '') == 'Villa' ? 'selected' : ''; ?>>Villa</option>
                                                                        <option value="Arsa" <?php echo ($_GET['tip'] ?? '') == 'Arsa' ? 'selected' : ''; ?>>Arsa</option>
                                                                        <option value="İşyeri" <?php echo ($_GET['tip'] ?? '') == 'İşyeri' ? 'selected' : ''; ?>>İşyeri</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-style mt-4">
                                                                <button type="submit" class="tf-btn btn-view primary hover-btn-view">Filtrele <span class="icon icon-arrow-right2"></span></button>
                                                                <a href="ilanlar.php" class="btn btn-link mt-2 d-block text-center text-muted">Filtreleri Temizle</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-7 flat-animate-tab">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="gridLayout" role="tabpanel">
                                    <div class="row">
                                        <?php if(empty($ilanlar)): ?>
                                            <div class="col-12 text-center py-5">
                                                <i class="icon icon-search8 fa-4x text-muted mb-3 d-block"></i>
                                                <h5>Kriterlere uygun ilan bulunamadı.</h5>
                                                <p>Farklı bir arama yapmayı deneyin veya tüm ilanları görün.</p>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach($ilanlar as $ilan): ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="homelengo-box">
                                                    <div class="archive-top">
                                                        <a href="ilan-detay.php?id=<?php echo $ilan['id']; ?>" class="images-group">
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
                                                                <i class="icon icon-mapPin text-white me-1"></i>
                                                                <?php echo htmlspecialchars($ilan['il'] . ' / ' . $ilan['ilce']); ?>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="archive-bottom">
                                                        <div class="content-top">
                                                            <h6 class="text-capitalize"><a href="ilan-detay.php?id=<?php echo $ilan['id']; ?>" class="link"><?php echo htmlspecialchars($ilan['baslik']); ?></a></h6>
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
                            </div>
                        </div>
                    </div>
                </div>
            </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
