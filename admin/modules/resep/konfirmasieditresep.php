<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/resep/";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Akses tidak sah.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$id_resep = trim($_POST['id_resep'] ?? '');
$nama_resep = trim($_POST['nama_resep'] ?? '');
$id_admin = trim($_POST['id_admin'] ?? '');
$id_kondisi = trim($_POST['id_kondisi'] ?? '');
$cara_buat = trim($_POST['cara_buat'] ?? '');

$resep_bahans_post = isset($_POST['resep_bahan']) && is_array($_POST['resep_bahan']) ? $_POST['resep_bahan'] : [];
$gizi_post = isset($_POST['gizi']) && is_array($_POST['gizi']) ? $_POST['gizi'] : [];

$errors = [];

if (empty($id_resep) || strlen($id_resep) > 10) {
    $_SESSION['error_message'] = "ID Resep tidak valid untuk diedit.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$stmt_check_resep_exist = mysqli_prepare($koneksi, "SELECT id_resep FROM resep WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt_check_resep_exist, "s", $id_resep);
mysqli_stmt_execute($stmt_check_resep_exist);
mysqli_stmt_store_result($stmt_check_resep_exist);
if (mysqli_stmt_num_rows($stmt_check_resep_exist) == 0) {
    $_SESSION['error_message'] = "Resep dengan ID " . htmlspecialchars($id_resep) . " tidak ditemukan.";
    mysqli_stmt_close($stmt_check_resep_exist);
    header('Location: ' . $base_url . 'resep.php');
    exit;
}
mysqli_stmt_close($stmt_check_resep_exist);


if (empty($nama_resep)) $errors[] = "Nama resep wajib diisi.";
if (strlen($nama_resep) > 100) $errors[] = "Nama resep maksimal 100 karakter.";

if (empty($id_admin) || strlen($id_admin) > 10) $errors[] = "Admin pembuat resep wajib dipilih.";
if (empty($id_kondisi) || strlen($id_kondisi) > 10) $errors[] = "Kondisi kesehatan wajib dipilih.";
if (empty($cara_buat)) $errors[] = "Cara membuat resep wajib diisi.";

if (empty($resep_bahans_post) || count($resep_bahans_post) == 0) {
    $errors[] = "Minimal harus ada satu bahan dalam resep.";
} else {
    foreach ($resep_bahans_post as $index => $item_bahan) {
        if (empty($item_bahan['id_bahan']) || strlen($item_bahan['id_bahan']) > 10) {
            $errors[] = "Bahan ke-" . ($index + 1) . " wajib dipilih.";
        }
        if (!isset($item_bahan['jumlah']) || $item_bahan['jumlah'] === '' || !is_numeric($item_bahan['jumlah']) || (float)$item_bahan['jumlah'] <= 0) {
            $errors[] = "Jumlah untuk bahan ke-" . ($index + 1) . " wajib diisi dengan angka valid lebih dari 0.";
        }
    }
}

$kalori = isset($gizi_post['kalori']) && $gizi_post['kalori'] !== '' ? (float)$gizi_post['kalori'] : null;
$protein = isset($gizi_post['protein']) && $gizi_post['protein'] !== '' ? (float)$gizi_post['protein'] : null;
$karbohidrat = isset($gizi_post['karbohidrat']) && $gizi_post['karbohidrat'] !== '' ? (float)$gizi_post['karbohidrat'] : null;
$lemak = isset($gizi_post['lemak']) && $gizi_post['lemak'] !== '' ? (float)$gizi_post['lemak'] : null;

$has_gizi_data = ($kalori !== null || $protein !== null || $karbohidrat !== null || $lemak !== null);

if ($has_gizi_data) {
    if ($kalori !== null && (!is_numeric($kalori) || $kalori < 0)) $errors[] = "Kalori (jika diisi) harus angka non-negatif.";
    if ($protein !== null && (!is_numeric($protein) || $protein < 0)) $errors[] = "Protein (jika diisi) harus angka non-negatif.";
    if ($karbohidrat !== null && (!is_numeric($karbohidrat) || $karbohidrat < 0)) $errors[] = "Karbohidrat (jika diisi) harus angka non-negatif.";
    if ($lemak !== null && (!is_numeric($lemak) || $lemak < 0)) $errors[] = "Lemak (jika diisi) harus angka non-negatif.";
}


if (!empty($errors)) {
    $_SESSION['error_message'] = implode("<br>", $errors);
    $_SESSION['form_input_resep_edit'] = $_POST;
    header('Location: ' . $base_url . 'editresep.php?id=' . urlencode($id_resep));
    exit;
}

mysqli_autocommit($koneksi, false);
$error_flag_transaction = false;

$query_update_resep = "UPDATE resep SET id_admin = ?, id_kondisi = ?, nama_resep = ?, cara_buat = ? WHERE id_resep = ?";
$stmt_update_resep = mysqli_prepare($koneksi, $query_update_resep);
if ($stmt_update_resep) {
    mysqli_stmt_bind_param($stmt_update_resep, "sssss", $id_admin, $id_kondisi, $nama_resep, $cara_buat, $id_resep);
    if (!mysqli_stmt_execute($stmt_update_resep)) {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mengupdate data resep utama: " . mysqli_stmt_error($stmt_update_resep);
    }
    mysqli_stmt_close($stmt_update_resep);
} else {
    $error_flag_transaction = true;
    $_SESSION['error_message'] = "Gagal mempersiapkan update resep utama: " . mysqli_error($koneksi);
}


if (!$error_flag_transaction) {
    $stmt_delete_old_bahan = mysqli_prepare($koneksi, "DELETE FROM resep_bahan WHERE id_resep = ?");
    if ($stmt_delete_old_bahan) {
        mysqli_stmt_bind_param($stmt_delete_old_bahan, "s", $id_resep);
        if (!mysqli_stmt_execute($stmt_delete_old_bahan)) {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal menghapus bahan lama: " . mysqli_stmt_error($stmt_delete_old_bahan);
        }
        mysqli_stmt_close($stmt_delete_old_bahan);
    } else {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mempersiapkan hapus bahan lama: " . mysqli_error($koneksi);
    }
}

if (!$error_flag_transaction) {
    $query_insert_bahan_resep = "INSERT INTO resep_bahan (id_resep, id_bahan, jumlah) VALUES (?, ?, ?)";
    $stmt_insert_bahan_resep = mysqli_prepare($koneksi, $query_insert_bahan_resep);
    if ($stmt_insert_bahan_resep) {
        foreach ($resep_bahans_post as $item_bahan) {
            $id_bahan_item = $item_bahan['id_bahan'];
            $jumlah_item = (string)$item_bahan['jumlah'];
            mysqli_stmt_bind_param($stmt_insert_bahan_resep, "sss", $id_resep, $id_bahan_item, $jumlah_item);
            if (!mysqli_stmt_execute($stmt_insert_bahan_resep)) {
                $error_flag_transaction = true;
                $_SESSION['error_message'] = "Gagal menyimpan bahan resep baru: " . mysqli_stmt_error($stmt_insert_bahan_resep);
                break;
            }
        }
        mysqli_stmt_close($stmt_insert_bahan_resep);
    } else {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mempersiapkan statement bahan resep baru: " . mysqli_error($koneksi);
    }
}


if (!$error_flag_transaction) {
    $stmt_delete_old_gizi = mysqli_prepare($koneksi, "DELETE FROM gizi_resep WHERE id_resep = ?");
    if ($stmt_delete_old_gizi) {
        mysqli_stmt_bind_param($stmt_delete_old_gizi, "s", $id_resep);
        if (!mysqli_stmt_execute($stmt_delete_old_gizi)) {
             $error_flag_transaction = true;
             $_SESSION['error_message'] = "Gagal menghapus data gizi lama: " . mysqli_stmt_error($stmt_delete_old_gizi);
        }
        mysqli_stmt_close($stmt_delete_old_gizi);
    } else {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mempersiapkan hapus gizi lama: " . mysqli_error($koneksi);
    }
}

if (!$error_flag_transaction && $has_gizi_data) {
    $query_insert_gizi = "INSERT INTO gizi_resep (id_resep, kalori, protein, karbohidrat, lemak) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_gizi = mysqli_prepare($koneksi, $query_insert_gizi);
    if ($stmt_insert_gizi) {
        mysqli_stmt_bind_param($stmt_insert_gizi, "sdddd", $id_resep, $kalori, $protein, $karbohidrat, $lemak);
        if (!mysqli_stmt_execute($stmt_insert_gizi)) {
            $error_flag_transaction = true;
            $_SESSION['error_message'] = "Gagal menyimpan data gizi resep baru: " . mysqli_stmt_error($stmt_insert_gizi);
        }
        mysqli_stmt_close($stmt_insert_gizi);
    } else {
        $error_flag_transaction = true;
        $_SESSION['error_message'] = "Gagal mempersiapkan statement gizi resep baru: " . mysqli_error($koneksi);
    }
}


if ($error_flag_transaction) {
    mysqli_rollback($koneksi);
    $_SESSION['form_input_resep_edit'] = $_POST;
    header('Location: ' . $base_url . 'editresep.php?id=' . urlencode($id_resep));
} else {
    mysqli_commit($koneksi);
    $_SESSION['success_message'] = "Resep '" . htmlspecialchars($nama_resep) . "' berhasil diupdate.";
    unset($_SESSION['form_input_resep_edit']);
    header('Location: ' . $base_url . 'resep.php');
}
mysqli_autocommit($koneksi, true);
exit;
?>