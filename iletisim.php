<?php
// iletisim.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
?>

            <section class="flat-section-v3 flat-contact">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="contact-content">
                                <h3 class="title">Bize Ulaşın</h3>
                                <p class="text-variant-1 desc">Sorularınız veya randevu talepleriniz için aşağıdaki iletişim kanallarını kullanabilir veya formu doldurabilirsiniz.</p>
                                <div class="contact-info">
                                    <div class="item d-flex gap-8 align-items-center mb-4">
                                        <div class="icon-box"><i class="icon icon-mapPin"></i></div>
                                        <div class="content">
                                            <h6 class="fw-bolder">Adres</h6>
                                            <p class="text-variant-1"><?php echo htmlspecialchars($site_set['adres']); ?></p>
                                        </div>
                                    </div>
                                    <div class="item d-flex gap-8 align-items-center mb-4">
                                        <div class="icon-box"><i class="icon icon-phone2"></i></div>
                                        <div class="content">
                                            <h6 class="fw-bolder">Telefon</h6>
                                            <p class="text-variant-1"><?php echo htmlspecialchars($site_set['telefon']); ?></p>
                                        </div>
                                    </div>
                                    <div class="item d-flex gap-8 align-items-center">
                                        <div class="icon-box"><i class="icon icon-mail"></i></div>
                                        <div class="content">
                                            <h6 class="fw-bolder">E-posta</h6>
                                            <p class="text-variant-1"><?php echo htmlspecialchars($site_set['email']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="contact-form">
                                <form action="#" method="POST" class="form-sl">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <input type="email" name="email" class="form-control" placeholder="E-posta Adresiniz" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <input type="text" name="subject" class="form-control" placeholder="Konu" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <textarea name="message" class="form-control" rows="5" placeholder="Mesajınız" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="tf-btn primary hover-btn-view">Mesaj Gönder <span class="icon icon-arrow-right2"></span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
