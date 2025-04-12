<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kondisi = $_POST['id_kondisi'];
    $nama_kondisi = $_POST['nama_kondisi'];

    // Cek apakah ID sudah ada
    $sql_check = "SELECT id_kondisi FROM kondisi_kesehatan WHERE id_kondisi = '$id_kondisi'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ID Kondisi sudah ada!']);
        exit;
    }

    // Insert data
    $sql = "INSERT INTO kondisi_kesehatan (id_kondisi, nama_kondisi) VALUES ('$id_kondisi', '$nama_kondisi')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $conn->error]);
    }
}

$conn->close();
?>