<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/kondisi_kesehatan/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

$id_kondisi = trim($_POST['id_kondisi'] ?? '');
$nama_kondisi = trim($_POST['nama_kondisi'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

$errors = [];

if (empty($id_kondisi) || strlen($id_kondisi) > 10) {
    $_SESSION['error_message'] = "ID Kondisi Kesehatan tidak valid untuk diedit.";
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

$stmt_curr = mysqli_prepare($koneksi, "SELECT nama_kondisi FROM kondisi_kesehatan WHERE id_kondisi = ?");
mysqli_stmt_bind_param($stmt_curr, "s", $id_kondisi);
mysqli_stmt_execute($stmt_curr);
$result_curr = mysqli_stmt_get_result($stmt_curr);
$current_kondisi_data = mysqli_fetch_assoc($result_curr);
mysqli_stmt_close($stmt_curr);

if (!$current_kondisi_data) {
    $_SESSION['error_message'] = "Kondisi kesehatan dengan ID " . htmlspecialchars($id_kondisi) . " tidak ditemukan.";
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}


if (empty($nama_kondisi)) {
    $errors[] = "Nama kondisi wajib diisi.";
} elseif (strlen($nama_kondisi) > 100) {
    $errors[] = "Nama kondisi maksimal 100 karakter.";
} elseif ($nama_kondisi !== $current_kondisi_data['nama_kondisi']) {
    $stmt_check_nama = mysqli_prepare($koneksi, "SELECT id_kondisi FROM kondisi_kesehatan WHERE nama_kondisi = ? AND id_kondisi != ?");
    if ($stmt_check_nama) {
        mysqli_stmt_bind_param($stmt_check_nama, "ss", $nama_kondisi, $id_kondisi);
        mysqli_stmt_execute($stmt_check_nama);
        mysqli_stmt_store_result($stmt_check_nama);
        if (mysqli_stmt_num_rows($stmt_check_nama) > 0) {
            $errors[] = "Nama kondisi '" . htmlspecialchars($nama_kondisi) . "' sudah ada untuk kondisi lain.";
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
    $_SESSION['form_input_kondisi_edit'] = $_POST; 
    header('Location: ' . $base_url . 'editkondisikesehatan.php?id=' . urlencode($id_kondisi));
    exit;
}

$query_update = "UPDATE kondisi_kesehatan SET nama_kondisi = ?, deskripsi = ? WHERE id_kondisi = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);

if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, "sss", $nama_kondisi, $deskripsi, $id_kondisi);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['success_message'] = "Data kondisi kesehatan '" . htmlspecialchars($nama_kondisi) . "' berhasil diupdate.";
        } else {
            $_SESSION['success_message'] = "Tidak ada perubahan data untuk kondisi '" . htmlspecialchars($nama_kondisi) . "'."; 
        }
        unset($_SESSION['form_input_kondisi_edit']);
        header('Location: ' . $base_url . 'kondisikesehatan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate data kondisi kesehatan: " . mysqli_stmt_error($stmt_update);
    }
    mysqli_stmt_close($stmt_update);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_kondisi_edit'] = $_POST;
header('Location: ' . $base_url . 'editkondisikesehatan.php?id=' . urlencode($id_kondisi));
exit;
?>