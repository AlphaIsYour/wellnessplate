<?php
session_start();
require_once '../../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Verifikasi metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Verifikasi password untuk konfirmasi tambahan
if (!isset($_POST['password'])) {
    setcookie('delete_error', 'Password harus diisi untuk verifikasi', time() + 5, '/');
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$password = $_POST['password'];
$success = false;
$error_message = '';

try {
    // Verifikasi password
    $verify_query = "SELECT password FROM users WHERE id_user = ?";
    $stmt_verify = mysqli_prepare($koneksi, $verify_query);
    if (!$stmt_verify) {
        throw new Exception("Error dalam persiapan query verifikasi");
    }
    
    mysqli_stmt_bind_param($stmt_verify, "s", $user_id);
    mysqli_stmt_execute($stmt_verify);
    $result = mysqli_stmt_get_result($stmt_verify);
    $user_data = mysqli_fetch_assoc($result);
    
    if (!$user_data || !password_verify($password, $user_data['password'])) {
        throw new Exception("Password yang dimasukkan tidak valid");
    }
    
    mysqli_stmt_close($stmt_verify);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    // Fungsi untuk mengecek apakah tabel ada
    function tableExists($koneksi, $table) {
        $result = mysqli_query($koneksi, "SHOW TABLES LIKE '$table'");
        return mysqli_num_rows($result) > 0;
    }

    // Daftar tabel yang mungkin memiliki relasi dengan user
    $tables_to_clean = [];
    
    // Cek setiap tabel sebelum ditambahkan ke daftar yang akan dihapus
    if (tableExists($koneksi, 'favorit')) {
        $tables_to_clean['favorit'] = 'id_user';
    }
    if (tableExists($koneksi, 'rating')) {
        $tables_to_clean['rating'] = 'id_user';
    }
    if (tableExists($koneksi, 'komentar')) {
        $tables_to_clean['komentar'] = 'id_user';
    }
    if (tableExists($koneksi, 'resep')) {
        $tables_to_clean['resep'] = 'id_admin';
    }
    if (tableExists($koneksi, 'artikel')) {
        $tables_to_clean['artikel'] = 'id_admin';
    }

    // Hapus data dari tabel-tabel terkait yang ada
    foreach ($tables_to_clean as $table => $user_column) {
        // Jika tabel adalah resep atau artikel, mungkin perlu menghapus file terkait
        if ($table === 'resep' || $table === 'artikel') {
            $query = "SELECT image FROM $table WHERE $user_column = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!empty($row['image'])) {
                        $file_path = '../../uploads/' . $row['image'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }

        // Hapus record dari tabel
        $query = "DELETE FROM $table WHERE $user_column = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Hapus data user dari tabel users
    $delete_user_query = "DELETE FROM users WHERE id_user = ?";
    $stmt_delete_user = mysqli_prepare($koneksi, $delete_user_query);
    
    if ($stmt_delete_user) {
        mysqli_stmt_bind_param($stmt_delete_user, "s", $user_id);
        
        if (mysqli_stmt_execute($stmt_delete_user)) {
            // Commit transaksi jika semua operasi berhasil
            mysqli_commit($koneksi);
            $success = true;
            
            // Hapus semua data session
            session_unset();
            session_destroy();
            
            // Set cookie untuk menampilkan pesan sukses setelah redirect
            setcookie('account_deleted', 'Akun Anda telah berhasil dihapus', time() + 5, '/');
        } else {
            throw new Exception("Gagal menghapus akun");
        }
        
        mysqli_stmt_close($stmt_delete_user);
    } else {
        throw new Exception("Error dalam persiapan query");
    }

} catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($koneksi);
    $error_message = $e->getMessage();
    error_log("Error saat menghapus akun user $user_id: " . $error_message);
    
    // Set cookie untuk menampilkan pesan error setelah redirect
    setcookie('delete_error', $error_message, time() + 5, '/');
}

// Tutup koneksi
mysqli_close($koneksi);

// Redirect ke halaman utama
if ($success) {
    header("Location: " . BASE_URL . "/index.php");
} else {
    header("Location: " . BASE_URL . "/index.php?error=" . urlencode($error_message));
}
exit;
?> 