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

// Ambil semua data resep dengan join ke tabel kondisi_kesehatan
$sql = "SELECT resep.id_resep, resep.nama_resep, kondisi_kesehatan.nama_kondisi 
        FROM resep 
        LEFT JOIN kondisi_kesehatan ON resep.id_kondisi = kondisi_kesehatan.id_kondisi";
$result = $conn->query($sql);

// Hapus resep
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM resep WHERE id_resep = '$id'";
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
    <title>Kelola Resep - WellnessPlate</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #E0E0E0;
        }
        th {
            background-color: #2C3E50;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #F4F4F4;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            border-radius: 3px;
        }
        .btn-tambah {
            background-color: #27AE60;
            margin-bottom: 10px;
            display: inline-block;
        }
        .btn-edit {
            background-color: #27AE60;
        }
        .btn-hapus {
            background-color: #E74C3C;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">WellnessPlate Admin</div>
        <a href="dashboard.php" style="color: #fff;">Kembali ke Dashboard</a>
    </div>
    <div class="content">
        <h2>Kelola Resep</h2>
        <a href="add_resep.php" class="btn btn-tambah">Tambah Resep</a>
        <table>
            <tr>
                <th>ID Resep</th>
                <th>Nama Resep</th>
                <th>Kondisi Kesehatan</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id_resep']; ?></td>
                <td><?php echo $row['nama_resep']; ?></td>
                <td><?php echo $row['nama_kondisi'] ?: 'Tidak ada kondisi'; ?></td>
                <td>
                    <a href="edit_resep.php?id=<?php echo $row['id_resep']; ?>" class="btn btn-edit">Edit</a>
                    <a href="?hapus=<?php echo $row['id_resep']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>