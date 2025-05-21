<?php
// File: pages/auth/proses_register_user.php

// koneksi.php akan otomatis start session dan define BASE_URL
require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    $_SESSION['form_data_register'] = $_POST; // Simpan input untuk diisi ulang jika error
    $errors = [];

    if (empty($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi.";
    if (empty($email)) $errors[] = "Email wajib diisi.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
    
    if (empty($password)) {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    }
    
    if (empty($konfirmasi_password)) {
        $errors[] = "Konfirmasi password wajib diisi.";
    } elseif ($password !== $konfirmasi_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }


    // Cek duplikasi email hanya jika tidak ada error sebelumnya dan email valid
    if (empty($errors) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ?");
        if ($stmt_check_email) {
            mysqli_stmt_bind_param($stmt_check_email, "s", $email);
            mysqli_stmt_execute($stmt_check_email);
            mysqli_stmt_store_result($stmt_check_email);
            if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
                $errors[] = "Email sudah terdaftar.";
            }
            mysqli_stmt_close($stmt_check_email);
        } else {
            $errors[] = "Gagal memeriksa email. Silakan coba lagi.";
            error_log("MySQLi prepare (check_email) failed: " . mysqli_error($koneksi));
        }
    }

    if (!empty($errors)) {
        $_SESSION['register_error'] = implode("<br>", $errors);
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=register");
        exit;
    }

    // Jika validasi lolos
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // PASSWORD_DEFAULT lebih disarankan
    
    // Generate id_user (contoh sederhana, pastikan unik di produksi)
    // Pertimbangkan menggunakan UUID atau auto_increment di database
    $prefix = "USR";
    $unique_part = strtoupper(substr(uniqid(), -7)); // Cukup untuk contoh, tapi tidak 100% unik global
    $id_user = $prefix . $unique_part;
    if (strlen($id_user) > 10) $id_user = substr($id_user, 0, 10);
    
    // Pastikan id_user unik, ini contoh sederhana, di produksi perlu loop cek
    // $is_unique = false;
    // while(!$is_unique) {
    //     $stmt_check_id = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE id_user = ?");
    //     mysqli_stmt_bind_param($stmt_check_id, "s", $id_user);
    //     mysqli_stmt_execute($stmt_check_id);
    //     mysqli_stmt_store_result($stmt_check_id);
    //     if (mysqli_stmt_num_rows($stmt_check_id) == 0) {
    //         $is_unique = true;
    //     } else {
    //         // Generate ulang jika sudah ada
    //         $unique_part = strtoupper(substr(uniqid(), -7));
    //         $id_user = $prefix . $unique_part;
    //         if (strlen($id_user) > 10) $id_user = substr($id_user, 0, 10);
    //     }
    //     mysqli_stmt_close($stmt_check_id);
    // }


    $current_datetime = date('Y-m-d H:i:s');
    // Asumsikan username juga ada di tabel users, jika tidak, hapus dari query
    $username_default = explode('@', $email)[0] . rand(100,999); // Contoh username sementara

    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO users (id_user, username, password, email, nama_lengkap, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt_insert) {
        error_log("MySQLi prepare (insert_user) failed: " . mysqli_error($koneksi));
        $_SESSION['register_error'] = "Pendaftaran gagal karena kesalahan sistem. Silakan coba lagi.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=register");
        exit;
    }

    mysqli_stmt_bind_param($stmt_insert, "sssssss", $id_user, $username_default, $hashed_password, $email, $nama_lengkap, $current_datetime, $current_datetime);

    if (mysqli_stmt_execute($stmt_insert)) {
        unset($_SESSION['form_data_register']); // Hapus data form jika sukses
        unset($_SESSION['register_error']);     // Hapus error sebelumnya jika ada
        $_SESSION['register_success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=login");
        exit;
    } else {
        error_log("MySQLi execute (insert_user) failed: " . mysqli_stmt_error($stmt_insert));
        $_SESSION['register_error'] = "Pendaftaran gagal. Silakan coba lagi. " . mysqli_stmt_error($stmt_insert);
        header("Location: " . BASE_URL . "/pages/auth/index.php?form=register");
        exit;
    }
    mysqli_stmt_close($stmt_insert);

} else {
    header("Location: " . BASE_URL . "/pages/auth/index.php");
    exit;
}
?>