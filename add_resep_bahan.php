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

// Ambil daftar resep dan bahan untuk dropdown
$sql_resep = "SELECT id_resep, nama_resep FROM resep";
$result_resep = $conn->query($sql_resep);

$sql_bahan = "SELECT id_bahan, nama_bahan FROM bahan";
$result_bahan = $conn->query($sql_bahan);

// Proses tambah resep_bahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_resep_bahan = $_POST['id_resep_bahan'];
    $id_resep = $_POST['id_resep'];
    $id_bahan = $_POST['id_bahan'];
    $jumlah = $_POST['jumlah'];
    $sql = "INSERT INTO resep_bahan (id_resep_bahan, id_resep, id_bahan, jumlah) 
            VALUES ('$id_resep_bahan', '$id_resep', '$id_bahan', $jumlah)";
    $conn->query($sql);
    header('Location: manage_resep_bahan.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resep Bahan - WellnessPlate</title>
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
        <a href="manage_resep_bahan.php" style="color: #fff;">Kembali</a>
    </div>
    <div class="content">
        <h2>Tambah Resep Bahan</h2>
        <form method="POST">
            <label for="id_resep_bahan">ID Resep Bahan:</label>
            <input type="text" id="id_resep_bahan" name="id_resep_bahan" required>
            <label for="id_resep">Resep:</label>
            <select id="id_resep" name="id_resep" required>
                <option value="">Pilih Resep</option>
                <?php while ($row = $result_resep->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id_resep']; ?>">
                        <?php echo $row['nama_resep']; ?>
                    </option>
                <?php } ?>
            </select>
            <label for="id_bahan">Bahan:</label>
            <select id="id_bahan" name="id_bahan" required>
                <option value="">Pilih Bahan</option>
                <?php while ($row = $result_bahan->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id_bahan']; ?>">
                        <?php echo $row['nama_bahan']; ?>
                    </option>
                <?php } ?>
            </select>
            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>