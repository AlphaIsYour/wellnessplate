<?php
session_start();
header('Content-Type: application/json');

// Cek session admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kondisi = $conn->real_escape_string($_POST['id_kondisi']);
    $nama_kondisi = $conn->real_escape_string($_POST['nama_kondisi']);

    // Validasi input
    if (empty($id_kondisi) || empty($nama_kondisi)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Cek apakah ID sudah ada
    $sql_check = "SELECT id_kondisi FROM kondisi_kesehatan WHERE id_kondisi = '$id_kondisi'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ID Kondisi sudah ada!']);
        exit;
    }

    // Insert data
    $sql = "INSERT INTO kondisi_kesehatan (id_kondisi, nama_kondisi) VALUES ('$id_kondisi', '$nama_kondisi')";
    if ($conn->query($sql) ){
        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}

$conn->close();
?>