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

$base_url = "/modules/gizi/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$id_gizi_resep = trim($_POST['id_gizi_resep'] ?? '');
$kalori = isset($_POST['kalori']) && $_POST['kalori'] !== '' ? (float)$_POST['kalori'] : null;
$protein = isset($_POST['protein']) && $_POST['protein'] !== '' ? (float)$_POST['protein'] : null;
$karbohidrat = isset($_POST['karbohidrat']) && $_POST['karbohidrat'] !== '' ? (float)$_POST['karbohidrat'] : null;
$lemak = isset($_POST['lemak']) && $_POST['lemak'] !== '' ? (float)$_POST['lemak'] : null;

$errors = [];

if (empty($id_gizi_resep) || strlen($id_gizi_resep) > 10) {
    $_SESSION['error_message'] = "ID Gizi tidak valid untuk diedit.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$stmt_check_gizi_exist = mysqli_prepare($koneksi, "SELECT id_gizi_resep, id_resep FROM gizi_resep WHERE id_gizi_resep = ?");
mysqli_stmt_bind_param($stmt_check_gizi_exist, "s", $id_gizi_resep);
mysqli_stmt_execute($stmt_check_gizi_exist);
$result_check = mysqli_stmt_get_result($stmt_check_gizi_exist);
$gizi_to_edit_data = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check_gizi_exist);

if (!$gizi_to_edit_data) {
    $_SESSION['error_message'] = "Data gizi dengan ID " . htmlspecialchars($id_gizi_resep) . " tidak ditemukan.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}


if ($kalori === null && $protein === null && $karbohidrat === null && $lemak === null) {
    $errors[] = "Minimal satu nilai gizi (Kalori, Protein, Karbohidrat, atau Lemak) harus diisi.";
}
if ($kalori !== null && (!is_numeric($kalori) || $kalori < 0)) $errors[] = "Kalori (jika diisi) harus angka non-negatif.";
if ($protein !== null && (!is_numeric($protein) || $protein < 0)) $errors[] = "Protein (jika diisi) harus angka non-negatif.";
if ($karbohidrat !== null && (!is_numeric($karbohidrat) || $karbohidrat < 0)) $errors[] = "Karbohidrat (jika diisi) harus angka non-negatif.";
if ($lemak !== null && (!is_numeric($lemak) || $lemak < 0)) $errors[] = "Lemak (jika diisi) harus angka non-negatif.";


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_gizi_edit'] = $_POST;
    $_SESSION['form_input_gizi_edit']['nama_resep'] = $_POST['nama_resep_display'] ?? 'N/A'; // Ambil dari hidden input jika perlu
    header('Location: ' . $base_url . 'editgizi.php?id=' . urlencode($id_gizi_resep));
    exit;
}

$query_update = "UPDATE gizi_resep SET kalori = ?, protein = ?, karbohidrat = ?, lemak = ? WHERE id_gizi_resep = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);

if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, "dddds", $kalori, $protein, $karbohidrat, $lemak, $id_gizi_resep);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['success_message'] = "Data gizi berhasil diupdate.";
        } else {
            $_SESSION['success_message'] = "Tidak ada perubahan data gizi."; 
        }
        unset($_SESSION['form_input_gizi_edit']);
        header('Location: ' . $base_url . 'gizi.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate data gizi: " . mysqli_stmt_error($stmt_update);
    }
    mysqli_stmt_close($stmt_update);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_gizi_edit'] = $_POST;
$_SESSION['form_input_gizi_edit']['nama_resep'] = $_POST['nama_resep_display'] ?? 'N/A'; 
header('Location: ' . $base_url . 'editgizi.php?id=' . urlencode($id_gizi_resep));
exit;
?>