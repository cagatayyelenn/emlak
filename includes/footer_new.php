<?php
// includes/footer_new.php - Modern & Minimalist Redesign
?>
<footer class="footer-new">
    <div class="container">
        <div class="row">
            <!-- Newsletter & Contact -->
            <div class="col-lg-7">
                <div class="newsletter-section mb-5">
                    <h3 class="newsletter-title mb-4">Haber bültenimize abone olun!</h3>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Adresinizi girin" class="newsletter-input">
                        <button type="submit" class="newsletter-btn"><i class="icon-arrow-right"></i></button>
                    </form>
                </div>

                <div class="contact-grid row mt-5">
                    <div class="col-md-4 mb-4">
                        <span class="contact-label d-block text-muted small mb-2 uppercase">Merkez</span>
                        <p class="contact-value fw-bold"><?php echo htmlspecialchars($site_set['iletisim_adres'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <span class="contact-label d-block text-muted small mb-2 uppercase">Bize e-posta gönderin</span>
                        <p class="contact-value fw-bold"><?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <span class="contact-label d-block text-muted small mb-2 uppercase">Bizi Arayın</span>
                        <p class="contact-value fw-bold"><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></p>
                    </div>
                </div>
            </div>

            <!-- Menus -->
            <div class="col-lg-3 col-md-6 mb-4">
                <ul class="footer-menu-list p-0 m-0" style="list-style: none;">
                    <li><a href="ilanlar.php" class="footer-menu-link">Aramak</a></li>
                    <li><a href="danismanlar.php" class="footer-menu-link">Ajanlar</a></li>
                    <li><a href="hakkimizda.php" class="footer-menu-link">Hakkımızda</a></li>
                    <li><a href="iletisim.php" class="footer-menu-link">Katılmak</a></li>
                </ul>
            </div>

            <!-- Socials -->
            <div class="col-lg-2 col-md-6 mb-4">
                <ul class="footer-social-list p-0 m-0" style="list-style: none;">
                    <?php if(!empty($site_set['facebook'])): ?>
                    <li><a href="<?php echo $site_set['facebook']; ?>" class="footer-social-link">Facebook</a></li>
                    <?php endif; ?>
                    <?php if(!empty($site_set['instagram'])): ?>
                    <li><a href="<?php echo $site_set['instagram']; ?>" class="footer-social-link">Instagram</a></li>
                    <?php endif; ?>
                    <?php if(!empty($site_set['twitter'])): ?>
                    <li><a href="<?php echo $site_set['twitter']; ?>" class="footer-social-link">Twitter</a></li>
                    <?php endif; ?>
                    <?php if(!empty($site_set['linkedin'])): ?>
                    <li><a href="<?php echo $site_set['linkedin']; ?>" class="footer-social-link">LinkedIn</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Huge Branding Logo -->
        <div class="huge-logo-container mt-5">
            <img src="images/branding/huge_logo.png" alt="MAXWELL" class="huge-footer-logo">
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom-bar d-flex justify-content-between align-items-center mt-5 pt-4 border-top border-secondary">
            <div class="legal-links small text-muted">
                <a href="#" class="me-3">Şartlar</a>
                <a href="#" class="me-3">Gizlilik politikası</a>
                <a href="#" class="me-3">Adil Konut Bildirimi</a>
                <a href="#" class="me-3">Çalışma Prosedürü</a>
                <a href="#">Telif hakkı © <?php echo date('Y'); ?></a>
            </div>
            <div class="footer-credits small text-muted">
                Emlak Bul
            </div>
        </div>
    </div>
</footer>

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
