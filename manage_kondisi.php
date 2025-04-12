<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.html'); // Fix redirect (ganti index.html jadi login.php)
    exit;
}

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil informasi admin yang login
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT nama FROM admin WHERE id_admin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin['nama'];

// Ambil semua data kondisi kesehatan
$sql = "SELECT * FROM kondisi_kesehatan";
$result = $conn->query($sql);

// Hapus kondisi
if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);
    $sql = "DELETE FROM kondisi_kesehatan WHERE id_kondisi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>WellnessPlate</h3>
            <button class="toggle-sidebar" id="toggle-sidebar"><i data-feather="menu"></i></button>
        </div>
        <ul>
            <li><a href="manage_kondisi.php" aria-label="Kelola Kondisi Kesehatan" class="active"><i data-feather="heart"></i><span>Kondisi Kesehatan</span></a></li>
            <li><a href="manage_resep.php" aria-label="Kelola Resep"><i data-feather="book"></i><span>Resep</span></a></li>
            <li><a href="manage_bahan.php" aria-label="Kelola Bahan"><i data-feather="shopping-bag"></i><span>Bahan</span></a></li>
            <li><a href="manage_gizi.php" aria-label="Kelola Gizi"><i data-feather="bar-chart-2"></i><span>Gizi</span></a></li>
            <li><a href="manage_resep_bahan.php" aria-label="Kelola Resep Bahan"><i data-feather="link"></i><span>Resep Bahan</span></a></li>
            <li><a href="manage_users.php" aria-label="Kelola Users"><i data-feather="users"></i><span>Users</span></a></li>
            <li><a href="manage_admins.php" aria-label="Kelola Admins"><i data-feather="user-check"></i><span>Admins</span></a></li>
            <li><a href="dashboard.php" aria-label="Kembali ke Dashboard"><i data-feather="home"></i><span>Dashboard</span></a></li>
        </ul>
    </div>
    <div class="main">
        <div class="header">
            <div class="logo">
                <span class="logo-text">WellnessPlate Admin</span>
            </div>
            <div class="admin-info">
                <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=8b5cf6&color=fff" alt="Avatar">
                    <div class="dropdown-content">
                        <a href="edit_profile.php">Edit Profil</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <h2>Kelola Kondisi Kesehatan</h2>
            <p>Atur data kondisi kesehatan untuk sistem WellnessPlate.</p>
            <button class="btn btn-tambah" onclick="openPopup('add-kondisi-popup')"><i data-feather="plus"></i> Tambah Kondisi</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="table-row">
                            <td><?php echo htmlspecialchars($row['id_kondisi']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_kondisi']); ?></td>
                            <td>
                                <a href="edit_kondisi.php?id=<?php echo $row['id_kondisi']; ?>" class="btn btn-edit" data-tooltip="Edit kondisi"><i data-feather="edit"></i></a>
                                <a href="?hapus=<?php echo $row['id_kondisi']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus?')" data-tooltip="Hapus kondisi"><i data-feather="trash-2"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pop-up untuk Tambah Kondisi -->
            <div id="add-kondisi-popup" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('add-kondisi-popup')">×</span>
                    <h3>Tambah Kondisi Kesehatan</h3>
                    <form id="add-kondisi-form">
                        <div class="input-group">
                            <input type="text" id="id_kondisi" name="id_kondisi" placeholder=" " required>
                            <label for="id_kondisi">ID Kondisi</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="nama_kondisi" name="nama_kondisi" placeholder=" " required>
                            <label for="nama_kondisi">Nama Kondisi</label>
                        </div>
                        <button type="submit" class="btn btn-tambah"><i data-feather="save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
    <script>
        // Animasi fade-in untuk baris tabel
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>