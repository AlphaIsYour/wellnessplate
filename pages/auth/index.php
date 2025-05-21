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


?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container <?php echo $active_form === 'register' ? 'active' : ''; ?>" id="authContainer">
    <!-- LOGIN FORM -->
    <div class="form-box login">
        <form action="/pages/auth/proses_login_user.php" method="POST">
            <h1>Login</h1>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo "<p class='error-message'>" . htmlspecialchars($_SESSION['login_error']) . "</p>";
                unset($_SESSION['login_error']); // Hapus setelah ditampilkan
            }
            if (isset($_SESSION['register_success'])) { // Pesan setelah registrasi berhasil
                echo "<p class='success-message'>" . htmlspecialchars($_SESSION['register_success']) . "</p>";
                unset($_SESSION['register_success']); // Hapus setelah ditampilkan
            }
            ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($form_data_login['username'] ?? ''); ?>" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="forgot-link">
                <a href="#">Lupa Password?</a> <!-- Nanti buat fiturnya -->
            </div>
            <button type="submit" class="btn">Login</button>
            <p>or login with social platforms</p>
                  <div class="social-icons">
                      <a href="#"><i class='bx bxl-google' ></i></a>
                      <a href="#"><i class='bx bxl-facebook' ></i></a>
                      <a href="#"><i class='bx bxl-github' ></i></a>
                      <a href="#"><i class='bx bxl-linkedin' ></i></a>
                  </div>
        </form>
    </div>

    <!-- REGISTRATION FORM -->
    <div class="form-box register">
        <form action="/pages/auth/proses_register_user.php" method="POST">
            <h1>Registrasi</h1>
            <?php
            if (isset($_SESSION['register_error'])) {
                echo "<p class='error-message'>" . nl2br(htmlspecialchars($_SESSION['register_error'])) . "</p>"; // nl2br untuk multiline error
                unset($_SESSION['register_error']); // Hapus setelah ditampilkan
            }
            ?>
            <div class="input-box">
                      <input type="text" placeholder="Username" required>
                      <i class='bx bxs-envelope' ></i>
            </div>
            <div class="input-box">
                      <input type="password" placeholder="Password" required>
                      <i class='bx bxs-lock-alt' ></i>
            </div>
            <button type="submit" class="btn">Register</button>
                  <p>or register with social platforms</p>
            <div class="social-icons">
                      <a href="#"><i class='bx bxl-google' ></i></a>
                      <a href="#"><i class='bx bxl-facebook' ></i></a>
                      <a href="#"><i class='bx bxl-github' ></i></a>
                      <a href="#"><i class='bx bxl-linkedin' ></i></a>
            </div>
        </form>
    </div>

    <!-- TOGGLE PANEL -->
    <div class="toggle-box">
              <div class="toggle-panel toggle-left">
                  <h1>Hello, Welcome!</h1>
                  <p>Don't have an account?</p>
                  <button class="btn register-btn">Register</button>
              </div>

              <div class="toggle-panel toggle-right">
                  <h1>Welcome Back!</h1>
                  <p>Already have an account?</p>
                  <button class="btn login-btn">Login</button>
              </div>
          </div>
</div>

<?php
// Bersihkan sisa data form session yang mungkin belum ter-unset
unset($_SESSION['form_data_register']);
unset($_SESSION['form_data_login']);

require_once __DIR__ . '/../../includes/footer.php';
?>
