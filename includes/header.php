<?php
// includes/header.php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="tr" lang="tr">

<head>
    <meta charset="utf-8">
    <base href="/">
    <title><?php echo htmlspecialchars($site_set['site_baslik']); ?></title>
    <meta name="keywords" content="<?php echo htmlspecialchars($site_set['site_anahtar_kelimeler']); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($site_set['site_aciklama']); ?>">
    <meta name="author" content="Maxwell Emlak">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- font -->
    <link rel="stylesheet" href="fonts/fonts.css">
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!-- Favicon and Touch Icons  -->
    <?php if(!empty($site_set['favicon'])): ?>
    <link rel="shortcut icon" href="admin/uploads/settings/<?php echo $site_set['favicon']; ?>">
    <link rel="apple-touch-icon-precomposed" href="admin/uploads/settings/<?php echo $site_set['favicon']; ?>">
    <?php else: ?>
    <link rel="shortcut icon" href="images/logo/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="images/logo/favicon.png">
    <?php endif; ?>

    <!-- Google Search Console & Analytics -->
    <?php echo $site_set['google_search_console'] ?? ''; ?>
    <?php echo $site_set['google_analytics'] ?? ''; ?>

</head>

<body class="body">

    <!-- preload -->
    <div class="preload preload-container">
        <div class="preload-logo">
            <div class="spinner"></div>
            <span class="icon icon-villa-fill"></span>
        </div>
    </div>
    <!-- /preload -->


    <div id="wrapper">
        <div id="pagee" class="clearfix">

            <!-- Main Header -->
            <header id="header" class="main-header header-fixed fixed-header">
                <!-- Header Lower -->
                <div class="header-lower">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="inner-header">
                                <div class="inner-header-left">
                                    <div class="logo-box flex">
                                        <div class="logo">
                                            <a href="index.php">
                                                <?php if(!empty($site_set['logo'])): ?>
                                                    <img src="admin/uploads/settings/<?php echo $site_set['logo']; ?>" alt="logo" width="166" height="48">
                                                <?php else: ?>
                                                    <img src="images/logo/logo@2x.png" alt="logo" width="166" height="48">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="nav-outer flex align-center">
                                    <!-- Main Menu -->
                                    <nav class="main-menu show navbar-expand-md">
                                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                            <ul class="navigation clearfix">
                                                <li class="home"><a href="index.php">Anasayfa</a></li>
                                                <li><a href="ilanlar.php">İlanlar</a></li>
                                                <li><a href="hakkimizda.php">Hakkımızda</a></li>
                                                <li><a href="iletisim.php">İletişim</a></li>
                                            </ul>
                                        </div>
                                    </nav>
                                    <!-- Main Menu End-->
                                </div>
                                <div class="inner-header-right header-account">
                                    <div class="mobi-icon-box" style="margin-right: 20px;">
                                        <div class="box d-flex align-items-center">
                                            <span class="icon icon-phone2"></span>
                                            <div style="font-weight: 600; margin-left: 8px;"><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="flat-bt-top">
                                        <?php if(!empty($site_set['sahibinden_url'])): ?>
                                            <a class="tf-btn primary" href="<?php echo $site_set['sahibinden_url']; ?>" target="_blank">
                                                Sahibinden.com
                                            </a>
                                        <?php else: ?>
                                            <a class="tf-btn primary" href="admin/login.php">
                                                Yönetim Paneli
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mobile-nav-toggler mobile-button"><span></span></div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Lower -->

                <!-- Mobile Menu  -->
                <div class="close-btn"><span class="icon flaticon-cancel-1"></span></div>
                <div class="mobile-menu">
                    <div class="menu-backdrop"></div>
                    <nav class="menu-box">
                        <div class="nav-logo">
                            <a href="index.php">
                                <?php if(!empty($site_set['logo'])): ?>
                                    <img src="admin/uploads/settings/<?php echo $site_set['logo']; ?>" alt="nav-logo" width="174" height="44">
                                <?php else: ?>
                                    <img src="images/logo/logo@2x.png" alt="nav-logo" width="174" height="44">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="bottom-canvas">
                            <div class="menu-outer"></div>
                            <div class="mobi-icon-box">
                                <div class="box d-flex align-items-center">
                                    <span class="icon icon-phone2"></span>
                                    <div><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></div>
                                </div>
                                <div class="box d-flex align-items-center">
                                    <span class="icon icon-mail"></span>
                                    <div><?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
                <!-- End Mobile Menu -->

            </header>
            <!-- End Main Header -->
