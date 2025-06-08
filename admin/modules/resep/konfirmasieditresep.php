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

$base_url = "/admin/modules/resep/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$id_resep = trim($_POST['id_resep'] ?? '');
$nama_resep = trim($_POST['nama_resep'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$id_admin = trim($_POST['id_admin'] ?? '');
$id_kondisi = trim($_POST['id_kondisi'] ?? '');
$cara_buat = trim($_POST['cara_buat'] ?? '');
$current_image = trim($_POST['current_image'] ?? '');
$tags = isset($_POST['tags']) ? $_POST['tags'] : [];

$resep_bahans_post = isset($_POST['resep_bahan']) && is_array($_POST['resep_bahan']) ? $_POST['resep_bahan'] : [];
$gizi_post = isset($_POST['gizi']) && is_array($_POST['gizi']) ? $_POST['gizi'] : [];

$errors = [];

if (empty($id_resep)) $errors[] = "ID Resep tidak valid.";
if (empty($nama_resep)) $errors[] = "Nama resep wajib diisi.";
if (strlen($nama_resep) > 100) $errors[] = "Nama resep maksimal 100 karakter.";
if (empty($deskripsi)) $errors[] = "Deskripsi resep wajib diisi.";
if (empty($id_admin) || strlen($id_admin) > 10) $errors[] = "Admin pembuat resep wajib dipilih.";
if (empty($id_kondisi) || strlen($id_kondisi) > 10) $errors[] = "Kondisi kesehatan wajib dipilih.";
if (empty($cara_buat)) $errors[] = "Cara membuat resep wajib diisi.";

// Handle image upload if a new image is provided
$image_name = $current_image;
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = "Format file harus JPG, JPEG, atau PNG.";
    }
    if ($file['size'] > $max_size) {
        $errors[] = "Ukuran file maksimal 2MB.";
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Terjadi kesalahan saat upload file.";
    }

    if (empty($errors)) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $image_name = uniqid() . '.' . $extension;
        $upload_path = __DIR__ . '/../../../assets/images/menu/' . $image_name;
        
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $errors[] = "Gagal menyimpan file gambar.";
        } else {
            // Delete old image if exists and different from new one
            if (!empty($current_image) && $current_image !== $image_name) {
                $old_image_path = __DIR__ . '/../../../assets/images/menu/' . $current_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }
}

if (empty($resep_bahans_post) || count($resep_bahans_post) == 0) {
    $errors[] = "Minimal harus ada satu bahan dalam resep.";
} else {
    foreach ($resep_bahans_post as $index => $item_bahan) {
        if (empty($item_bahan['id_bahan']) || strlen($item_bahan['id_bahan']) > 10) {
            $errors[] = "Bahan ke-" . ($index + 1) . " wajib dipilih.";
        }
        if (!isset($item_bahan['jumlah']) || $item_bahan['jumlah'] === '' || !is_numeric($item_bahan['jumlah']) || (float)$item_bahan['jumlah'] <= 0) {
            $errors[] = "Jumlah untuk bahan ke-" . ($index + 1) . " wajib diisi dengan angka valid lebih dari 0.";
        }
    }
}

$kalori = isset($gizi_post['kalori']) && $gizi_post['kalori'] !== '' ? (float)$gizi_post['kalori'] : null;
$protein = isset($gizi_post['protein']) && $gizi_post['protein'] !== '' ? (float)$gizi_post['protein'] : null;
$karbohidrat = isset($gizi_post['karbohidrat']) && $gizi_post['karbohidrat'] !== '' ? (float)$gizi_post['karbohidrat'] : null;
$lemak = isset($gizi_post['lemak']) && $gizi_post['lemak'] !== '' ? (float)$gizi_post['lemak'] : null;

$has_gizi_data = ($kalori !== null || $protein !== null || $karbohidrat !== null || $lemak !== null);

if ($has_gizi_data) {
    if ($kalori !== null && (!is_numeric($kalori) || $kalori < 0)) $errors[] = "Kalori (jika diisi) harus angka non-negatif.";
    if ($protein !== null && (!is_numeric($protein) || $protein < 0)) $errors[] = "Protein (jika diisi) harus angka non-negatif.";
    if ($karbohidrat !== null && (!is_numeric($karbohidrat) || $karbohidrat < 0)) $errors[] = "Karbohidrat (jika diisi) harus angka non-negatif.";
    if ($lemak !== null && (!is_numeric($lemak) || $lemak < 0)) $errors[] = "Lemak (jika diisi) harus angka non-negatif.";
}

if (!empty($errors)) {
    if (isset($image_name) && $image_name !== $current_image && file_exists(__DIR__ . '/../../../assets/images/menu/' . $image_name)) {
        unlink(__DIR__ . '/../../../assets/images/menu/' . $image_name);
    }
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_resep_edit'] = $_POST;
    header('Location: ' . $base_url . 'editresep.php?id=' . urlencode($id_resep));
    exit;
}

mysqli_autocommit($koneksi, false);
$error_flag_transaction = false;

// Update recipe
$query_update_resep = "UPDATE resep SET id_admin = ?, id_kondisi = ?, nama_resep = ?, deskripsi = ?, image = ?, cara_buat = ?, tags = ? WHERE id_resep = ?";
$stmt_update_resep = mysqli_prepare($koneksi, $query_update_resep);
if ($stmt_update_resep) {
    $tags_json = json_encode($tags);
    mysqli_stmt_bind_param($stmt_update_resep, "ssssssss", $id_admin, $id_kondisi, $nama_resep, $deskripsi, $image_name, $cara_buat, $tags_json, $id_resep);
    if (!mysqli_stmt_execute($stmt_update_resep)) {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mengupdate data resep utama: " . mysqli_stmt_error($stmt_update_resep);
    }
    mysqli_stmt_close($stmt_update_resep);
} else {
    $error_flag_transaction = true;
    $_SESSION['error_message'] = "Gagal mempersiapkan statement update resep: " . mysqli_error($koneksi);
}

// Update ingredients
if (!$error_flag_transaction) {
    // Delete existing ingredients
    $query_delete_bahan = "DELETE FROM resep_bahan WHERE id_resep = ?";
    $stmt_delete_bahan = mysqli_prepare($koneksi, $query_delete_bahan);
    if ($stmt_delete_bahan) {
        mysqli_stmt_bind_param($stmt_delete_bahan, "s", $id_resep);
        if (!mysqli_stmt_execute($stmt_delete_bahan)) {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal menghapus bahan resep lama: " . mysqli_stmt_error($stmt_delete_bahan);
        }
        mysqli_stmt_close($stmt_delete_bahan);
    } else {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus bahan: " . mysqli_error($koneksi);
    }

    // Insert new ingredients
    if (!$error_flag_transaction) {
        // Get the current max id_resep_bahan
        $max_id_query = "SELECT MAX(CAST(SUBSTRING(id_resep_bahan, 3) AS UNSIGNED)) as max_id FROM resep_bahan";
        $max_id_result = mysqli_query($koneksi, $max_id_query);
        $max_id_row = mysqli_fetch_assoc($max_id_result);
        $next_id = $max_id_row['max_id'] ? $max_id_row['max_id'] + 1 : 1;

        $query_insert_bahan = "INSERT INTO resep_bahan (id_resep_bahan, id_resep, id_bahan, jumlah) VALUES (?, ?, ?, ?)";
        $stmt_insert_bahan = mysqli_prepare($koneksi, $query_insert_bahan);
        if ($stmt_insert_bahan) {
            foreach ($resep_bahans_post as $item_bahan) {
                $id_bahan_item = $item_bahan['id_bahan'];
                $jumlah_item = (string)$item_bahan['jumlah'];
                $id_resep_bahan = 'RB' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
                mysqli_stmt_bind_param($stmt_insert_bahan, "ssss", $id_resep_bahan, $id_resep, $id_bahan_item, $jumlah_item);
                if (!mysqli_stmt_execute($stmt_insert_bahan)) {
                    $error_flag_transaction = true;
                    $_SESSION['error_message'] = "Gagal menyimpan bahan resep: " . mysqli_stmt_error($stmt_insert_bahan);
                    break;
                }
                $next_id++;
            }
            mysqli_stmt_close($stmt_insert_bahan);
        } else {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal mempersiapkan statement insert bahan: " . mysqli_error($koneksi);
        }
    }
}

// Update nutrition info
if (!$error_flag_transaction) {
    // Delete existing nutrition info
    $query_delete_gizi = "DELETE FROM gizi_resep WHERE id_resep = ?";
    $stmt_delete_gizi = mysqli_prepare($koneksi, $query_delete_gizi);
    if ($stmt_delete_gizi) {
        mysqli_stmt_bind_param($stmt_delete_gizi, "s", $id_resep);
        if (!mysqli_stmt_execute($stmt_delete_gizi)) {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal menghapus data gizi lama: " . mysqli_stmt_error($stmt_delete_gizi);
        }
        mysqli_stmt_close($stmt_delete_gizi);
    }

    // Insert new nutrition info if exists
    if (!$error_flag_transaction && $has_gizi_data) {
        $query_insert_gizi = "INSERT INTO gizi_resep (id_resep, kalori, protein, karbohidrat, lemak) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_gizi = mysqli_prepare($koneksi, $query_insert_gizi);
        if ($stmt_insert_gizi) {
            mysqli_stmt_bind_param($stmt_insert_gizi, "sdddd", $id_resep, $kalori, $protein, $karbohidrat, $lemak);
            if (!mysqli_stmt_execute($stmt_insert_gizi)) {
                $error_flag_transaction = true;
                $_SESSION['error_message'] = "Gagal menyimpan data gizi baru: " . mysqli_stmt_error($stmt_insert_gizi);
            }
            mysqli_stmt_close($stmt_insert_gizi);
        } else {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal mempersiapkan statement insert gizi: " . mysqli_error($koneksi);
        }
    }
}

if ($error_flag_transaction) {
    mysqli_rollback($koneksi);
    if ($image_name !== $current_image && file_exists(__DIR__ . '/../../../assets/images/menu/' . $image_name)) {
        unlink(__DIR__ . '/../../../assets/images/menu/' . $image_name);
    }
    $_SESSION['form_input_resep_edit'] = $_POST;
    header('Location: ' . $base_url . 'editresep.php?id=' . urlencode($id_resep));
} else {
    mysqli_commit($koneksi);
    $_SESSION['success_message'] = "Resep '" . htmlspecialchars($nama_resep) . "' berhasil diupdate.";
    unset($_SESSION['form_input_resep_edit']);
    header('Location: ' . $base_url . 'resep.php');
}

mysqli_autocommit($koneksi, true);
exit;
?>