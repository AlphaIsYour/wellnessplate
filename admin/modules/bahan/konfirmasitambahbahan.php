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
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/bahan/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'tambahbahan.php');
    exit;
}

$nama_bahan = trim($_POST['nama_bahan'] ?? '');
$satuan = trim($_POST['satuan'] ?? '');

$errors = [];

if (empty($nama_bahan)) {
    $errors[] = "Nama bahan wajib diisi.";
} elseif (strlen($nama_bahan) > 100) {
    $errors[] = "Nama bahan maksimal 100 karakter.";
} else {
    $stmt_check_nama = mysqli_prepare($koneksi, "SELECT id_bahan FROM bahan WHERE nama_bahan = ?");
    if ($stmt_check_nama) {
        mysqli_stmt_bind_param($stmt_check_nama, "s", $nama_bahan);
        mysqli_stmt_execute($stmt_check_nama);
        mysqli_stmt_store_result($stmt_check_nama);
        if (mysqli_stmt_num_rows($stmt_check_nama) > 0) {
            $errors[] = "Nama bahan '" . htmlspecialchars($nama_bahan) . "' sudah ada.";
        }
        mysqli_stmt_close($stmt_check_nama);
    } else {
        $errors[] = "Gagal memeriksa nama bahan: " . mysqli_error($koneksi);
    }
}

if (empty($satuan)) {
    $errors[] = "Satuan wajib diisi.";
} elseif (strlen($satuan) > 20) {
    $errors[] = "Satuan maksimal 20 karakter.";
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_bahan'] = $_POST;
    header('Location: ' . $base_url . 'tambahbahan.php');
    exit;
}

$prefix = "BHN";
$unique_part = strtoupper(substr(uniqid(), -7));
$id_bahan = $prefix . $unique_part;
if (strlen($id_bahan) > 10) $id_bahan = substr($id_bahan, 0, 10);


$query_insert = "INSERT INTO bahan (id_bahan, nama_bahan, satuan) VALUES (?, ?, ?)";
$stmt_insert = mysqli_prepare($koneksi, $query_insert);

if ($stmt_insert) {
    mysqli_stmt_bind_param($stmt_insert, "sss", $id_bahan, $nama_bahan, $satuan);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Bahan '" . htmlspecialchars($nama_bahan) . "' berhasil ditambahkan.";
        unset($_SESSION['form_input_bahan']);
        header('Location: ' . $base_url . 'bahan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan data bahan: " . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_bahan'] = $_POST;
header('Location: ' . $base_url . 'tambahbahan.php');
exit;
?>