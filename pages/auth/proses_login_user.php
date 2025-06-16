<?php
// [PERBAIKAN 1] Selalu panggil session_start() di baris paling atas!
session_start();

// File: pages/auth/proses_login_user.php

require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input username dan password
    $username_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    $_SESSION['form_data_login'] = $_POST;

    if (empty($username_input) || empty($password_input)) {
        $_SESSION['login_error'] = "Username dan password wajib diisi.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "SELECT id_user, username, nama_lengkap, email, password FROM users WHERE username = ?");
    if (!$stmt) {
        error_log("MySQLi prepare failed: " . mysqli_error($koneksi));
        $_SESSION['login_error'] = "Terjadi kesalahan pada sistem (DBP). Silakan coba lagi nanti.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password_input, $user['password'])) {
        // Login berhasil
        
        // [PERBAIKAN 2] Gunakan nama kunci 'id_user' agar konsisten dengan search.php
        $_SESSION['id_user'] = $user['id_user']; 
        
        // Simpan data lain yang mungkin berguna dengan nama kunci yang konsisten
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];

        // Hapus data form dan error dari session
        unset($_SESSION['form_data_login']);
        unset($_SESSION['login_error']);
        
        // Regenerasi session ID untuk keamanan setelah login
        session_regenerate_id(true);

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