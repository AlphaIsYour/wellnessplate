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

// Ambil semua data kondisi kesehatan
$sql = "SELECT * FROM kondisi_kesehatan";
$result = $conn->query($sql);

// Hapus kondisi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM kondisi_kesehatan WHERE id_kondisi = '$id'";
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
    <title>Kelola Kondisi Kesehatan - WellnessPlate</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="header">
        <div class="logo">WellnessPlate Admin</div>
        <a href="dashboard.php" style="color: #fff;">Kembali ke Dashboard</a>
    </div>
    <div class="content">
        <h2>Kelola Kondisi Kesehatan</h2>
        <button class="btn btn-tambah" onclick="openPopup('add-kondisi-popup')">Tambah Kondisi</button>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Kondisi</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id_kondisi']; ?></td>
                <td><?php echo $row['nama_kondisi']; ?></td>
                <td>
                    <a href="edit_kondisi.php?id=<?php echo $row['id_kondisi']; ?>" class="btn btn-edit">Edit</a>
                    <a href="?hapus=<?php echo $row['id_kondisi']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <!-- Pop-up untuk Tambah Kondisi -->
        <div id="add-kondisi-popup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('add-kondisi-popup')">&times;</span>
                <h3>Tambah Kondisi Kesehatan</h3>
                <form id="add-kondisi-form">
                    <label for="id_kondisi">ID Kondisi:</label>
                    <input type="text" id="id_kondisi" name="id_kondisi" required>
                    <label for="nama_kondisi">Nama Kondisi:</label>
                    <input type="text" id="nama_kondisi" name="nama_kondisi" required>
                    <button type="submit" class="btn btn-tambah">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>
</html>

<?php $conn->close(); ?>