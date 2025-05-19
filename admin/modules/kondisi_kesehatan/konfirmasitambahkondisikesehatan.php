<?php
require_once '../../koneksi.php';

$base_url = "/modules/kondisi_kesehatan/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'tambahkondisikesehatan.php');
    exit;
}

$nama_kondisi = trim($_POST['nama_kondisi'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

$errors = [];

if (empty($nama_kondisi)) {
    $errors[] = "Nama kondisi wajib diisi.";
} elseif (strlen($nama_kondisi) > 100) {
    $errors[] = "Nama kondisi maksimal 100 karakter.";
} else {
    $stmt_check_nama = mysqli_prepare($koneksi, "SELECT id_kondisi FROM kondisi_kesehatan WHERE nama_kondisi = ?");
    if ($stmt_check_nama) {
        mysqli_stmt_bind_param($stmt_check_nama, "s", $nama_kondisi);
        mysqli_stmt_execute($stmt_check_nama);
        mysqli_stmt_store_result($stmt_check_nama);
        if (mysqli_stmt_num_rows($stmt_check_nama) > 0) {
            $errors[] = "Nama kondisi '" . htmlspecialchars($nama_kondisi) . "' sudah ada.";
        }
        mysqli_stmt_close($stmt_check_nama);
    } else {
        $errors[] = "Gagal memeriksa nama kondisi: " . mysqli_error($koneksi);
    }
}

if (empty($deskripsi)) {
    $errors[] = "Deskripsi wajib diisi.";
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_kondisi'] = $_POST;
    header('Location: ' . $base_url . 'tambahkondisikesehatan.php');
    exit;
}

$prefix = "KND";
$unique_part = strtoupper(substr(uniqid(), -7));
$id_kondisi = $prefix . $unique_part;
if (strlen($id_kondisi) > 10) $id_kondisi = substr($id_kondisi, 0, 10);


$query_insert = "INSERT INTO kondisi_kesehatan (id_kondisi, nama_kondisi, deskripsi) VALUES (?, ?, ?)";
$stmt_insert = mysqli_prepare($koneksi, $query_insert);

if ($stmt_insert) {
    mysqli_stmt_bind_param($stmt_insert, "sss", $id_kondisi, $nama_kondisi, $deskripsi);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Kondisi kesehatan '" . htmlspecialchars($nama_kondisi) . "' berhasil ditambahkan.";
        unset($_SESSION['form_input_kondisi']);
        header('Location: ' . $base_url . 'kondisikesehatan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan data kondisi kesehatan: " . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_kondisi'] = $_POST;
header('Location: ' . $base_url . 'tambahkondisikesehatan.php');
exit;
?>