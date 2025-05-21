<?php
// File: pages/auth/proses_login_user.php

require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input username dan password
    $username_input = trim($_POST['username'] ?? ''); // Ambil 'username' dari POST
    $password_input = $_POST['password'] ?? '';

    $_SESSION['form_data_login'] = $_POST;

    if (empty($username_input) || empty($password_input)) {
        $_SESSION['login_error'] = "Username dan password wajib diisi.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    }

    // Validasi username (opsional, bisa ditambah jika ada aturan khusus untuk format username)
    // Contoh sederhana: if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username_input)) {
    //     $_SESSION['login_error'] = "Format username tidak valid.";
    //     header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
    //     exit;
    // }

    // Cek ke database menggunakan username
    // Pastikan tabel 'users' punya kolom 'username' (atau nama kolom yang sesuai untuk username)
    // dan kolom 'password' (hashed), 'id_user', 'nama_lengkap', 'email'
    $stmt = mysqli_prepare($koneksi, "SELECT id_user, username, nama_lengkap, email, password FROM users WHERE username = ?");
    if (!$stmt) {
        error_log("MySQLi prepare failed: " . mysqli_error($koneksi));
        $_SESSION['login_error'] = "Terjadi kesalahan pada sistem (DBP). Silakan coba lagi nanti.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username_input); // Bind $username_input
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password_input, $user['password'])) {
        // Login berhasil
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_username'] = $user['username']; // Simpan username di session jika perlu
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['user_email'] = $user['email']; // Simpan email juga jika ada dan perlu

        unset($_SESSION['form_data_login']);
        unset($_SESSION['login_error']);

        header("Location: " . BASE_URL . "/index.php?message=Login+berhasil!");
        exit;
    } else {
        // Login gagal
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    }
} else {
    header("Location: " . BASE_URL . "/pages/auth/index.php");
    exit;
}
?>