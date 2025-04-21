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

$sql_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan";
$result_kondisi = $conn->query($sql_kondisi);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_resep = $_POST['id_resep'];
    $id_admin = $_SESSION['admin_id'];
    $id_kondisi = $_POST['id_kondisi'];
    $nama_resep = $_POST['nama_resep'];
    $sql = "INSERT INTO resep (id_resep, id_admin, id_kondisi, nama_resep) 
            VALUES ('$id_resep', '$id_admin', '$id_kondisi', '$nama_resep')";
    $conn->query($sql);
    header('Location: manage_resep.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resep - WellnessPlate</title>
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
        <a href="manage_resep.php" style="color: #fff;">Kembali</a>
    </div>
    <div class="content">
        <h2>Tambah Resep</h2>
        <form method="POST">
            <label for="id_resep">ID Resep:</label>
            <input type="text" id="id_resep" name="id_resep" required>
            <label for="id_kondisi">Kondisi Kesehatan:</label>
            <select id="id_kondisi" name="id_kondisi" required>
                <option value="">Pilih Kondisi</option>
                <?php while ($row = $result_kondisi->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id_kondisi']; ?>">
                        <?php echo $row['nama_kondisi']; ?>
                    </option>
                <?php } ?>
            </select>
            <label for="nama_resep">Nama Resep:</label>
            <input type="text" id="nama_resep" name="nama_resep" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>