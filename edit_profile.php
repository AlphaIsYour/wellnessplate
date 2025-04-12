<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$admin_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $sql = "UPDATE admin SET nama = '$nama', email = '$email' WHERE id_admin = '$admin_id'";
    $conn->query($sql);
    header('Location: dashboard.php');
    exit;
}

$sql = "SELECT nama, email FROM admin WHERE id_admin = '$admin_id'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - WellnessPlate</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2c3e50;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            background-color: #27ae60;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">WellnessPlate Admin</div>
        <a href="dashboard.php" style="color: #fff;">Kembali ke Dashboard</a>
    </div>
    <div class="content">
        <h2>Edit Profil</h2>
        <form method="POST">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" value="<?php echo $admin['nama']; ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>