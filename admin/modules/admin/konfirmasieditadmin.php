<?php
// modules/admin/konfirmasieditadmin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../koneksi.php'; // Pastikan $base_url ada di sini

$base_url = "/modules/admin/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'admin.php');
    exit;
}

// Ambil data dari form
$id_admin = isset($_POST['id_admin']) ? trim($_POST['id_admin']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_baru = isset($_POST['password_baru']) ? $_POST['password_baru'] : '';
$konfirmasi_password_baru = isset($_POST['konfirmasi_password_baru']) ? $_POST['konfirmasi_password_baru'] : '';

// Simpan input form ke session untuk diisi kembali jika ada error
$_SESSION['form_input_admin_edit'] = $_POST; 

// URL untuk redirect kembali ke form edit jika ada error
$redirect_url_on_error = $base_url . 'editadmin.php?id=' . urlencode($id_admin);

$errors = [];

if (empty($id_admin)) {
    $errors[] = "ID Admin tidak terdefinisi. Proses tidak dapat dilanjutkan.";
    // Jika ID admin tidak ada, ini masalah serius, redirect ke halaman utama admin
    $_SESSION['error_message'] = implode("<br>", $errors);
    unset($_SESSION['form_input_admin_edit']); // Hapus form input karena ID tidak valid
    header('Location: ' . $base_url . 'admin.php');
    exit;
}

if (empty($username)) {
    $errors[] = "Username tidak boleh kosong.";
} elseif (strlen($username) < 3) {
    $errors[] = "Username minimal 3 karakter.";
} elseif (strlen($username) > 50) {
    $errors[] = "Username maksimal 50 karakter.";
}

if (empty($nama)) {
    $errors[] = "Nama lengkap tidak boleh kosong.";
} elseif (strlen($nama) > 100) {
    $errors[] = "Nama lengkap maksimal 100 karakter.";
}

if (empty($email)) {
    $errors[] = "Email tidak boleh kosong.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
} elseif (strlen($email) > 100) {
    $errors[] = "Email maksimal 100 karakter.";
}

// Validasi password baru (hanya jika diisi)
$update_password = false;
if (!empty($password_baru)) {
    if (strlen($password_baru) < 6) {
        $errors[] = "Password baru minimal 6 karakter.";
    }
    if ($password_baru !== $konfirmasi_password_baru) {
        $errors[] = "Password baru dan konfirmasi password baru tidak cocok.";
    }
    // Hanya set flag jika tidak ada error yang muncul DARI validasi password itu sendiri
    if (empty(array_filter($errors, function($err_msg) { // Cek apakah ada error password baru
        return strpos($err_msg, 'Password baru') !== false;
    }))) {
        $update_password = true;
    }
} elseif (!empty($konfirmasi_password_baru) && empty($password_baru)) {
    // Jika konfirmasi diisi tapi password baru tidak, ini juga error
    $errors[] = "Password baru tidak boleh kosong jika konfirmasi password diisi.";
}


// Lakukan pengecekan keunikan username dan email HANYA JIKA validasi dasar lolos
if (empty($errors)) {
    // Cek keunikan username
    $stmt_check_user = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE username = ? AND id_admin != ?");
    if ($stmt_check_user) {
        mysqli_stmt_bind_param($stmt_check_user, "ss", $username, $id_admin);
        mysqli_stmt_execute($stmt_check_user);
        mysqli_stmt_store_result($stmt_check_user);
        if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
            $errors[] = "Username '" . htmlspecialchars($username) . "' sudah digunakan oleh admin lain.";
        }
        mysqli_stmt_close($stmt_check_user);
    } else {
        $errors[] = "Terjadi kesalahan saat memeriksa username. Silakan coba lagi.";
        // error_log("MySQL Prep Error (check username edit): " . mysqli_error($koneksi));
    }

    // Cek keunikan email
    $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_admin FROM admin WHERE email = ? AND id_admin != ?");
    if ($stmt_check_email) {
        mysqli_stmt_bind_param($stmt_check_email, "ss", $email, $id_admin);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors[] = "Email '" . htmlspecialchars($email) . "' sudah digunakan oleh admin lain.";
        }
        mysqli_stmt_close($stmt_check_email);
    } else {
        $errors[] = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
        // error_log("MySQL Prep Error (check email edit): " . mysqli_error($koneksi));
    }
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    header('Location: ' . $redirect_url_on_error);
    exit;
}

// Jika lolos semua validasi, lanjutkan proses update
// Persiapkan query UPDATE
$params_type = "";
$params_values = [];

if ($update_password) {
    $hashed_password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    $query = "UPDATE admin SET username = ?, nama = ?, email = ?, password = ? WHERE id_admin = ?";
    $params_type = "sssss"; // username, nama, email, password_baru, id_admin
    $params_values = [$username, $nama, $email, $hashed_password_baru, $id_admin];
} else {
    // Update tanpa mengubah password
    $query = "UPDATE admin SET username = ?, nama = ?, email = ? WHERE id_admin = ?";
    $params_type = "ssss"; // username, nama, email, id_admin
    $params_values = [$username, $nama, $email, $id_admin];
}

$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    // mysqli_stmt_bind_param($stmt, $params_type, ...$params_values); // PHP 5.6+ spread operator
    // Untuk kompatibilitas lebih luas, kita gunakan call_user_func_array
    $bind_names[] = $params_type;
    for ($i=0; $i<count($params_values);$i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params_values[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_names));
    
    if (mysqli_stmt_execute($stmt)) {
        // Cek apakah admin yang diedit adalah admin yang sedang login
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id_admin) {
            $_SESSION['admin_username'] = $username; // Asumsi key session adalah 'admin_username'
            $_SESSION['admin_nama'] = $nama;     // Asumsi key session adalah 'admin_nama'
            $_SESSION['admin_email'] = $email;    // Asumsi key session adalah 'admin_email'
        }

        $_SESSION['success_message'] = "Data admin berhasil diperbarui.";
        unset($_SESSION['form_input_admin_edit']); 
        header('Location: ' . $base_url . 'admin.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data admin: " . mysqli_stmt_error($stmt);
        header('Location: ' . $redirect_url_on_error);
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error_message'] = "Gagal mempersiapkan statement database untuk update: " . mysqli_error($koneksi);
    header('Location: ' . $redirect_url_on_error);
    exit;
}

mysqli_close($koneksi); // Sebenarnya tidak akan tercapai jika ada exit di atasnya
exit;
?>