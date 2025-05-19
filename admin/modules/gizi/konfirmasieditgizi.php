<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/modules/gizi/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$id_gizi_resep = trim($_POST['id_gizi_resep'] ?? '');
$kalori = isset($_POST['kalori']) && $_POST['kalori'] !== '' ? (float)$_POST['kalori'] : null;
$protein = isset($_POST['protein']) && $_POST['protein'] !== '' ? (float)$_POST['protein'] : null;
$karbohidrat = isset($_POST['karbohidrat']) && $_POST['karbohidrat'] !== '' ? (float)$_POST['karbohidrat'] : null;
$lemak = isset($_POST['lemak']) && $_POST['lemak'] !== '' ? (float)$_POST['lemak'] : null;

$errors = [];

if (empty($id_gizi_resep) || strlen($id_gizi_resep) > 10) {
    $_SESSION['error_message'] = "ID Gizi tidak valid untuk diedit.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$stmt_check_gizi_exist = mysqli_prepare($koneksi, "SELECT id_gizi_resep, id_resep FROM gizi_resep WHERE id_gizi_resep = ?");
mysqli_stmt_bind_param($stmt_check_gizi_exist, "s", $id_gizi_resep);
mysqli_stmt_execute($stmt_check_gizi_exist);
$result_check = mysqli_stmt_get_result($stmt_check_gizi_exist);
$gizi_to_edit_data = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check_gizi_exist);

if (!$gizi_to_edit_data) {
    $_SESSION['error_message'] = "Data gizi dengan ID " . htmlspecialchars($id_gizi_resep) . " tidak ditemukan.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
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
    $_SESSION['form_input_gizi_edit'] = $_POST;
    $_SESSION['form_input_gizi_edit']['nama_resep'] = $_POST['nama_resep_display'] ?? 'N/A'; // Ambil dari hidden input jika perlu
    header('Location: ' . $base_url . 'editgizi.php?id=' . urlencode($id_gizi_resep));
    exit;
}

$query_update = "UPDATE gizi_resep SET kalori = ?, protein = ?, karbohidrat = ?, lemak = ? WHERE id_gizi_resep = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);

if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, "dddds", $kalori, $protein, $karbohidrat, $lemak, $id_gizi_resep);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['success_message'] = "Data gizi berhasil diupdate.";
        } else {
            $_SESSION['success_message'] = "Tidak ada perubahan data gizi."; 
        }
        unset($_SESSION['form_input_gizi_edit']);
        header('Location: ' . $base_url . 'gizi.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate data gizi: " . mysqli_stmt_error($stmt_update);
    }
    mysqli_stmt_close($stmt_update);
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan pada sistem (prepare statement): " . mysqli_error($koneksi);
}

$_SESSION['form_input_gizi_edit'] = $_POST;
$_SESSION['form_input_gizi_edit']['nama_resep'] = $_POST['nama_resep_display'] ?? 'N/A'; 
header('Location: ' . $base_url . 'editgizi.php?id=' . urlencode($id_gizi_resep));
exit;
?>