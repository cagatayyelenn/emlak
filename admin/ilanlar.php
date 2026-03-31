<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$query = "
    SELECT i.*, p.ad_soyad AS yonetici_adi,
    (SELECT dosya_yolu FROM ilan_medya WHERE ilan_id = i.id AND medya_tipi = 'gorsel' ORDER BY id ASC LIMIT 1) as vitrin_gorseli
    FROM ilanlar i
    LEFT JOIN portfoy_yoneticileri p ON i.portfoy_yoneticisi_id = p.id
    ORDER BY 
        CASE WHEN i.yayin_durumu = 'Aktif' THEN 0 ELSE 1 END ASC,
        i.id DESC
";

try {
    $stmt = $db->query($query);
    $ilanlar = $stmt ? $stmt->fetchAll() : [];
} catch (PDOException $e) {
    // Veritabanı veya tablo henüz hazır değilse boş liste dön
    $ilanlar = [];
}
?>
<style>
    .sold-rented {
        opacity: 0.6;
        filter: grayscale(100%);
        background-color: #f8f9fa !important;
    }
    .sold-rented img {
        filter: grayscale(100%);
    }
    .status-badge-Aktif { background-color: #198754; }
    .status-badge-Satıldı { background-color: #dc3545; }
    .status-badge-Kiralandı { background-color: #e62236; }
    
    .search-container {
        position: relative;
    }
    .search-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .search-container input {
        padding-left: 40px;
        border-radius: 0px;
        border: 1px solid #dee2e6;
        height: 45px;
    }
    .filter-btn.active {
        background-color: #4361ee !important;
        color: white !important;
        border-color: #4361ee !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">İlan Yönetimi</h2>
        <p class="text-muted small mb-0">Tüm emlak portföyünüzü buradan yönetebilirsiniz.</p>
    </div>
    <a href="ilan_ekle.php" class="btn btn-primary px-4 py-2 fw-bold shadow-sm"><i class="fa-solid fa-plus me-2"></i> Yeni İlan Ekle</a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="ilanSearch" class="form-control" placeholder="Başlık, İlan No veya Konum ile ara...">
                </div>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-2">
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary filter-btn active" data-filter="Tümü">Tümü</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="Satılık">Satılık</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="Kiralık">Kiralık</button>
                </div>
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-outline-danger filter-btn-status" data-status="Satıldı">Satılanlar</button>
                    <button type="button" class="btn btn-outline-primary filter-btn-status" data-status="Kiralandı">Kiralananlar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="ilanTable">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th width="80" class="ps-4 py-3">Görsel</th>
                        <th class="py-3">İlan Bilgileri</th>
                        <th class="py-3">Konum</th>
                        <th class="py-3">Fiyat</th>
                        <th class="border-0 text-secondary small fw-bold">DANIŞMAN</th>
                        <th class="py-3 pe-4 text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($ilanlar) > 0): ?>
                        <?php foreach($ilanlar as $ilan): 
                            $isPassive = ($ilan['yayin_durumu'] !== 'Aktif');
                            $rowClass = $isPassive ? 'sold-rented' : '';
                        ?>
                        <tr class="ilan-row <?= $rowClass ?>" 
                            data-durum="<?= htmlspecialchars($ilan['durumu']??'') ?>" 
                            data-yayin="<?= htmlspecialchars($ilan['yayin_durumu']??'Aktif') ?>">
                            <td class="ps-4">
                                <?php if($ilan['vitrin_gorseli']): ?>
                                    <img src="uploads/images/<?= htmlspecialchars($ilan['vitrin_gorseli']) ?>" alt="Vitrin" class="rounded shadow-sm border" style="width:65px; height:65px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded border shadow-xs" style="width:65px; height:65px;">
                                        <i class="fa-solid fa-image fa-2x opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark fs-6 ilan-baslik"><?= htmlspecialchars($ilan['baslik']) ?></span>
                                    <div class="mt-1">
                                        <span class="badge <?= ($ilan['durumu']??'')=='Satılık' ? 'bg-danger' : 'bg-primary' ?> rounded-pill me-1 small"><?= htmlspecialchars($ilan['durumu'] ?? 'Satılık') ?></span>
                                        <span class="badge bg-light text-secondary border rounded-pill me-1 small ilan-no">No: <?= htmlspecialchars($ilan['ilan_no'] ?? '-') ?></span> 
                                        <span class="text-muted small fw-semibold"><?= htmlspecialchars($ilan['oda_sayisi'] ?? '') ?> <?= $ilan['m2_brut'] ? ' • '.$ilan['m2_brut'].'m²' : '' ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center text-muted small ilan-konum">
                                    <i class="fa-solid fa-location-dot text-danger me-2"></i>
                                    <span><?= htmlspecialchars($ilan['il'] ?? '-') ?><?= !empty($ilan['ilce']) ? ' / '.htmlspecialchars($ilan['ilce']) : '' ?></span>
                                </div>
                                <div class="text-muted smaller ms-4"><?= htmlspecialchars($ilan['mahalle'] ?? '') ?></div>
                            </td>
                            <td>
                                <span class="fs-5 fw-bold text-success ilan-fiyat"><?= number_format($ilan['fiyat'], 0, ',', '.') ?> ₺</span>
                            </td>
                            <td>
                                <div class="mb-1 small fw-bold text-secondary"><?= htmlspecialchars($ilan['yonetici_adi'] ?? 'Atanmamış') ?></div>
                                <span class="badge status-badge-<?= $ilan['yayin_durumu'] ?? 'Aktif' ?> border-0 text-white shadow-xs" style="font-size:0.75rem;">
                                    <i class="fa-solid <?= $ilan['yayin_durumu'] == 'Aktif' ? 'fa-check-circle' : 'fa-clock-rotate-left' ?> me-1"></i>
                                    <?= htmlspecialchars($ilan['yayin_durumu'] ?? 'Aktif') ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-outline-success btn-status-change" 
                                            data-id="<?= $ilan['id'] ?>" 
                                            data-current="<?= htmlspecialchars($ilan['yayin_durumu']??'Aktif') ?>"
                                            title="Durumu Değiştir">
                                        <i class="fa-solid fa-handshake"></i>
                                    </button>
                                    <a href="ilan_duzenle.php?id=<?= $ilan['id'] ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="ilan_sil.php?id=<?= $ilan['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Sil">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Henüz hiç ilan eklenmemiş.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Durum Güncelleme Modalı -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Durum Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="statusIlanId">
                <div class="d-grid gap-2">
                    <button class="btn btn-success status-option py-2 fw-bold" data-val="Aktif">
                        <i class="fa-solid fa-check-circle me-2"></i>Aktif İlan
                    </button>
                    <button class="btn btn-danger status-option py-2 fw-bold" data-val="Satıldı">
                        <i class="fa-solid fa-house-circle-check me-2"></i>Satıldı
                    </button>
                    <button class="btn btn-primary status-option py-2 fw-bold" data-val="Kiralandı">
                        <i class="fa-solid fa-key me-2"></i>Kiralandı
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ilanSearch');
    const tableRows = document.querySelectorAll('.ilan-row');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    let currentFilter = 'Tümü';

    // Arama ve Filtreleme Fonksiyonu
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const type = row.dataset.durum;
            const status = row.dataset.yayin;
            
            const matchesSearch = text.includes(searchTerm);
            const matchesType = (currentFilter === 'Tümü' || type === currentFilter);
            
            if (matchesSearch && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Arama Girdisi
    searchInput.addEventListener('input', filterTable);

    // Kiralık/Satılık/Tümü Filtreleme
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterTable();
        });
    });

    // Durum Değiştirme Butonu
    document.querySelectorAll('.btn-status-change').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('statusIlanId').value = this.dataset.id;
            statusModal.show();
        });
    });

    // Durum Seçenekleri
    document.querySelectorAll('.status-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = document.getElementById('statusIlanId').value;
            const durum = this.dataset.val;
            
            updateStatus(id, durum);
        });
    });

    function updateStatus(id, durum) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('durum', durum);

        fetch('ilan_durum_guncelle.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Hata', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Hata', 'İşlem sırasında bir hata oluştu.', 'error');
        });
    }
    
    // Satılanlar/Kiralananlar hızlı filtre butonları
    document.querySelectorAll('.filter-btn-status').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetStatus = this.dataset.status;
            tableRows.forEach(row => {
                if (row.dataset.yayin === targetStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            // Arama ve diğer filtreleri temizle görüntüyü bozmamak için
            searchInput.value = '';
            filterBtns.forEach(b => b.classList.remove('active'));
            filterBtns[0].classList.add('active'); // Tümü butonunu seçili yap
        });
    });
});
</script>
<?php require_once 'includes/footer.php'; ?>
