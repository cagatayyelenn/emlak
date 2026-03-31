// assets/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Silme butonlarına SweetAlert Onayı Ekleme
    function attachDeleteAlert() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(btn => {
            // Eğer daha önceden event listener eklenmişse tekrar eklemesin
            if (btn.dataset.alertAttached !== "true") {
                btn.dataset.alertAttached = "true";
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    
                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: "Bu veriyi sildiğinizde geri alamazsınız!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Evet, Sil!',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    })
                });
            }
        });
    }

    attachDeleteAlert();
});
