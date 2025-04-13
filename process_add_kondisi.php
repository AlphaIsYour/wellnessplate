<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bahan = $_POST['id_bahan'] ?? '';
    $nama_bahan = $_POST['nama_bahan'] ?? '';
    $satuan = $_POST['satuan'] ?? '';

    if (empty($id_bahan) || empty($nama_bahan) || empty($satuan)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }

    $sql = "SELECT id_bahan FROM bahan WHERE id_bahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_bahan);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ID Bahan sudah digunakan']);
        exit;
    }

    $sql = "INSERT INTO bahan (id_bahan, nama_bahan, satuan) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id_bahan, $nama_bahan, $satuan);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan bahan']);
    }

    $stmt->close();
}

$conn->close();
?>