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
require_once '../../templates/header.php';

$page_title = "Edit Bahan";
$id_bahan_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/bahan/";

if (empty($id_bahan_to_edit)) {
    $_SESSION['error_message'] = "ID Bahan tidak valid.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$stmt_get_bahan = mysqli_prepare($koneksi, "SELECT id_bahan, nama_bahan, satuan FROM bahan WHERE id_bahan = ?");
if (!$stmt_get_bahan) {
    $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get_bahan, "s", $id_bahan_to_edit);
mysqli_stmt_execute($stmt_get_bahan);
$result_bahan_db = mysqli_stmt_get_result($stmt_get_bahan);
$bahan_data_db = mysqli_fetch_assoc($result_bahan_db);
mysqli_stmt_close($stmt_get_bahan);

if (!$bahan_data_db) {
    $_SESSION['error_message'] = "Bahan dengan ID '" . htmlspecialchars($id_bahan_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$form_input = isset($_SESSION['form_input_bahan_edit']) ? $_SESSION['form_input_bahan_edit'] : $bahan_data_db;
unset($_SESSION['form_input_bahan_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Bahan: <?php echo htmlspecialchars($bahan_data_db['nama_bahan']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditbahan.php" method="POST">
                <input type="hidden" name="id_bahan" value="<?php echo htmlspecialchars($bahan_data_db['id_bahan']); ?>">

                <div class="form-group">
                    <label for="nama_bahan">Nama Bahan</label>
                    <input type="text" id="nama_bahan" name="nama_bahan" value="<?php echo htmlspecialchars($form_input['nama_bahan'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" id="satuan" name="satuan" value="<?php echo htmlspecialchars($form_input['satuan'] ?? ''); ?>" placeholder="Contoh: gram, ml, buah, sdt" required maxlength="20">
                </div>
                
                <button type="submit" class="btn">Update Bahan</button>
                <a href="<?php echo $base_url; ?>bahan.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_bahan_db);
require_once '../../templates/footer.php';
?>