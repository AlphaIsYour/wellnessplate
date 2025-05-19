<?php
// modules/users/konfirmasitambahuser.php
require_once '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . MODULE_USERS_URL . 'tambahuser.php');
    exit;
}

// Ambil data dari form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
$email = trim($_POST['email'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$jenis_kelamin_text = $_POST['jenis_kelamin_text'] ?? '';

$errors = [];

// Validasi Username
if (empty($username)) $errors[] = "Username wajib diisi.";
elseif (strlen($username) > 50) $errors[] = "Username maksimal 50 karakter.";
elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) $errors[] = "Username hanya boleh huruf, angka, dan underscore (_).";
else {
    $stmt_check = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) $errors[] = "Username '" . htmlspecialchars($username) . "' sudah digunakan.";
    mysqli_stmt_close($stmt_check);
}

// Validasi Password
if (empty($password)) $errors[] = "Password wajib diisi.";
elseif (strlen($password) < 8) $errors[] = "Password minimal 8 karakter.";
if ($password !== $konfirmasi_password) $errors[] = "Konfirmasi password tidak cocok.";

// Validasi Email
if (empty($email)) $errors[] = "Email wajib diisi.";
elseif (strlen($email) > 100) $errors[] = "Email maksimal 100 karakter.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
else {
    $stmt_check = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) $errors[] = "Email '" . htmlspecialchars($email) . "' sudah terdaftar.";
    mysqli_stmt_close($stmt_check);
}

// Validasi Nama Lengkap
if (empty($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi.";
elseif (strlen($nama_lengkap) > 100) $errors[] = "Nama lengkap maksimal 100 karakter.";

// Validasi Tanggal Lahir
if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir wajib diisi.";
else {
    $d = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
    if (!$d || $d->format('Y-m-d') !== $tanggal_lahir) $errors[] = "Format tanggal lahir tidak valid.";
    elseif (new DateTime($tanggal_lahir) >= new DateTime('today')) $errors[] = "Tanggal lahir tidak boleh hari ini atau di masa depan.";
}

// Konversi Jenis Kelamin
$jenis_kelamin_db = '';
if ($jenis_kelamin_text === 'Laki-laki') $jenis_kelamin_db = 'L';
elseif ($jenis_kelamin_text === 'Perempuan') $jenis_kelamin_db = 'P';
else $errors[] = "Jenis kelamin wajib dipilih.";


// Jika ada error, kembali ke form tambah
if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_user'] = $_POST;
    header('Location: ' . MODULE_USERS_URL . 'tambahuser.php');
    exit;
}

// Generate id_user (Sederhana untuk presentasi - POTENSI TABRAKAN ID DI PRODUKSI!)
$prefix = "USR";
$unique_part = strtoupper(substr(uniqid(), -7)); // Ambil 7 char terakhir dari uniqid
$id_user = $prefix . $unique_part;
if (strlen($id_user) > 10) $id_user = substr($id_user, 0, 10); 
// CATATAN PENTING: Untuk aplikasi nyata, cara generate ID ini TIDAK AMAN karena uniqid()
// bisa menghasilkan nilai yang sama jika dipanggil terlalu cepat.
// Pertimbangkan untuk cek keunikan ke DB atau gunakan UUID.

// Hash password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Waktu saat ini
$current_datetime = date('Y-m-d H:i:s');

// Query INSERT
$query_insert = "INSERT INTO users (id_user, username, password, email, nama_lengkap, tanggal_lahir, jenis_kelamin, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = mysqli_prepare($koneksi, $query_insert);

if ($stmt_insert) {
    mysqli_stmt_bind_param($stmt_insert, "sssssssss", 
        $id_user, $username, $hashed_password, $email, $nama_lengkap, 
        $tanggal_lahir, $jenis_kelamin_db, $current_datetime, $current_datetime
    );

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Pengguna '" . htmlspecialchars($username) . "' berhasil ditambahkan.";
        unset($_SESSION['form_input_user']); // Hapus data form dari session jika sukses
        header('Location: ' . MODULE_USERS_URL . 'user.php');
        exit;
    } else {
        // Jika eksekusi gagal
        $_SESSION['error_message'] = "Gagal menyimpan data pengguna: " . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
} else {
    // Jika prepare statement gagal
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

// Jika gagal (baik prepare atau execute), kembali ke form tambah
$_SESSION['form_input_user'] = $_POST;
header('Location: ' . MODULE_USERS_URL . 'tambahuser.php');
exit;
?>