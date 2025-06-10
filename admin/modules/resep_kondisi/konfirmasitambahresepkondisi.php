<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /index.php?error=Silakan login terlebih dahulu.");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

$base_url = "/admin/modules/resep_kondisi/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_resep = isset($_POST['id_resep']) ? $_POST['id_resep'] : '';
    $id_kondisi = isset($_POST['id_kondisi']) ? $_POST['id_kondisi'] : '';

    // Validasi input
    if (empty($id_resep) || empty($id_kondisi)) {
        $_SESSION['error_message'] = "Semua field harus diisi!";
        $_SESSION['form_input'] = $_POST;
        header("Location: " . $base_url . "tambahresepkondisi.php");
        exit;
    }

    // Cek apakah kombinasi resep dan kondisi sudah ada
    $check_query = "SELECT id_resep_kondisi FROM resep_kondisi WHERE id_resep = ? AND id_kondisi = ?";
    $stmt_check = mysqli_prepare($koneksi, $check_query);
    mysqli_stmt_bind_param($stmt_check, "ss", $id_resep, $id_kondisi);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['error_message'] = "Kombinasi resep dan kondisi kesehatan ini sudah ada!";
        $_SESSION['form_input'] = $_POST;
        header("Location: " . $base_url . "tambahresepkondisi.php");
        exit;
    }
    mysqli_stmt_close($stmt_check);

    // Insert data
    $insert_query = "INSERT INTO resep_kondisi (id_resep, id_kondisi, created_at) VALUES (?, ?, NOW())";
    $stmt_insert = mysqli_prepare($koneksi, $insert_query);
    
    if ($stmt_insert) {
        mysqli_stmt_bind_param($stmt_insert, "ss", $id_resep, $id_kondisi);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['success_message'] = "Data berhasil ditambahkan!";
            header("Location: " . $base_url . "resepkondisi.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan data: " . mysqli_error($koneksi);
            $_SESSION['form_input'] = $_POST;
            header("Location: " . $base_url . "tambahresepkondisi.php");
            exit;
        }
        mysqli_stmt_close($stmt_insert);
    } else {
        $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
        $_SESSION['form_input'] = $_POST;
        header("Location: " . $base_url . "tambahresepkondisi.php");
        exit;
    }
} else {
    header("Location: " . $base_url . "resepkondisi.php");
    exit;
} 