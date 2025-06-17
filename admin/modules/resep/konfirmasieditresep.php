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
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /index.php?error=Silakan login terlebih dahulu.");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

// Validasi input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Metode request tidak valid!";
    header("Location: resep.php");
    exit;
}

$id_resep = $_POST['id_resep'] ?? '';
if (empty($id_resep)) {
    $_SESSION['error_message'] = "ID resep tidak valid!";
    header("Location: resep.php");
    exit;
}

// Simpan input form untuk digunakan jika ada error
$_SESSION['form_input'] = $_POST;

// Validasi dan sanitasi input
$nama_resep = trim($_POST['nama_resep'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$cara_buat = trim($_POST['cara_buat'] ?? '');
$id_admin = trim($_POST['id_admin'] ?? '');
$id_kondisi = trim($_POST['id_kondisi'] ?? '');

// Validasi data wajib
$errors = [];
if (empty($nama_resep)) $errors[] = "Nama resep wajib diisi!";
if (empty($deskripsi)) $errors[] = "Deskripsi wajib diisi!";
if (empty($cara_buat)) $errors[] = "Cara membuat wajib diisi!";
if (empty($id_admin)) $errors[] = "Admin wajib dipilih!";
if (empty($id_kondisi)) $errors[] = "Kondisi kesehatan wajib dipilih!";

if (!empty($errors)) {
    $_SESSION['error_message'] = implode("\n", $errors);
    $_SESSION['form_input_resep_edit'] = $_POST;
    header("Location: editresep.php?id=" . urlencode($id_resep));
    exit;
}

try {
    mysqli_begin_transaction($koneksi);

    // nah disini query untuk update resep
    $stmt = mysqli_prepare($koneksi, "UPDATE resep SET 
        nama_resep = ?,
        deskripsi = ?,
        cara_buat = ?,
        id_admin = ?,
        id_kondisi = ?
        WHERE id_resep = ?");

    mysqli_stmt_bind_param($stmt, "ssssss", 
        $nama_resep,
        $deskripsi,
        $cara_buat,
        $id_admin,
        $id_kondisi,
        $id_resep
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal mengupdate data resep: " . mysqli_error($koneksi));
    }

    // Update tags
    mysqli_query($koneksi, "DELETE FROM resep_tags WHERE id_resep = '" . mysqli_real_escape_string($koneksi, $id_resep) . "'");
    if (isset($_POST['tags']) && is_array($_POST['tags'])) {
        $values = [];
        foreach ($_POST['tags'] as $tag_id) {
            $values[] = "('" . mysqli_real_escape_string($koneksi, $id_resep) . "', " . intval($tag_id) . ")";
        }
        if (!empty($values)) {
            $query = "INSERT INTO resep_tags (id_resep, id_tag) VALUES " . implode(", ", $values);
            if (!mysqli_query($koneksi, $query)) {
                throw new Exception("Gagal mengupdate tags: " . mysqli_error($koneksi));
            }
        }
    }

    // Update bahan-bahan
    mysqli_query($koneksi, "DELETE FROM resep_bahan WHERE id_resep = '" . mysqli_real_escape_string($koneksi, $id_resep) . "'");
    if (isset($_POST['resep_bahan']) && is_array($_POST['resep_bahan'])) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO resep_bahan (id_resep_bahan, id_resep, id_bahan, jumlah) VALUES (?, ?, ?, ?)");
        
        foreach ($_POST['resep_bahan'] as $bahan) {
            if (!empty($bahan['id_bahan']) && isset($bahan['jumlah'])) {
                // Generate a unique ID for id_resep_bahan
                $stmt_max = mysqli_prepare($koneksi, "SELECT MAX(CAST(SUBSTRING(id_resep_bahan, 3) AS UNSIGNED)) as max_id FROM resep_bahan WHERE id_resep_bahan LIKE 'RB%'");
                mysqli_stmt_execute($stmt_max);
                $result = mysqli_stmt_get_result($stmt_max);
                $row = mysqli_fetch_assoc($result);
                $next_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                $id_resep_bahan = 'RB' . str_pad($next_id, 8, '0', STR_PAD_LEFT);
                mysqli_stmt_close($stmt_max);

                $id_bahan = $bahan['id_bahan'];
                $jumlah = floatval($bahan['jumlah']);
                
                mysqli_stmt_bind_param($stmt, "sssd", $id_resep_bahan, $id_resep, $id_bahan, $jumlah);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal mengupdate bahan-bahan: " . mysqli_error($koneksi));
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

    // Update gizi
    mysqli_query($koneksi, "DELETE FROM gizi_resep WHERE id_resep = '" . mysqli_real_escape_string($koneksi, $id_resep) . "'");
    if (isset($_POST['gizi']) && is_array($_POST['gizi'])) {
        // Generate ID for gizi_resep
        $stmt_max_gizi = mysqli_prepare($koneksi, "SELECT MAX(CAST(SUBSTRING(id_gizi_resep, 3) AS UNSIGNED)) as max_id FROM gizi_resep WHERE id_gizi_resep LIKE 'GR%'");
        mysqli_stmt_execute($stmt_max_gizi);
        $result_gizi = mysqli_stmt_get_result($stmt_max_gizi);
        $row_gizi = mysqli_fetch_assoc($result_gizi);
        $next_id_gizi = $row_gizi['max_id'] ? $row_gizi['max_id'] + 1 : 1;
        $id_gizi_resep = 'GR' . str_pad($next_id_gizi, 8, '0', STR_PAD_LEFT);
        mysqli_stmt_close($stmt_max_gizi);

        $stmt_gizi = mysqli_prepare($koneksi, "INSERT INTO gizi_resep (id_gizi_resep, id_resep, kalori, protein, karbohidrat, lemak) VALUES (?, ?, ?, ?, ?, ?)");
        
        $kalori = !empty($_POST['gizi']['kalori']) ? floatval($_POST['gizi']['kalori']) : 0;
        $protein = !empty($_POST['gizi']['protein']) ? floatval($_POST['gizi']['protein']) : 0;
        $karbohidrat = !empty($_POST['gizi']['karbohidrat']) ? floatval($_POST['gizi']['karbohidrat']) : 0;
        $lemak = !empty($_POST['gizi']['lemak']) ? floatval($_POST['gizi']['lemak']) : 0;

        mysqli_stmt_bind_param($stmt_gizi, "ssdddd", 
            $id_gizi_resep,
            $id_resep,
            $kalori,
            $protein,
            $karbohidrat,
            $lemak
        );
        
        if (!mysqli_stmt_execute($stmt_gizi)) {
            throw new Exception("Gagal menambahkan informasi gizi: " . mysqli_error($koneksi));
        }
        mysqli_stmt_close($stmt_gizi);
    }

    mysqli_commit($koneksi);
    unset($_SESSION['form_input']);
    $_SESSION['success_message'] = "Resep berhasil diperbarui!";
    header("Location: resep.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: editresep.php?id=" . urlencode($id_resep));
    exit;
}
?>