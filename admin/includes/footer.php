<?php
// includes/footer.php
?>
        </div> <!-- .container-fluid kapaması -->
    </div> <!-- #page-content-wrapper kapaması -->
</div> <!-- #wrapper kapaması -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom JS -->
<script src="assets/js/app.js"></script>

<script>
    // Mobil Sidebar Toggle
    document.getElementById("menu-toggle")?.addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>

</body>
</html>
