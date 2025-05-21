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
// modules/users/konfirmasiedituser.php
require_once __DIR__ . '/../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: user.php');
    exit;
}

// Ambil dan sanitasi input dasar
$id_user = trim($_POST['id_user'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$jenis_kelamin_text = $_POST['jenis_kelamin_text'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi_password_baru = $_POST['konfirmasi_password_baru'] ?? '';

// Validasi
$errors = [];

if (empty($id_user)) {
    $_SESSION['error_message'] = "ID Pengguna tidak valid untuk diedit.";
    header('Location: user.php');
    exit;
}

// Cek apakah user dengan id_user ini ada dan ambil data lamanya
$stmt_curr = mysqli_prepare($koneksi, "SELECT username, email FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_curr, "s", $id_user);
mysqli_stmt_execute($stmt_curr);
$result_curr = mysqli_stmt_get_result($stmt_curr);
$current_user_data = mysqli_fetch_assoc($result_curr);
mysqli_stmt_close($stmt_curr);

if (!$current_user_data) {
    $_SESSION['error_message'] = "Pengguna dengan ID " . htmlspecialchars($id_user) . " tidak ditemukan.";
    header('Location: user.php');
    exit;
}


// Username
if (empty($username)) $errors[] = "Username wajib diisi.";
elseif (strlen($username) > 50) $errors[] = "Username maksimal 50 karakter.";
elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) $errors[] = "Username hanya boleh berisi huruf, angka, dan underscore (_).";
elseif ($username !== $current_user_data['username']) { // Cek duplikasi jika username diubah
    $stmt_check = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ? AND id_user != ?");
    mysqli_stmt_bind_param($stmt_check, "ss", $username, $id_user);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) $errors[] = "Username '" . htmlspecialchars($username) . "' sudah digunakan.";
    mysqli_stmt_close($stmt_check);
}

// Email
if (empty($email)) $errors[] = "Email wajib diisi.";
elseif (strlen($email) > 100) $errors[] = "Email maksimal 100 karakter.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
elseif ($email !== $current_user_data['email']) { // Cek duplikasi jika email diubah
    $stmt_check = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ? AND id_user != ?");
    mysqli_stmt_bind_param($stmt_check, "ss", $email, $id_user);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) $errors[] = "Email '" . htmlspecialchars($email) . "' sudah terdaftar.";
    mysqli_stmt_close($stmt_check);
}

// Nama Lengkap
if (empty($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi.";
elseif (strlen($nama_lengkap) > 100) $errors[] = "Nama lengkap maksimal 100 karakter.";

// Tanggal Lahir
if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir wajib diisi.";
else {
    $d = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
    if (!$d || $d->format('Y-m-d') !== $tanggal_lahir) $errors[] = "Format tanggal lahir tidak valid.";
    elseif (new DateTime($tanggal_lahir) >= new DateTime('today')) $errors[] = "Tanggal lahir tidak boleh hari ini atau di masa depan.";
}

// Jenis Kelamin
$jenis_kelamin_db = '';
if ($jenis_kelamin_text === 'Laki-laki') $jenis_kelamin_db = 'L';
elseif ($jenis_kelamin_text === 'Perempuan') $jenis_kelamin_db = 'P';
else $errors[] = "Jenis kelamin wajib dipilih.";

// Password (jika diisi)
$update_password = false;
$hashed_password_baru = null;
if (!empty($password_baru)) {
    if (strlen($password_baru) < 8) $errors[] = "Password baru minimal 8 karakter.";
    if ($password_baru !== $konfirmasi_password_baru) $errors[] = "Konfirmasi password baru tidak cocok.";
    if (empty($errors)) { // Hanya hash jika tidak ada error sebelumnya terkait password
        $hashed_password_baru = password_hash($password_baru, PASSWORD_BCRYPT);
        $update_password = true;
    }
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_user_edit'] = $_POST; 
    header('Location: edituser.php?id=' . urlencode($id_user));
    exit;
}

// Waktu saat ini untuk updated_at
$current_datetime = date('Y-m-d H:i:s');

// Bangun query UPDATE secara dinamis
$query_parts = [];
$params = [];
$types = "";

$query_parts[] = "username = ?"; $params[] = $username; $types .= "s";
$query_parts[] = "email = ?"; $params[] = $email; $types .= "s";
$query_parts[] = "nama_lengkap = ?"; $params[] = $nama_lengkap; $types .= "s";
$query_parts[] = "tanggal_lahir = ?"; $params[] = $tanggal_lahir; $types .= "s";
$query_parts[] = "jenis_kelamin = ?"; $params[] = $jenis_kelamin_db; $types .= "s";

if ($update_password) {
    $query_parts[] = "password = ?"; $params[] = $hashed_password_baru; $types .= "s";
}

$query_parts[] = "updated_at = ?"; $params[] = $current_datetime; $types .= "s";

// Tambahkan id_user ke akhir parameter untuk WHERE clause
$params[] = $id_user; $types .= "s";

$query_update = "UPDATE users SET " . implode(", ", $query_parts) . " WHERE id_user = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);

if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, $types, ...$params);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['success_message'] = "Data pengguna '" . htmlspecialchars($username) . "' berhasil diupdate.";
        } else {
            // Bisa jadi tidak ada perubahan data, atau ID tidak ditemukan (meski sudah dicek)
            $_SESSION['success_message'] = "Tidak ada perubahan data untuk pengguna '" . htmlspecialchars($username) . "'."; 
        }
        unset($_SESSION['form_input_user_edit']);
        header('Location: user.php');
        exit;
    } else {
        // error_log("Gagal update user: " . mysqli_stmt_error($stmt_update));
        $_SESSION['error_message'] = "Gagal mengupdate data pengguna. Silakan coba lagi.";
    }
    mysqli_stmt_close($stmt_update);
} else {
    // error_log("Gagal prepare update: " . mysqli_error($koneksi));
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
}

$_SESSION['form_input_user_edit'] = $_POST;
header('Location: edituser.php?id=' . urlencode($id_user));
exit;
?>