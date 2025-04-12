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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kondisi = $_POST['id_kondisi'];
    $nama_kondisi = $_POST['nama_kondisi'];
    $sql = "INSERT INTO kondisi_kesehatan (id_kondisi, nama_kondisi) VALUES ('$id_kondisi', '$nama_kondisi')";
    $conn->query($sql);
    header('Location: manage_kondisi.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kondisi Kesehatan - WellnessPlate</title>
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
        <a href="manage_kondisi.php" style="color: #fff;">Kembali</a>
    </div>
    <div class="content">
        <h2>Tambah Kondisi Kesehatan</h2>
        <form method="POST">
            <label for="id_kondisi">ID Kondisi:</label>
            <input type="text" id="id_kondisi" name="id_kondisi" required>
            <label for="nama_kondisi">Nama Kondisi:</label>
            <input type="text" id="nama_kondisi" name="nama_kondisi" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>