<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/admin/"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . 'tambahadmin.php');
    exit;
}

// Ambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$konfirmasi_password = isset($_POST['konfirmasi_password']) ? $_POST['konfirmasi_password'] : '';
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$_SESSION['form_input'] = $_POST;

// Validasi dasar
$errors = [];
if (empty($username)) {
    $errors[] = "Username tidak boleh kosong.";
} elseif (strlen($username) < 3) {
    $errors[] = "Username minimal 3 karakter.";
} elseif (strlen($username) > 50) {
    $errors[] = "Username maksimal 50 karakter.";
}

if (empty($password)) {
    $errors[] = "Password tidak boleh kosong.";
} elseif (strlen($password) < 6) {
    $errors[] = "Password minimal 6 karakter.";
}

if ($password !== $konfirmasi_password) {
    $errors[] = "Password dan konfirmasi password tidak cocok.";
}
if (empty($nama)) {
    $errors[] = "Nama lengkap tidak boleh kosong.";
} elseif (strlen($nama) > 100) { // Sesuai VARCHAR(100)
    $errors[] = "Nama lengkap maksimal 100 karakter.";
}

if (empty($email)) {
    $errors[] = "Email tidak boleh kosong.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
} elseif (strlen($email) > 100) { // Sesuai VARCHAR(100)
    $errors[] = "Email maksimal 100 karakter.";
}

// Lakukan pengecekan keunikan username dan email HANYA JIKA validasi dasar lolos
if (empty($errors)) {
    // Cek keunikan username
    $stmt_check_user = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE username = ?");
    if ($stmt_check_user) {
        mysqli_stmt_bind_param($stmt_check_user, "s", $username);
        mysqli_stmt_execute($stmt_check_user);
        mysqli_stmt_store_result($stmt_check_user);
        if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
            $errors[] = "Username '" . htmlspecialchars($username) . "' sudah terdaftar.";
        }
        mysqli_stmt_close($stmt_check_user);
    } else {
        // Sebaiknya log error ini, jangan tampilkan mysqli_error ke user di production
        $errors[] = "Terjadi kesalahan saat memeriksa username. Silakan coba lagi."; 
        // error_log("MySQL Prep Error (check user): " . mysqli_error($koneksi));
    }

    // Cek keunikan email (jika tidak ada error sebelumnya)
    $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE email = ?");
    if ($stmt_check_email) {
        mysqli_stmt_bind_param($stmt_check_email, "s", $email);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors[] = "Email '" . htmlspecialchars($email) . "' sudah terdaftar.";
        }
        mysqli_stmt_close($stmt_check_email);
    } else {
        $errors[] = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
        // error_log("MySQL Prep Error (check email): " . mysqli_error($koneksi));
    }
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    header('Location: '. $base_url .'tambahadmin.php');
    exit;
}

// --- PEMBUATAN ID_ADMIN (VARCHAR 10) ---
// Kita akan buat id unik dengan prefix 'ADM' dan 7 karakter acak/berbasis waktu.
// Ini contoh sederhana. Untuk produksi, pertimbangkan UUID atau metode yang lebih kuat.
$id_admin_generated = false;
$max_generate_tries = 5; // Batas percobaan generate ID jika terjadi collision
$try_count = 0;
$new_id_admin = '';

while (!$id_admin_generated && $try_count < $max_generate_tries) {
    $prefix = "ADM";
    // Menghasilkan 7 karakter unik. Bisa dari timestamp atau random.
    // Contoh: microtime untuk variasi cepat + sedikit random
    $unique_part = substr(str_shuffle(str_replace('.', '', microtime(true))), 0, 7);
    $new_id_admin = $prefix . $unique_part;
    
    // Pastikan panjangnya tepat 10 karakter (jika metode di atas bisa menghasilkan lebih/kurang)
    if (strlen($new_id_admin) > 10) {
        $new_id_admin = substr($new_id_admin, 0, 10);
    } elseif (strlen($new_id_admin) < 10) {
        // Jika kurang, tambahkan padding (misal dengan angka random)
        $new_id_admin .= str_pad('', 10 - strlen($new_id_admin), mt_rand(0, 9));
    }

    // Cek apakah ID yang di-generate sudah ada di database
    $stmt_check_id = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE id_admin = ?");
    if ($stmt_check_id) {
        mysqli_stmt_bind_param($stmt_check_id, "s", $new_id_admin);
        mysqli_stmt_execute($stmt_check_id);
        mysqli_stmt_store_result($stmt_check_id);
        if (mysqli_stmt_num_rows($stmt_check_id) == 0) {
            $id_admin_generated = true; // ID unik, bisa dipakai
        }
        mysqli_stmt_close($stmt_check_id);
    } else {
        // Gagal mempersiapkan statement untuk cek ID, ini masalah
        $_SESSION['error_message'] = "Gagal memverifikasi keunikan ID Admin: " . mysqli_error($koneksi);
        header('Location: ' . $base_url .'tambahadmin.php');
        exit;
    }
    $try_count++;
}

if (!$id_admin_generated) {
    // Jika setelah beberapa kali percobaan ID tetap tidak unik atau gagal dicek
    $_SESSION['error_message'] = "Gagal menghasilkan ID Admin yang unik. Silakan coba lagi.";
    header('Location: ' . $base_url .'tambahadmin.php');
    exit;
}
// --- AKHIR PEMBUATAN ID_ADMIN ---


$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Query INSERT sekarang menyertakan id_admin
$query = "INSERT INTO admin (id_admin, username, password, nama, email) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    // Bind id_admin sebagai string ("s")
    mysqli_stmt_bind_param($stmt, "sssss", $new_id_admin, $username, $hashed_password, $nama, $email);
    
    if (mysqli_stmt_execute($stmt)) { 
        mysqli_stmt_close($stmt);
        $_SESSION['success_message'] = "Admin baru (ID: " . htmlspecialchars($new_id_admin) . ") berhasil ditambahkan.";
        unset($_SESSION['form_input']); 
        header('Location: ' . $base_url .'admin.php');
        exit; // Penting: Selalu exit setelah redirect header
    } else {
        mysqli_stmt_close($stmt);
        // Hapus pesan tentang AUTO_INCREMENT karena tidak relevan
        $_SESSION['error_message'] = "Gagal menambahkan admin ke database: " . mysqli_stmt_error($stmt);
        // Untuk debugging, bisa tambahkan juga ID yang coba diinsert:
        // $_SESSION['error_message'] .= " | ID yang dicoba: " . htmlspecialchars($new_id_admin);
        header('Location: ' . $base_url .'tambahadmin.php');
        exit; // Penting
    }
} else {
    $_SESSION['error_message'] = "Gagal mempersiapkan statement database: " . mysqli_error($koneksi);
    header('Location: ' . $base_url .'tambahadmin.php');
    exit; // Penting
}
?>