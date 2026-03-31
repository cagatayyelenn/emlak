<?php
// includes/footer.php
?>
            <footer class="footer">
                <div class="top-footer">
                    <div class="container">
                        <div class="content-footer-top">
                            <div class="footer-logo">
                                <a href="index.php">
                                    <?php if(!empty($site_set['logo_beyaz'])): ?>
                                        <img src="admin/uploads/settings/<?php echo $site_set['logo_beyaz']; ?>" alt="logo-footer" width="166" height="48">
                                    <?php elseif(!empty($site_set['logo'])): ?>
                                        <img src="admin/uploads/settings/<?php echo $site_set['logo']; ?>" alt="logo-footer" width="166" height="48">
                                    <?php else: ?>
                                        <img src="images/logo/logo@2x.png" alt="logo-footer" width="166" height="48">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="wd-social">
                                <span>Social</span>
                                <ul class="list-social d-flex align-items-center">
                                    <?php if(!empty($site_set['facebook'])): ?>
                                    <li><a href="<?php echo $site_set['facebook']; ?>"><i class="icon-facebook"></i></a></li>
                                    <?php endif; ?>
                                    <?php if(!empty($site_set['instagram'])): ?>
                                    <li><a href="<?php echo $site_set['instagram']; ?>"><i class="icon-instagram"></i></a></li>
                                    <?php endif; ?>
                                    <?php if(!empty($site_set['twitter'])): ?>
                                    <li><a href="<?php echo $site_set['twitter']; ?>"><i class="icon-twitter"></i></a></li>
                                    <?php endif; ?>
                                    <?php if(!empty($site_set['linkedin'])): ?>
                                    <li><a href="<?php echo $site_set['linkedin']; ?>"><i class="icon-linkedin2"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="footer-cl-1">
                                    <p class="text-variant-2">İhtiyaçlarınıza en uygun gayrimenkul çözümlerini sunuyoruz. Maxwell Emlak olarak güven ve profesyonellik ilkelerimizle yanınızdayız.</p>
                                    <ul class="mt-10">
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-mapPin"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_adres'] ?? ''); ?></p>
                                        </li>
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-phone2"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></p>
                                        </li>
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-mail"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?></p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-6">
                                <div class="footer-cl-2">
                                    <h6 class="text-white">Hızlı Menü</h6>
                                    <ul class="mt-10">
                                        <li><a href="index.php" class="text-variant-2">Anasayfa</a></li>
                                        <li><a href="ilanlar.php" class="text-variant-2">İlanlar</a></li>
                                        <li><a href="hakkimizda.php" class="text-variant-2">Hakkımızda</a></li>
                                        <li><a href="iletisim.php" class="text-variant-2">İletişim</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-6">
                                <div class="footer-cl-3">
                                    <h6 class="text-white">Yasal</h6>
                                    <ul class="mt-10">
                                        <li><a href="gizlilik-politikasi.php" class="text-variant-2">Gizlilik Politikası</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="footer-cl-4">
                                    <h6 class="text-white">İletişim Bilgileri</h6>
                                    <ul class="mt-10">
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-mapPin"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_adres'] ?? ''); ?></p>
                                        </li>
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-phone2"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></p>
                                        </li>
                                        <li class="mt-12 d-flex align-items-center gap-8">
                                            <i class="icon icon-mail"></i>
                                            <p class="text-variant-2"><?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?></p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottom-footer">
                    <div class="container">
                        <div class="content-footer-bottom">
                            <div class="copyright text-variant-2">©2024 Maxwell Emlak. Tüm hakları saklıdır.</div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Javascript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/swiper-bundle.min.js"></script>
    <script type="text/javascript" src="js/carousel.js"></script>
    <script type="text/javascript" src="js/plugin.js"></script>
    <script type="text/javascript" src="js/jquery.nice-select.min.js"></script>
    <script type="text/javascript" src="js/rangle-slider.js"></script>
    <script type="text/javascript" src="js/countto.js"></script>
    <script type="text/javascript" src="js/shortcodes.js"></script>
    <script type="text/javascript" src="js/animation_heading.js"></script>
    <script type="text/javascript" src="js/lazysize.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script>
        $(document).ready(function() {
            $('[data-fancybox="gallery"]').fancybox({
                loop: true,
                buttons: [
                    "zoom",
                    "slideShow",
                    "fullScreen",
                    "download",
                    "thumbs",
                    "close"
                ],
                animationEffect: "zoom-in-out"
            });
        });
    </script>

</body>

</html>
