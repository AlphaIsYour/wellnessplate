<?php
session_start();
require_once __DIR__ . '../../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $sql = "SELECT id_admin, username, password, nama FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);

        if ($password === $admin['password']) { 
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama'];

            header("Location: dashboard.php");
            exit;
        } else {
            header("Location: index.php?error=Username atau password salah.");
            exit;
        }
    } else {
        header("Location: index.php?error=Username atau password salah.");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>