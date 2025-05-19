<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/gizi/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'tambahgizi.php');
    exit;
}

$id_resep = trim($_POST['id_resep'] ?? '');
$kalori = isset($_POST['kalori']) && $_POST['kalori'] !== '' ? (float)$_POST['kalori'] : null;
$protein = isset($_POST['protein']) && $_POST['protein'] !== '' ? (float)$_POST['protein'] : null;
$karbohidrat = isset($_POST['karbohidrat']) && $_POST['karbohidrat'] !== '' ? (float)$_POST['karbohidrat'] : null;
$lemak = isset($_POST['lemak']) && $_POST['lemak'] !== '' ? (float)$_POST['lemak'] : null;

$errors = [];

if (empty($id_resep) || strlen($id_resep) > 10) {
    $errors[] = "Resep wajib dipilih.";
} else {
    $stmt_check_resep_gizi = mysqli_prepare($koneksi, "SELECT id_gizi_resep FROM gizi_resep WHERE id_resep = ?");
    if ($stmt_check_resep_gizi) {
        mysqli_stmt_bind_param($stmt_check_resep_gizi, "s", $id_resep);
        mysqli_stmt_execute($stmt_check_resep_gizi);
        mysqli_stmt_store_result($stmt_check_resep_gizi);
        if (mysqli_stmt_num_rows($stmt_check_resep_gizi) > 0) {
            $errors[] = "Resep yang dipilih sudah memiliki data gizi. Silakan edit data gizi yang sudah ada.";
        }
        mysqli_stmt_close($stmt_check_resep_gizi);
    } else {
         $errors[] = "Gagal memeriksa data gizi resep: " . mysqli_error($koneksi);
    }
}

if ($kalori === null && $protein === null && $karbohidrat === null && $lemak === null) {
    $errors[] = "Minimal satu nilai gizi (Kalori, Protein, Karbohidrat, atau Lemak) harus diisi.";
}
if ($kalori !== null && (!is_numeric($kalori) || $kalori < 0)) $errors[] = "Kalori (jika diisi) harus angka non-negatif.";
if ($protein !== null && (!is_numeric($protein) || $protein < 0)) $errors[] = "Protein (jika diisi) harus angka non-negatif.";
if ($karbohidrat !== null && (!is_numeric($karbohidrat) || $karbohidrat < 0)) $errors[] = "Karbohidrat (jika diisi) harus angka non-negatif.";
if ($lemak !== null && (!is_numeric($lemak) || $lemak < 0)) $errors[] = "Lemak (jika diisi) harus angka non-negatif.";


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_gizi'] = $_POST;
    header('Location: ' . $base_url . 'tambahgizi.php');
    exit;
}

$prefix = "GZR";
$unique_part = strtoupper(substr(uniqid(), -7));
$id_gizi_resep = $prefix . $unique_part;
if (strlen($id_gizi_resep) > 10) $id_gizi_resep = substr($id_gizi_resep, 0, 10);

$query_insert = "INSERT INTO gizi_resep (id_gizi_resep, id_resep, kalori, protein, karbohidrat, lemak) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_insert = mysqli_prepare($koneksi, $query_insert);

if ($stmt_insert) {
    mysqli_stmt_bind_param($stmt_insert, "ssdddd", $id_gizi_resep, $id_resep, $kalori, $protein, $karbohidrat, $lemak);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Data gizi berhasil ditambahkan untuk resep.";
        unset($_SESSION['form_input_gizi']);
        header('Location: ' . $base_url . 'gizi.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan data gizi: " . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_gizi'] = $_POST;
header('Location: ' . $base_url . 'tambahgizi.php');
exit;
?>