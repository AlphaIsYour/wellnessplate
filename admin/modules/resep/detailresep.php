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

$page_title = "Detail Resep";
$id_resep_to_view = $_GET['id'] ?? null;
$base_url = "/admin/modules/resep/";
if (empty($id_resep_to_view)) {
    $_SESSION['error_message'] = "ID Resep tidak valid untuk dilihat.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$resep_detail = null;
$stmt_resep_detail = mysqli_prepare($koneksi, "SELECT r.id_resep, r.nama_resep, r.cara_buat, r.tanggal_dibuat, u.nama_lengkap AS nama_admin, k.nama_kondisi 
                                              FROM resep r 
                                              LEFT JOIN users u ON r.id_admin = u.id_user 
                                              LEFT JOIN kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi 
                                              WHERE r.id_resep = ?");
if ($stmt_resep_detail) {
    mysqli_stmt_bind_param($stmt_resep_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_resep_detail);
    $result_resep = mysqli_stmt_get_result($stmt_resep_detail);
    $resep_detail = mysqli_fetch_assoc($result_resep);
    mysqli_stmt_close($stmt_resep_detail);
} else {
    die("Gagal mempersiapkan query detail resep: " . mysqli_error($koneksi));
}

if (!$resep_detail) {
    $_SESSION['error_message'] = "Resep tidak ditemukan.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$bahan_list_detail = [];
$stmt_bahan_detail = mysqli_prepare($koneksi, "SELECT b.nama_bahan, rb.jumlah, b.satuan 
                                             FROM resep_bahan rb 
                                             JOIN bahan b ON rb.id_bahan = b.id_bahan 
                                             WHERE rb.id_resep = ? ORDER BY b.nama_bahan ASC");
if ($stmt_bahan_detail) {
    mysqli_stmt_bind_param($stmt_bahan_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_bahan_detail);
    $result_bahan = mysqli_stmt_get_result($stmt_bahan_detail);
    while ($row = mysqli_fetch_assoc($result_bahan)) {
        $bahan_list_detail[] = $row;
    }
    mysqli_stmt_close($stmt_bahan_detail);
} else {
    die("Gagal mempersiapkan query bahan resep: " . mysqli_error($koneksi));
}

$gizi_detail = null;
$stmt_gizi_detail = mysqli_prepare($koneksi, "SELECT kalori, protein, karbohidrat, lemak FROM gizi_resep WHERE id_resep = ?");
if ($stmt_gizi_detail) {
    mysqli_stmt_bind_param($stmt_gizi_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_gizi_detail);
    $result_gizi = mysqli_stmt_get_result($stmt_gizi_detail);
    $gizi_detail = mysqli_fetch_assoc($result_gizi);
    mysqli_stmt_close($stmt_gizi_detail);
} else {
    die("Gagal mempersiapkan query gizi resep: " . mysqli_error($koneksi));
}
if (!$gizi_detail) $gizi_detail = [];

?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Detail Resep: <?php echo htmlspecialchars($resep_detail['nama_resep']); ?></h2>
            <a href="<?php echo $base_url; ?>editresep.php?id=<?php echo urlencode($id_resep_to_view); ?>" class="btn btn-sm btna">Edit Resep Ini</a>
        </div>
        <div class="card-body">
                <p><strong>Nama Resep:</strong> <?php echo htmlspecialchars($resep_detail['nama_resep']); ?></p>
                <p><strong>Dibuat Oleh:</strong> <?php echo htmlspecialchars($resep_detail['nama_admin'] ?? 'N/A'); ?></p>
                <p><strong>Untuk Kondisi:</strong> <?php echo htmlspecialchars($resep_detail['nama_kondisi'] ?? 'N/A'); ?></p>
                <p><strong>Tanggal Dibuat:</strong> <?php echo htmlspecialchars(date('d F Y H:i', strtotime($resep_detail['tanggal_dibuat']))); ?></p>
                
                <hr>
                <h4>Cara Membuat:</h4>
                <div style="white-space: pre-wrap; background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 5px;"><?php echo htmlspecialchars($resep_detail['cara_buat']); ?></div>

                <hr>
                <h4>Bahan-bahan:</h4>
                <?php if (!empty($bahan_list_detail)) : ?>
                    <ul>
                        <?php foreach ($bahan_list_detail as $bahan_item) : ?>
                            <li><?php echo htmlspecialchars($bahan_item['jumlah']) . " " . htmlspecialchars($bahan_item['satuan']) . " — " . htmlspecialchars($bahan_item['nama_bahan']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Tidak ada data bahan untuk resep ini.</p>
                <?php endif; ?>

                <hr>
                <h4>Informasi Gizi (Per Porsi):</h4>
                <?php if (!empty($gizi_detail) && (isset($gizi_detail['kalori']) || isset($gizi_detail['protein']) || isset($gizi_detail['karbohidrat']) || isset($gizi_detail['lemak']) )) : ?>
                    <ul>
                        <?php if (isset($gizi_detail['kalori']) && $gizi_detail['kalori'] !== null): ?>
                            <li>Kalori: <?php echo htmlspecialchars(number_format($gizi_detail['kalori'], 1)); ?> kkal</li>
                        <?php endif; ?>
                        <?php if (isset($gizi_detail['protein']) && $gizi_detail['protein'] !== null): ?>
                            <li>Protein: <?php echo htmlspecialchars(number_format($gizi_detail['protein'], 1)); ?> gram</li>
                        <?php endif; ?>
                        <?php if (isset($gizi_detail['karbohidrat']) && $gizi_detail['karbohidrat'] !== null): ?>
                            <li>Karbohidrat: <?php echo htmlspecialchars(number_format($gizi_detail['karbohidrat'], 1)); ?> gram</li>
                        <?php endif; ?>
                        <?php if (isset($gizi_detail['lemak']) && $gizi_detail['lemak'] !== null): ?>
                            <li>Lemak: <?php echo htmlspecialchars(number_format($gizi_detail['lemak'], 1)); ?> gram</li>
                        <?php endif; ?>
                    </ul>
                <?php else : ?>
                    <p>Informasi gizi tidak tersedia atau belum diisi.</p>
                <?php endif; ?>
                
                <div style="margin-top: 20px;">
                    <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d;">Kembali ke Daftar Resep</a>
                </div>
        </div>
    </div>
</div>

<?php
if(isset($result_resep)) mysqli_free_result($result_resep);
if(isset($result_bahan)) mysqli_free_result($result_bahan);
if(isset($result_gizi)) mysqli_free_result($result_gizi);
require_once '../../templates/footer.php';
?>