<?php
// Pastikan session sudah dimulai (biasanya di koneksi.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan BASE_URL sudah terdefinisi (dari config/koneksi.php)
// Jika file ini dipanggil sebelum koneksi.php, BASE_URL tidak akan ada.
// Solusi terbaik: pastikan koneksi.php di-include pertama di file halaman utama.
if (!defined('BASE_URL')) {
    // Fallback sederhana jika BASE_URL belum ada, tapi ini bukan solusi ideal
    // Sebaiknya pastikan koneksi.php di-include sebelum header ini
    // Jika kamu pakai folder 'wellnessplate2'
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host_name = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host_name .''); // Ganti 'wellnessplate2' jika perlu
}

$page_title_default = "WellnessPlate - Resep Sehat Untukmu";
$current_page_title = $page_title ?? $page_title_default; // Gunakan $page_title dari halaman atau default

$body_class = ''; // Untuk class body spesifik halaman
if (isset($is_auth_page) && $is_auth_page === true) { // Variabel ini bisa diset di halaman auth
    $body_class = 'auth-page';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_page_title); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if ($body_class === 'auth-page' && file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url( BASE_URL,PHP_URL_PATH) . '/assets/css/style_login.css')): ?>
        <link rel="stylesheet" href="/assets/css/style_login.css">
    <?php endif; ?>
    <link rel="icon" href="/assets/images/favicon.png" type="image/png">
    <!-- Tambahkan link CSS atau font lain jika perlu -->
</head>
<body class="<?php echo $body_class; ?>">
    <header class="site-header-frontend">
        <div class="container-navbar">
            <div class="logo-frontend">
                <a href="<?php echo BASE_URL; ?>/index.php">
                    <img src="/assets/images/logo-wellnessplate.png" alt="WellnessPlate Logo">
                    <span>WellnessPlate</span>
                </a>
            </div>
            <nav class="main-navigation-frontend">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/search.php">Cari Resep</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/artikel/index.php">Artikel</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/kategori/index.php">Kategori</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/about.php">Tentang Kami</a></li>
                </ul>
            </nav>
            <div class="user-actions-frontend">
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) : ?>
                    <span class="welcome-user">Halo, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?>!</span>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/logout_user.php" class="btn-nav-action">Logout</a>
                <?php else : ?>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php" class="btn-nav-action">Login</a>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php?form=register" class="btn-nav-action btn-register">Daftar</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-toggle" aria-label="Toggle Menu">☰</button>
        </div>
    </header>
    <div class="main-content-area-frontend">
    <?php // Konten utama akan dimulai setelah ini di file halaman spesifik ?>
<?php
// modules/admin/konfirmasieditadmin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/admin/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'admin.php');
    exit;
}

// Ambil data dari form
$id_admin = isset($_POST['id_admin']) ? trim($_POST['id_admin']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_baru = isset($_POST['password_baru']) ? $_POST['password_baru'] : '';
$konfirmasi_password_baru = isset($_POST['konfirmasi_password_baru']) ? $_POST['konfirmasi_password_baru'] : '';

$_SESSION['form_input_admin_edit'] = $_POST; 

$redirect_url_on_error = $base_url . 'editadmin.php?id=' . urlencode($id_admin);

$errors = [];

if (empty($id_admin)) {
    $errors[] = "ID Admin tidak terdefinisi. Proses tidak dapat dilanjutkan.";
    $_SESSION['error_message'] = implode("<br>", $errors);
    unset($_SESSION['form_input_admin_edit']);
    header('Location: ' . $base_url . 'admin.php');
    exit;
}

if (empty($username)) {
    $errors[] = "Username tidak boleh kosong.";
} elseif (strlen($username) < 3) {
    $errors[] = "Username minimal 3 karakter.";
} elseif (strlen($username) > 50) {
    $errors[] = "Username maksimal 50 karakter.";
}

if (empty($nama)) {
    $errors[] = "Nama lengkap tidak boleh kosong.";
} elseif (strlen($nama) > 100) {
    $errors[] = "Nama lengkap maksimal 100 karakter.";
}

if (empty($email)) {
    $errors[] = "Email tidak boleh kosong.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
} elseif (strlen($email) > 100) {
    $errors[] = "Email maksimal 100 karakter.";
}

$update_password = false;
if (!empty($password_baru)) {
    if (strlen($password_baru) < 6) {
        $errors[] = "Password baru minimal 6 karakter.";
    }
    if ($password_baru !== $konfirmasi_password_baru) {
        $errors[] = "Password baru dan konfirmasi password baru tidak cocok.";
    }
    if (empty(array_filter($errors, function($err_msg) {
        return strpos($err_msg, 'Password baru') !== false;
    }))) {
        $update_password = true;
    }
} elseif (!empty($konfirmasi_password_baru) && empty($password_baru)) {
    $errors[] = "Password baru tidak boleh kosong jika konfirmasi password diisi.";
}

if (empty($errors)) {
    $stmt_check_user = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE username = ? AND id_admin != ?");
    if ($stmt_check_user) {
        mysqli_stmt_bind_param($stmt_check_user, "ss", $username, $id_admin);
        mysqli_stmt_execute($stmt_check_user);
        mysqli_stmt_store_result($stmt_check_user);
        if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
            $errors[] = "Username '" . htmlspecialchars($username) . "' sudah digunakan oleh admin lain.";
        }
        mysqli_stmt_close($stmt_check_user);
    } else {
        $errors[] = "Terjadi kesalahan saat memeriksa username. Silakan coba lagi.";
    }

    $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE email = ? AND id_admin != ?");
    if ($stmt_check_email) {
        mysqli_stmt_bind_param($stmt_check_email, "ss", $email, $id_admin);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors[] = "Email '" . htmlspecialchars($email) . "' sudah digunakan oleh admin lain.";
        }
        mysqli_stmt_close($stmt_check_email);
    } else {
        $errors[] = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
    }
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    header('Location: ' . $redirect_url_on_error);
    exit;
}

$params_type = "";
$params_values = [];

if ($update_password) {
    $hashed_password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    $query = "UPDATE admin SET username = ?, nama = ?, email = ?, password = ? WHERE id_admin = ?";
    $params_type = "sssss";
    $params_values = [$username, $nama, $email, $hashed_password_baru, $id_admin];
} else {
    $query = "UPDATE admin SET username = ?, nama = ?, email = ? WHERE id_admin = ?";
    $params_type = "ssss";
    $params_values = [$username, $nama, $email, $id_admin];
}

$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {

    $bind_names[] = $params_type;
    for ($i=0; $i<count($params_values);$i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params_values[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_names));
    
    if (mysqli_stmt_execute($stmt)) {
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id_admin) {
            $_SESSION['admin_username'] = $username; 
            $_SESSION['admin_nama'] = $nama;    
            $_SESSION['admin_email'] = $email;
        }

        mysqli_stmt_close($stmt);
        $_SESSION['success_message'] = "Data admin berhasil diperbarui.";
        unset($_SESSION['form_input_admin_edit']); 
        header('Location: ' . $base_url . 'admin.php');
        exit;
    } else {
        mysqli_stmt_close($stmt);
        $_SESSION['error_message'] = "Gagal memperbarui data admin: " . mysqli_stmt_error($stmt);
        mysqli_close($koneksi);
        header('Location: ' . $redirect_url_on_error);
        exit;
    }
} else {
    $_SESSION['error_message'] = "Gagal mempersiapkan statement database untuk update: " . mysqli_error($koneksi);
    mysqli_close($koneksi);
    header('Location: ' . $redirect_url_on_error);
    exit;
}
?>