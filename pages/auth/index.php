<?php
// pages/auth/index.php
require_once __DIR__ . '/../../config/koneksi.php';
$page_title = "Login atau Daftar - WellnessPlate";
$is_auth_page = true;

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$active_form = isset($_GET['form']) && $_GET['form'] === 'register' ? 'register' : 'login';

$additional_css = [(defined('BASE_URL') ? BASE_URL : '') . '/assets/css/style_login.css'];
$additional_js = [(defined('BASE_URL') ? BASE_URL : '') . '/assets/js/script_login.js'];

// Jika BASE_URL tidak didefinisikan atau kosong, gunakan path relatif dari root
if (!defined('BASE_URL') || BASE_URL === '') {
    $base_path_css = '/assets/css/style_login.css';
    $base_path_js = '/assets/js/script_login.js';
    // Cek apakah skrip dijalankan dari subdirektori, jika iya, tambahkan '../'
    // Ini hanya contoh sederhana, penyesuaian mungkin diperlukan tergantung struktur proyek Anda
    if (strpos($_SERVER['REQUEST_URI'], '/pages/auth') !== false) {
        $base_path_css = '../../../assets/css/style_login.css';
        $base_path_js = '../../../assets/js/script_login.js';
    } else if (strpos($_SERVER['REQUEST_URI'], '/auth') !== false) {
         $base_path_css = '../../assets/css/style_login.css';
         $base_path_js = '../../assets/js/script_login.js';
    }
    $additional_css = [$base_path_css];
    $additional_js = [$base_path_js];
}


?>

<?php require_once __DIR__ . '/../../includes/header.php'; // Pastikan path ini benar ?>

<div class="auth-page-wrapper">
    <div class="auth-container <?php echo ($active_form === 'register') ? 'right-panel-active' : ''; ?>" id="authContainer">
        <!-- Form Sign Up / Register -->
        <div class="form-container sign-up-container">
            <form action="<?php echo (defined('BASE_URL') ? BASE_URL : ''); ?>/pages/auth/proses_register_user.php" method="POST" id="registerForm">
                <h1>Buat Akun</h1>
                <?php
                if (isset($_SESSION['register_error'])) {
                    echo '<div class="auth-error-message">' . nl2br(htmlspecialchars($_SESSION['register_error'])) . '</div>';
                    unset($_SESSION['register_error']);
                }
                if (isset($_SESSION['register_success'])) {
                    echo '<div class="auth-success-message">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                    unset($_SESSION['register_success']);
                }
                ?>
                <span>Gunakan email untuk pendaftaran</span>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($_SESSION['form_data_register']['nama_lengkap'] ?? ''); ?>" required />
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_SESSION['form_data_register']['email'] ?? ''); ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" required />
                <button type="submit">Daftar</button>
                <p class="form-switcher-text">Sudah punya akun? <a href="#" id="signInLinkBottom">Login di sini</a></p>
            </form>
        </div>

        <!-- Form Sign In / Login -->
        <div class="form-container sign-in-container">
            <form action="<?php echo (defined('BASE_URL') ? BASE_URL : ''); ?>/pages/auth/proses_login_user.php" method="POST" id="loginForm">
                <h1>Login</h1>
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo '<div class="auth-error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                    unset($_SESSION['login_error']);
                }
                if (isset($_SESSION['login_success'])) {
                    echo '<div class="auth-success-message">' . htmlspecialchars($_SESSION['login_success']) . '</div>';
                    unset($_SESSION['login_success']);
                }
                ?>
                <span>Gunakan akun Anda</span>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_SESSION['form_data_login']['email'] ?? ''); ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                <a href="<?php echo (defined('BASE_URL') ? BASE_URL : ''); ?>/pages/auth/forgot_password.php" class="forgot-password-link">Lupa password Anda?</a>
                <button type="submit">Login</button>
                <p class="form-switcher-text">Belum punya akun? <a href="#" id="signUpLinkBottom">Daftar sekarang</a></p>
            </form>
        </div>

        <!-- Overlay Container (Panel Geser) -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left-content">
                    <svg style="margin-right: 350px;" width="120" height="120" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="animated-svg">
                        <circle cx="50" cy="50" r="45" stroke="white" stroke-width="4" fill="none" stroke-dasharray="283" stroke-dashoffset="283" />
                        <path d="M30 50 L45 65 L70 35" stroke="white" stroke-width="5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="75" stroke-dashoffset="75" />
                    </svg>
                    <h2 style="margin-right: 350px;">Kembali Login</h2>
                    <p style="margin-right: 350px;">Sudah memiliki akun? Login disini.</p>
                    <button style="margin-right: 350px;" class="ghost" id="signInOverlayBtn">Login</button>
                </div>
                <div class="overlay-panel overlay-right-content">
                     <svg style="margin-left:350px;" width="120" height="120" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="animated-svg">
                        <rect x="20" y="20" width="60" height="60" rx="10" stroke="white" stroke-width="4" fill="none" stroke-dasharray="240" stroke-dashoffset="240" />
                        <line x1="50" y1="35" x2="50" y2="65" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="30" stroke-dashoffset="30" />
                        <line x1="35" y1="50" x2="65" y2="50" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="30" stroke-dashoffset="30" />
                    </svg>
                    <h2 style="margin-left:350px;">Buat Akun Baru</h2>
                    <p style="margin-left:350px;">Belum punya akun? Daftar sekarang!</p>
                    <button style="margin-left:350px;" class="ghost" id="signUpOverlayBtn">Daftar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Tambahan animasi SVG dan ripple, dipindahkan ke style_login.css untuk kerapian jika memungkinkan,
   tapi bisa tetap di sini jika hanya untuk halaman ini.
   Untuk SVG, animasi 'dash' didefinisikan di style_login.css.
*/

.auth-page-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 120px); /* Sesuaikan dengan tinggi header + footer Anda (misal 60px header + 60px footer) */
  padding: 20px 0;
  background-color: #f7fafc; /* Warna latar belakang halaman */
  overflow-x: hidden; /* Mencegah scroll horizontal jika ada konten meluber sedikit saat animasi */
}

/* Animasi ripple untuk tombol */
button .ripple-effect { /* Targetkan span di dalam button */
  position: absolute;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.3); /* Sedikit lebih subtle */
  transform: scale(0);
  animation: ripple 0.6s linear;
  pointer-events: none;
}

@keyframes ripple {
  to {
    transform: scale(4);
    opacity: 0;
  }
}

/* Style untuk "wrapper" di mobile */
@media (max-width: 768px) {
  .auth-page-wrapper {
    padding: 15px 0;
    min-height: calc(100vh - 100px); /* Sesuaikan dengan tinggi header + footer mobile Anda */
    align-items: flex-start; /* Mulai dari atas di mobile */
  }
}
</style>

<?php
// Bersihkan data form session agar tidak muncul lagi saat halaman direfresh atau navigasi
if (basename($_SERVER['PHP_SELF']) == 'index.php') { // Hanya unset jika ini halaman utama auth
    unset($_SESSION['form_data_register']);
    unset($_SESSION['form_data_login']);
}
?>

<?php require_once __DIR__ . '/../../includes/footer.php'; // Pastikan path ini benar ?>