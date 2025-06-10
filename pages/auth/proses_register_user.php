<?php
// File: pages/auth/proses_register_user.php

// koneksi.php akan otomatis start session dan define BASE_URL
require_once __DIR__ . '/../../config/koneksi.php';

// Simpan data form untuk digunakan kembali jika ada error
$form_data = [
    'username' => $_POST['username'] ?? '',
    'email' => $_POST['email'] ?? '',
    'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
    'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
    'jenis_kelamin' => $_POST['jenis_kelamin'] ?? ''
];
$_SESSION['form_data_register'] = $form_data;

// Validasi field yang wajib diisi
$required_fields = [
    'username' => 'Username',
    'email' => 'Email',
    'password' => 'Password',
    'confirm_password' => 'Konfirmasi Password',
    'nama_lengkap' => 'Nama Lengkap'
];

$errors = [];

foreach ($required_fields as $field => $label) {
    if (empty($_POST[$field])) {
        $errors[] = "$label wajib diisi!";
    }
}

// Validasi konfirmasi password
if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Password dan Konfirmasi Password tidak cocok!";
    }
    if (strlen($_POST['password']) < 6) {
        $errors[] = "Password harus minimal 6 karakter!";
    }
}

// Jika ada error, kembali ke halaman registrasi
if (!empty($errors)) {
    $_SESSION['register_error'] = implode("\n", $errors);
    header('Location: ' . BASE_URL . '/pages/auth/index.php?form=register');
    exit;
}

try {
    // Generate ID User
    $stmt = $koneksi->prepare("SELECT MAX(CAST(SUBSTRING(id_user, 2) AS UNSIGNED)) as max_id FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    $id_user = 'U' . str_pad($next_id, 9, '0', STR_PAD_LEFT);

    // Hash password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO users (id_user, username, password, email, nama_lengkap, tanggal_lahir, jenis_kelamin, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $koneksi->prepare($sql);
    
    // Bind parameters
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
    $jenis_kelamin = !empty($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null;
    
    $stmt->bind_param("sssssss", 
        $id_user,
        $_POST['username'],
        $hashed_password,
        $_POST['email'],
        $_POST['nama_lengkap'],
        $tanggal_lahir,
        $jenis_kelamin
    );
    
    // Execute statement
    $stmt->execute();
    
    // Clear form data session
    unset($_SESSION['form_data_register']);
    
    // Set success message
    $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
    header('Location: ' . BASE_URL . '/pages/auth/index.php');
    exit;
    
} catch (Exception $e) {
    $error_message = '';
    
    // Check for duplicate entry
    if ($koneksi->errno == 1062) { // MySQL error code for duplicate entry
        if (strpos($e->getMessage(), 'username') !== false) {
            $error_message = "Username sudah digunakan!";
        } elseif (strpos($e->getMessage(), 'email') !== false) {
            $error_message = "Email sudah terdaftar!";
        } else {
            $error_message = "Data sudah ada dalam sistem!";
        }
    } else {
        $error_message = "Terjadi kesalahan! Silakan coba lagi.";
    }
    
    $_SESSION['register_error'] = $error_message;
    header('Location: ' . BASE_URL . '/pages/auth/index.php?form=register');
    exit;
}
?>