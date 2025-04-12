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

// Proses tambah bahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_bahan = $_POST['id_bahan'];
    $nama_bahan = $_POST['nama_bahan'];
    $satuan = $_POST['satuan'];
    $sql = "INSERT INTO bahan (id_bahan, nama_bahan, satuan) 
            VALUES ('$id_bahan', '$nama_bahan', '$satuan')";
    $conn->query($sql);
    header('Location: manage_bahan.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Bahan - WellnessPlate</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2C3E50;
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
            color: #2C3E50;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            background-color: #27AE60;
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
        <a href="manage_bahan.php" style="color: #fff;">Kembali</a>
    </div>
    <div class="content">
        <h2>Tambah Bahan</h2>
        <form method="POST">
            <label for="id_bahan">ID Bahan:</label>
            <input type="text" id="id_bahan" name="id_bahan" required>
            <label for="nama_bahan">Nama Bahan:</label>
            <input type="text" id="nama_bahan" name="nama_bahan" required>
            <label for="satuan">Satuan:</label>
            <input type="text" id="satuan" name="satuan" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>