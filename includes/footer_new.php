<?php
// includes/footer_new.php - Orijinal "Find Real Estate" Yapısı
?>
<div class="footer_wrapper__9GQwi">
    <div class="container_container__v5gtR text-white">
        <div class="footer_content__E2ijt">
            <div class="footer_newsletter-container__POI_T">
                <div>
                    <div class="footer_newsletter-title__bRCRZ">
                        Haber bültenimize abone olun!
                    </div>
                    <div class="footer_newsletter-form__0k_h5">
                        <form>
                            <div class="footer_input-container__K2c_A">
                                <div class="form-text-input_form-input__5AJnT">
                                    <div class="text-input_input-wrapper__ia6GQ form-text-input_input-wrapper__Aw_YD footer_input-wrapper__1l5CZ text-input_dark__c1u8L">
                                        <input class="text-input_input__cs4B0" placeholder="Adresinizi girin" autocomplete="on" type="email" name="email">
                                    </div>
                                </div>
                                <button id="btn_newsletter_signup_footer" type="submit" class="footer_newsletter-submit-btn__HrC3v">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="m20.78 12.531-6.75 6.75a.75.75 0 1 1-1.06-1.061l5.47-5.47H3.75a.75.75 0 1 1 0-1.5h14.69l-5.47-5.469a.75.75 0 1 1 1.06-1.061l6.75 6.75a.75.75 0 0 1 0 1.061"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="footer_contacts__HFiAl">
                    <div data-contact="address" class="footer_contact__fFxbr">
                        <div class="footer_contact-label__gYKsP">Merkez</div>
                        <div class="footer_contact-value__e1jbK">
                            <a href="#">
                                <div><?php echo htmlspecialchars($site_set['iletisim_adres'] ?? ''); ?></div>
                            </a>
                        </div>
                    </div>
                    <div data-contact="email" class="footer_contact__fFxbr">
                        <div class="footer_contact-label__gYKsP">Bize e-posta gönderin</div>
                        <div class="footer_contact-value__e1jbK">
                            <a href="mailto:<?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?>">
                                <?php echo htmlspecialchars($site_set['iletisim_eposta'] ?? ''); ?>
                            </a>
                        </div>
                    </div>
                    <div data-contact="phone" class="footer_contact__fFxbr">
                        <div class="footer_contact-label__gYKsP">Bizi Arayın</div>
                        <div class="footer_contact-value__e1jbK">
                            <a href="tel:<?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?>">
                                <span><?php echo htmlspecialchars($site_set['iletisim_telefon'] ?? ''); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer_links__vib46">
                <div class="footer_nav__XkBHY">
                    <a class="footer_nav-link__LFUNG" href="ilanlar.php"><span data-text="Aramak">Aramak</span></a>
                    <a class="footer_nav-link__LFUNG" href="danismanlar.php"><span data-text="Danışmanlar">Danışmanlar</span></a>
                    <a class="footer_nav-link__LFUNG" href="hakkimizda.php"><span data-text="Hakkımızda">Hakkımızda</span></a>
                    <a class="footer_nav-link__LFUNG" href="iletisim.php"><span data-text="İletişim">İletişim</span></a>
                </div>
                <div class="footer_socials__4JfcA">
                    <?php if(!empty($site_set['facebook'])): ?>
                    <a href="<?php echo $site_set['facebook']; ?>" target="_blank" class="footer_social-link__2uQBq">Facebook</a>
                    <?php endif; ?>
                    <?php if(!empty($site_set['instagram'])): ?>
                    <a href="<?php echo $site_set['instagram']; ?>" target="_blank" class="footer_social-link__2uQBq">Instagram</a>
                    <?php endif; ?>
                    <?php if(!empty($site_set['twitter'])): ?>
                    <a href="<?php echo $site_set['twitter']; ?>" target="_blank" class="footer_social-link__2uQBq">Twitter</a>
                    <?php endif; ?>
                    <?php if(!empty($site_set['linkedin'])): ?>
                    <a href="<?php echo $site_set['linkedin']; ?>" target="_blank" class="footer_social-link__2uQBq">LinkedIn</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Huge Logo Section (Maxwell Typography) -->
            <div class="footer_logo__5ncK8">
                <div class="huge-typography-logo">MAXWELL</div>
            </div>

            <div class="footer_copyright-container__yt1ht">
                <div class="footer_sublinks__Pj_ed">
                    <a href="#">Şartlar</a>
                    <a href="#">Gizlilik politikası</a>
                    <a href="#">Adil Konut Bildirimi</a>
                    <a href="#">Çalışma Prosedürü</a>
                    <a href="#">Basmak</a>
                    <span class="undefined">Konut Seçimi Kuponları Kabul Edilir</span>
                </div>
                <div>Maxwell Emlak</div>
                <div>Telif hakkı © <?php echo date('Y'); ?></div>
            </div>
        </div>
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
                buttons: ["zoom", "slideShow", "fullScreen", "download", "thumbs", "close"],
                animationEffect: "zoom-in-out"
            });
        });
    </script>
</body>
</html>
