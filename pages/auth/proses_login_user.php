<?php
require_once __DIR__ . '/../../config/koneksi.php'; // Sesuaikan path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email dan password wajib diisi.";
        $_SESSION['form_data_login'] = $_POST;
        header("Location: " . BASE_URL . "/pages/auth/index.php"); // Kembali ke form login
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Format email tidak valid.";
        $_SESSION['form_data_login'] = $_POST;
        header("Location: " . BASE_URL . "/pages/auth/index.php");
        exit;
    }

    // Cek ke database
    // Asumsi tabel users punya kolom: id_user, nama_lengkap, email, password (hashed)
    $stmt = mysqli_prepare($koneksi, "SELECT id_user, nama_lengkap, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        // Login berhasil
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['user_email'] = $email;

        // Redirect ke halaman yang diinginkan setelah login, misal beranda
        header("Location: " . BASE_URL . "/index.php?message=Login berhasil!");
        exit;
    } else {
        // Login gagal
        $_SESSION['login_error'] = "Email atau password salah.";
        $_SESSION['form_data_login'] = $_POST;
        header("Location: " . BASE_URL . "/pages/auth/index.php");
        exit;
    }
} else {
    // Jika bukan POST, redirect
    header("Location: " . BASE_URL . "/pages/auth/index.php");
    exit;
}
?>