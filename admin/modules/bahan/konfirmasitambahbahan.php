<?php
require_once '../../koneksi.php';

$base_url = "/admin/modules/bahan/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'tambahbahan.php');
    exit;
}

$nama_bahan = trim($_POST['nama_bahan'] ?? '');
$satuan = trim($_POST['satuan'] ?? '');

$errors = [];

if (empty($nama_bahan)) {
    $errors[] = "Nama bahan wajib diisi.";
} elseif (strlen($nama_bahan) > 100) {
    $errors[] = "Nama bahan maksimal 100 karakter.";
} else {
    $stmt_check_nama = mysqli_prepare($koneksi, "SELECT id_bahan FROM bahan WHERE nama_bahan = ?");
    if ($stmt_check_nama) {
        mysqli_stmt_bind_param($stmt_check_nama, "s", $nama_bahan);
        mysqli_stmt_execute($stmt_check_nama);
        mysqli_stmt_store_result($stmt_check_nama);
        if (mysqli_stmt_num_rows($stmt_check_nama) > 0) {
            $errors[] = "Nama bahan '" . htmlspecialchars($nama_bahan) . "' sudah ada.";
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
    $_SESSION['form_input_bahan'] = $_POST;
    header('Location: ' . $base_url . 'tambahbahan.php');
    exit;
}

$prefix = "BHN";
$unique_part = strtoupper(substr(uniqid(), -7));
$id_bahan = $prefix . $unique_part;
if (strlen($id_bahan) > 10) $id_bahan = substr($id_bahan, 0, 10);


$query_insert = "INSERT INTO bahan (id_bahan, nama_bahan, satuan) VALUES (?, ?, ?)";
$stmt_insert = mysqli_prepare($koneksi, $query_insert);

if ($stmt_insert) {
    mysqli_stmt_bind_param($stmt_insert, "sss", $id_bahan, $nama_bahan, $satuan);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Bahan '" . htmlspecialchars($nama_bahan) . "' berhasil ditambahkan.";
        unset($_SESSION['form_input_bahan']);
        header('Location: ' . $base_url . 'bahan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan data bahan: " . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_bahan'] = $_POST;
header('Location: ' . $base_url . 'tambahbahan.php');
exit;
?>