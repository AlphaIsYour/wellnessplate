<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? null;
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;

    if (empty($id_user) || empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Field wajib harus diisi']);
        exit;
    }

    // Cek apakah id_user, username, atau email sudah ada
    $sql = "SELECT id_user, username, email FROM users WHERE id_user = ? OR username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id_user, $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        if ($existing['id_user'] == $id_user) {
            echo json_encode(['success' => false, 'message' => 'ID User sudah digunakan']);
        } elseif ($existing['username'] == $username) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email sudah digunakan']);
        }
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru
    $sql = "INSERT INTO users (id_user, username, email, password, nama_lengkap, tanggal_lahir, jenis_kelamin) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $id_user, $username, $email, $hashed_password, $nama_lengkap, $tanggal_lahir, $jenis_kelamin);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan user']);
    }

    $stmt->close();
}

$conn->close();
?>