<?php
// ===== FOOTER.PHP =====
?>
    </div> 

    <footer class="site-footer-frontend">
        <div class="container-footer">
            <div class="footer-section about-us">
                <h4>Tentang WellnessPlate</h4>
                <p>WellnessPlate adalah platform untuk menemukan resep makanan sehat yang disesuaikan dengan kondisi kesehatan Anda.</p>
            </div>
            <div class="footer-section quick-links">
                <h4>Link Cepat</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/pages/faq.php">FAQ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Kontak Kami</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div class="footer-section contact-info">
                <h4>Hubungi Kami</h4>
                <p>Email: info@wellnessplate.com</p>
                <p>Telepon: (021) 123-4567</p>
                <div class="social-iconsf">
                    <a href="#" class="bx bxl-facebook"></a>
                    <a href="#" class="bx bxl-instagram"></a>
                    <a href="#" class="bx bxl-twitter"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?php echo date("Y"); ?> WellnessPlate. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- JavaScript Files - konsisten pakai BASE_URL -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/search.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main_script.js"></script>
    
    <!-- Conditional JS for auth pages -->
    <?php if ($body_class === 'auth-page' && file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url(BASE_URL, PHP_URL_PATH) . '/assets/js/script_login.js')): ?>
        <script src="<?php echo BASE_URL; ?>/assets/js/script_login.js"></script>
    <?php endif; ?>

    <!-- SweetAlert2 Messages -->
    <?php
    $swal_message = null;
    $swal_type = null;
    if (isset($_SESSION['success_message_frontend'])) {
        $swal_message = $_SESSION['success_message_frontend'];
        $swal_type = 'success';
        unset($_SESSION['success_message_frontend']);
    } elseif (isset($_SESSION['error_message_frontend'])) {
        $swal_message = $_SESSION['error_message_frontend'];
        $swal_type = 'error';
        unset($_SESSION['error_message_frontend']);
    } elseif (isset($_SESSION['info_message_frontend'])) {
        $swal_message = $_SESSION['info_message_frontend'];
        $swal_type = 'info';
        unset($_SESSION['info_message_frontend']);
    }
    ?>
    <?php if ($swal_message && $swal_type): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?php echo ($swal_type === "success" ? "Berhasil!" : ($swal_type === "error" ? "Oops..." : "Info")); ?>',
                html: <?php echo json_encode($swal_message); ?>,
                icon: '<?php echo $swal_type; ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>