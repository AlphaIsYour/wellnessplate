<?php
session_start();
header('Content-Type: application/json'); // Tambahkan header JSON

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Password tidak di-escape karena akan di-hash

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            echo json_encode([
                'success' => true,
                'id_admin' => $admin['id_admin'],
                'message' => 'Login berhasil'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Password salah'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Username tidak ditemukan'
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid'
    ]);
}
$conn->close();
?>