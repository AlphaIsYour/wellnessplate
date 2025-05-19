<?php
// pages/auth/index.php
require_once __DIR__ . '/../../config/koneksi.php'; // Sesuaikan path jika koneksi.php ada di root config
$page_title = "Login atau Daftar - WellnessPlate";

// Logika untuk redirect jika user sudah login
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: " . BASE_URL . "/index.php"); // Arahkan ke beranda jika sudah login
    exit;
}

// Tentukan form mana yang aktif berdasarkan parameter URL (opsional, default ke login)
$active_form = $_GET['form'] ?? 'login'; // bisa ?form=register
?>

<?php require_once __DIR__ . '/../../includes/header.php'; // Header frontend ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style_login.css">

<div class="auth-container <?php echo ($active_form === 'register') ? 'right-panel-active' : ''; ?>" id="authContainer">
    <!-- Form Sign Up / Register -->
    <div class="form-container sign-up-container">
        <form action="<?php echo BASE_URL; ?>/pages/auth/proses_register_user.php" method="POST">
            <h1>Buat Akun</h1>
            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<div class="auth-error-message">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
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
        </form>
    </div>

    <!-- Form Sign In / Login -->
    <div class="form-container sign-in-container">
        <form action="<?php echo BASE_URL; ?>/pages/auth/proses_login_user.php" method="POST">
            <h1>Login</h1>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="auth-error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>
            <span>Gunakan akun Anda</span>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_SESSION['form_data_login']['email'] ?? ''); ?>" required />
            <input type="password" name="password" placeholder="Password" required />
            <a href="#">Lupa password Anda?</a>
            <button type="submit">Login</button>
        </form>
    </div>

    <!-- Overlay Container (Panel Geser) -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Selamat Datang Kembali!</h1>
                <p>Untuk tetap terhubung dengan kami, silakan login dengan info pribadi Anda</p>
                <button class="ghost" id="signIn">Login</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Halo, Kawan!</h1>
                <p>Masukkan detail pribadi Anda dan mulailah perjalanan bersama kami</p>
                <button class="ghost" id="signUp">Daftar</button>
            </div>
        </div>
    </div>
</div>

<?php
// Hapus data form dari session setelah ditampilkan
unset($_SESSION['form_data_register']);
unset($_SESSION['form_data_login']);
?>

<script src="/assets/js/script_login.js"></script>
<?php require_once __DIR__ . '/../../includes/footer.php'; // Footer frontend ?>