<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_resep_bahan = $_POST['id_resep_bahan'] ?? '';
    $id_resep = $_POST['id_resep'] ?? '';
    $id_bahan = $_POST['id_bahan'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';

    if (empty($id_resep_bahan) || empty($id_resep) || empty($id_bahan) || empty($jumlah)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }

    $sql = "SELECT id_resep_bahan FROM resep_bahan WHERE id_resep_bahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_resep_bahan);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ID Resep Bahan sudah digunakan']);
        exit;
    }

    $sql = "INSERT INTO resep_bahan (id_resep_bahan, id_resep, id_bahan, jumlah) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $id_resep_bahan, $id_resep, $id_bahan, $jumlah);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan resep bahan']);
    }

    $stmt->close();
}

$conn->close();
?>