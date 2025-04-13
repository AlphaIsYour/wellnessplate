<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.html');
    exit;
}

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT nama FROM admin WHERE id_admin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin['nama'];

$sql = "SELECT resep.id_resep, resep.nama_resep, kondisi_kesehatan.nama_kondisi 
        FROM resep 
        LEFT JOIN kondisi_kesehatan ON resep.id_kondisi = kondisi_kesehatan.id_kondisi";
$result = $conn->query($sql);

if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);
    $sql = "DELETE FROM resep WHERE id_resep = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
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
            <li><a href="dashboard.php" aria-label="Kembali ke Dashboard"><i data-feather="home"></i><span>Dashboard</span></a></li>
            <li><a href="manage_kondisi.php" aria-label="Kelola Kondisi Kesehatan"><i data-feather="heart"></i><span>Kondisi Kesehatan</span></a></li>
            <li><a href="manage_resep.php" aria-label="Kelola Resep" class="active"><i data-feather="book"></i><span>Resep</span></a></li>
            <li><a href="manage_bahan.php" aria-label="Kelola Bahan"><i data-feather="shopping-bag"></i><span>Bahan</span></a></li>
            <li><a href="manage_gizi.php" aria-label="Kelola Gizi"><i data-feather="bar-chart-2"></i><span>Gizi</span></a></li>
            <li><a href="manage_resep_bahan.php" aria-label="Kelola Resep Bahan"><i data-feather="link"></i><span>Resep Bahan</span></a></li>
            <li><a href="manage_users.php" aria-label="Kelola Users"><i data-feather="users"></i><span>Users</span></a></li>
            <li><a href="manage_admins.php" aria-label="Kelola Admins"><i data-feather="user-check"></i><span>Admins</span></a></li>
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
            <h2>Kelola Resep</h2>
            <p>Atur data resep untuk sistem WellnessPlate.</p>
            <button class="btn btn-tambah" onclick="openPopup('add-resep-popup')"><i data-feather="plus"></i> Tambah Resep</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Resep</th>
                            <th>Nama Resep</th>
                            <th>Kondisi Kesehatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_resep']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_resep']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_kondisi'] ?: 'Tidak ada kondisi'); ?></td>
                            <td>
                                <a href="edit_resep.php?id=<?php echo $row['id_resep']; ?>" class="btn btn-edit" data-tooltip="Edit resep"><i data-feather="edit"></i></a>
                                <a href="?hapus=<?php echo $row['id_resep']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus?')" data-tooltip="Hapus resep"><i data-feather="trash-2"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="add-resep-popup" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('add-resep-popup')">×</span>
                    <h3>Tambah Resep</h3>
                    <form id="add-resep-form">
                        <div class="input-group">
                            <input type="text" id="id_resep" name="id_resep" placeholder=" " required>
                            <label for="id_resep">ID Resep</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="nama_resep" name="nama_resep" placeholder=" " required>
                            <label for="nama_resep">Nama Resep</label>
                        </div>
                        <div class="input-group">
                            <select id="id_kondisi" name="id_kondisi" required>
                                <option value="" disabled selected>Pilih Kondisi</option>
                                <?php
                                $kondisi_sql = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan";
                                $kondisi_result = $conn->query($kondisi_sql);
                                while ($kondisi = $kondisi_result->fetch_assoc()) {
                                    echo "<option value='{$kondisi['id_kondisi']}'>" . htmlspecialchars($kondisi['nama_kondisi']) . "</option>";
                                }
                                ?>
                            </select>
                            <label for="id_kondisi">Kondisi Kesehatan</label>
                        </div>
                        <button type="submit" class="btn btn-tambah"><i data-feather="save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>