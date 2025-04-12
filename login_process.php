<?php
session_start();
$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: login.php?error=1');
            exit;
        }
    } else {
        header('Location: login.php?error=1');
        exit;
    }
}
$conn->close();
?>