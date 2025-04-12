<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_gizi = $_POST['id_gizi'] ?? '';
    $nama_gizi = $_POST['nama_gizi'] ?? '';
    $jumlah_kalori = $_POST['jumlah_kalori'] ?? '';

    if (empty($id_gizi) || empty($nama_gizi) || empty($jumlah_kalori)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }

    // Cek apakah id_gizi sudah ada
    $sql = "SELECT id_gizi FROM gizi WHERE id_gizi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_gizi);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ID Gizi sudah digunakan']);
        exit;
    }

    // Insert gizi baru
    $sql = "INSERT INTO gizi (id_gizi, nama_gizi, jumlah_kalori) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isd", $id_gizi, $nama_gizi, $jumlah_kalori);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan gizi']);
    }

    $stmt->close();
}

$conn->close();
?>