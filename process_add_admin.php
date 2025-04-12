<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_admin = $_POST['id_admin'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($id_admin) || empty($nama) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }

    // Cek apakah id_admin atau email sudah ada
    $sql = "SELECT id_admin, email FROM admin WHERE id_admin = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_admin, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        if ($existing['id_admin'] == $id_admin) {
            echo json_encode(['success' => false, 'message' => 'ID Admin sudah digunakan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email sudah digunakan']);
        }
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin baru
    $sql = "INSERT INTO admin (id_admin, nama, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_admin, $nama, $email, $hashed_password);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan admin']);
    }

    $stmt->close();
}

$conn->close();
?>