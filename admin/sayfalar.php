<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

$sayfalar = $db->query("SELECT * FROM sayfalar ORDER BY id ASC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-12">
        <h4 class="mb-1 text-dark fw-bold">Sayfa Yönetimi</h4>
        <p class="text-muted small">Hakkımızda, İletişim gibi sabit içerik sayfalarını buradan yönetebilirsiniz.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3" style="width: 80px;">ID</th>
                        <th class="py-3">Sayfa Başlığı</th>
                        <th class="py-3">Slug (Benzersiz Anahtar)</th>
                        <th class="py-3">Son Güncelleme</th>
                        <th class="px-4 py-3 text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sayfalar as $s): ?>
                    <tr>
                        <td class="px-4 fw-bold text-secondary">#<?= $s['id'] ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($s['baslik']) ?></div>
                        </td>
                        <td><code class="bg-light px-2 py-1 rounded text-primary small"><?= $s['slug'] ?></code></td>
                        <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($s['guncellenme_tarihi'])) ?></td>
                        <td class="px-4 text-end">
                            <a href="sayfa_duzenle.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm px-3 fw-bold rounded-pill">
                                <i class="fa-solid fa-edit me-1"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
