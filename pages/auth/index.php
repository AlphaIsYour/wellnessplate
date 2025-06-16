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

if (!defined('BASE_URL') || BASE_URL === '') {
    $base_path_css = '/assets/css/style_login.css';
    $base_path_js = '/assets/js/script_login.js';
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

// Get form data from session if exists (for re-populating form after error)
$form_data_register = $_SESSION['form_data_register'] ?? [];
$form_data_login = $_SESSION['form_data_login'] ?? [];
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container <?php echo $active_form === 'register' ? 'active' : ''; ?>" id="authContainer" style="margin-bottom: 50px;">
    <!-- LOGIN FORM -->
    <div class="form-box login">
        <form action="<?php echo BASE_URL; ?>/pages/auth/proses_login_user.php" method="POST">
            <h1>Login</h1>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo "<p class='error-message'>" . htmlspecialchars($_SESSION['login_error']) . "</p>";
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo "<p class='success-message'>" . htmlspecialchars($_SESSION['register_success']) . "</p>";
                unset($_SESSION['register_success']);
            }
            ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username atau Email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            
            <button type="submit" class="btn">Login</button>
            <p>atau login dengan</p>
            <div class="social-icons">
                <a href="#"><i class='bx bxl-google'></i></a>
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-github'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
        </form>
    </div>

    <!-- REGISTRATION FORM -->
    <div class="form-box register">
        <form action="<?php echo BASE_URL; ?>/pages/auth/proses_register_user.php" method="POST">
            <h1>Registrasi</h1>
            <?php
            if (isset($_SESSION['register_error'])) {
                echo "<p class='error-message'>" . nl2br(htmlspecialchars($_SESSION['register_error'])) . "</p>";
                unset($_SESSION['register_error']);
            }
            ?>
            <div class="input-box">
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($form_data_register['nama_lengkap'] ?? ''); ?>" required>
                <i class='bx bxs-user-detail'></i>
            </div>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($form_data_register['username'] ?? ''); ?>" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($form_data_register['email'] ?? ''); ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="password" placeholder="Password" required minlength="6">
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Konfirmasi Password" required minlength="6">
                <i class='bx bxs-lock'></i>
            </div>
            <div class="input-box">
                <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($form_data_register['tanggal_lahir'] ?? ''); ?>">
                <i class='bx bxs-calendar'></i>
            </div>
            <div class="input-box gender-box">
                <select name="jenis_kelamin">
                    <option value="" disabled <?php echo !isset($form_data_register['jenis_kelamin']) ? 'selected' : ''; ?>>Pilih Jenis Kelamin</option>
                    <option value="L" <?php echo (isset($form_data_register['jenis_kelamin']) && $form_data_register['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo (isset($form_data_register['jenis_kelamin']) && $form_data_register['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
                <i class='bx bx-male-female'></i>
            </div>
            <button type="submit" class="btn">Daftar</button>
            <p>atau daftar dengan</p>
            <div class="social-icons">
                <a href="#"><i class='bx bxl-google'></i></a>
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-github'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
        </form>
    </div>

    <!-- TOGGLE PANEL -->
    <div class="toggle-box">
        <div class="toggle-panel toggle-left">
            <h1>Selamat Datang!</h1>
            <p>Belum punya akun?</p>
            <button class="btn register-btn">Daftar</button>
        </div>

        <div class="toggle-panel toggle-right">
            <h1>Selamat Datang Kembali!</h1>
            <p>Sudah punya akun?</p>
            <button class="btn login-btn">Login</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: '<?php echo addslashes($_SESSION['success_message']); ?>',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4CAF50'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan form login setelah registrasi berhasil
                document.querySelector('.wrapper').classList.remove('active');
            }
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    // Check for register error
    <?php if (isset($_SESSION['register_error'])): ?>
        Swal.fire({
            title: 'Oops!',
            text: '<?php echo addslashes($_SESSION['register_error']); ?>',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>

    // Check for login error
    <?php if (isset($_SESSION['login_error'])): ?>
        Swal.fire({
            title: 'Oops!',
            text: '<?php echo addslashes($_SESSION['login_error']); ?>',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
});
</script>

<?php
unset($_SESSION['form_data_register']);
unset($_SESSION['form_data_login']);
require_once __DIR__ . '/../../includes/footer.php';
?>
