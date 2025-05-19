<?php
require_once __DIR__ . '/../../../config/koneksi.php';


$base_url = "/admin/modules/bahan/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$id_bahan = trim($_POST['id_bahan'] ?? '');
$nama_bahan = trim($_POST['nama_bahan'] ?? '');
$satuan = trim($_POST['satuan'] ?? '');

$errors = [];

if (empty($id_bahan) || strlen($id_bahan) > 10) {
    $_SESSION['error_message'] = "ID Bahan tidak valid untuk diedit.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$stmt_curr = mysqli_prepare($koneksi, "SELECT nama_bahan FROM bahan WHERE id_bahan = ?");
mysqli_stmt_bind_param($stmt_curr, "s", $id_bahan);
mysqli_stmt_execute($stmt_curr);
$result_curr = mysqli_stmt_get_result($stmt_curr);
$current_bahan_data = mysqli_fetch_assoc($result_curr);
mysqli_stmt_close($stmt_curr);

if (!$current_bahan_data) {
    $_SESSION['error_message'] = "Bahan dengan ID " . htmlspecialchars($id_bahan) . " tidak ditemukan.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

if (empty($nama_bahan)) {
    $errors[] = "Nama bahan wajib diisi.";
} elseif (strlen($nama_bahan) > 100) {
    $errors[] = "Nama bahan maksimal 100 karakter.";
} elseif ($nama_bahan !== $current_bahan_data['nama_bahan']) {
    $stmt_check_nama = mysqli_prepare($koneksi, "SELECT id_bahan FROM bahan WHERE nama_bahan = ? AND id_bahan != ?");
    if ($stmt_check_nama) {
        mysqli_stmt_bind_param($stmt_check_nama, "ss", $nama_bahan, $id_bahan);
        mysqli_stmt_execute($stmt_check_nama);
        mysqli_stmt_store_result($stmt_check_nama);
        if (mysqli_stmt_num_rows($stmt_check_nama) > 0) {
            $errors[] = "Nama bahan '" . htmlspecialchars($nama_bahan) . "' sudah ada untuk bahan lain.";
        }
        mysqli_stmt_close($stmt_check_nama);
    } else {
         $errors[] = "Gagal memeriksa nama bahan: " . mysqli_error($koneksi);
    }
}


if (empty($satuan)) {
    $errors[] = "Satuan wajib diisi.";
} elseif (strlen($satuan) > 20) {
    $errors[] = "Satuan maksimal 20 karakter.";
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_bahan_edit'] = $_POST; 
    header('Location: ' . $base_url . 'editbahan.php?id=' . urlencode($id_bahan));
    exit;
}

$query_update = "UPDATE bahan SET nama_bahan = ?, satuan = ? WHERE id_bahan = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);

if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, "sss", $nama_bahan, $satuan, $id_bahan);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['success_message'] = "Data bahan '" . htmlspecialchars($nama_bahan) . "' berhasil diupdate.";
        } else {
            $_SESSION['success_message'] = "Tidak ada perubahan data untuk bahan '" . htmlspecialchars($nama_bahan) . "'."; 
        }
        unset($_SESSION['form_input_bahan_edit']);
        header('Location: ' . $base_url . 'bahan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate data bahan: " . mysqli_stmt_error($stmt_update);
    }
    mysqli_stmt_close($stmt_update);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_bahan_edit'] = $_POST;
header('Location: ' . $base_url . 'editbahan.php?id=' . urlencode($id_bahan));
exit;
?>