<?php
// 404.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
?>

            <section class="flat-section-v3 flat-error text-center py-5">
                <div class="container">
                    <div class="box-error py-5">
                        <div class="content py-5">
                            <h1 class="title fw-8" style="font-size: 120px; color: var(--primary);">404</h1>
                            <h3 class="mt-4">Sayfa Bulunamadı</h3>
                            <p class="text-variant-1 mt-3">Aradığınız sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
                            <a href="index.php" class="tf-btn primary mt-4">Anasayfaya Dön <span class="icon icon-arrow-right2"></span></a>
                        </div>
                    </div>
                </div>
            </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
