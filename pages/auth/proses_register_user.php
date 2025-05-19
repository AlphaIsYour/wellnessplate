<?php
require_once __DIR__ . '/../../config/koneksi.php'; // Sesuaikan path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    $errors = [];

    if (empty($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi.";
    if (empty($email)) $errors[] = "Email wajib diisi.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
    if (empty($password)) $errors[] = "Password wajib diisi.";
    elseif (strlen($password) < 8) $errors[] = "Password minimal 8 karakter.";
    if ($password !== $konfirmasi_password) $errors[] = "Konfirmasi password tidak cocok.";

    // Cek duplikasi email
    if (empty($errors) && !empty($email)) {
        $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt_check_email, "s", $email);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors[] = "Email sudah terdaftar.";
        }
        mysqli_stmt_close($stmt_check_email);
    }

    if (!empty($errors)) {
        $_SESSION['register_error'] = implode("<br>", $errors);
        $_SESSION['form_data_register'] = $_POST; // Simpan input untuk diisi ulang
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=register"); // Kembali ke form register
        exit;
    }

    // Jika validasi lolos
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Generate id_user (contoh sederhana, pastikan unik di produksi)
    $prefix = "USR";
    $unique_part = strtoupper(substr(uniqid(), -7));
    $id_user = $prefix . $unique_part;
    if (strlen($id_user) > 10) $id_user = substr($id_user, 0, 10);
    // Sebaiknya ada loop cek keunikan ID di sini untuk produksi

    $current_datetime = date('Y-m-d H:i:s');

    // Asumsi tabel users punya kolom: id_user, username (bisa sama dengan email atau unik), password, email, nama_lengkap, tanggal_lahir (opsional di sini), jenis_kelamin (opsional di sini), created_at, updated_at
    // Untuk username, jika tidak ada field khusus, bisa set null atau default atau sama dengan email (tapi email sudah ada)
    // Mari kita asumsikan username juga diisi, bisa disamakan dengan bagian awal email atau input terpisah
    $username_default = explode('@', $email)[0] . rand(10,99); // Contoh username sementara

    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO users (id_user, username, password, email, nama_lengkap, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "sssssss", $id_user, $username_default, $hashed_password, $email, $nama_lengkap, $current_datetime, $current_datetime);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['register_success'] = "Pendaftaran berhasil! Silakan login.";
        // Kosongkan form data jika sukses
        unset($_SESSION['form_data_register']);
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login"); // Arahkan ke form login
        exit;
    } else {
        $_SESSION['register_error'] = "Pendaftaran gagal: " . mysqli_stmt_error($stmt_insert);
        $_SESSION['form_data_register'] = $_POST;
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=register");
        exit;
    }
    mysqli_stmt_close($stmt_insert);

} else {
    header("Location: " . BASE_URL . "/pages/auth/index.php");
    exit;
}
?>