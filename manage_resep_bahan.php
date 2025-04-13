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

$sql = "SELECT resep_bahan.id_resep_bahan, resep_bahan.id_resep, resep_bahan.id_bahan, resep_bahan.jumlah, 
        resep.nama_resep, bahan.nama_bahan 
        FROM resep_bahan 
        LEFT JOIN resep ON resep_bahan.id_resep = resep.id_resep 
        LEFT JOIN bahan ON resep_bahan.id_bahan = bahan.id_bahan";
$result = $conn->query($sql);

if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);
    $sql = "DELETE FROM resep_bahan WHERE id_resep_bahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: manage_resep_bahan.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Resep Bahan - WellnessPlate</title>
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
            <li><a href="manage_resep.php" aria-label="Kelola Resep"><i data-feather="book"></i><span>Resep</span></a></li>
            <li><a href="manage_bahan.php" aria-label="Kelola Bahan"><i data-feather="shopping-bag"></i><span>Bahan</span></a></li>
            <li><a href="manage_gizi.php" aria-label="Kelola Gizi"><i data-feather="bar-chart-2"></i><span>Gizi</span></a></li>
            <li><a href="manage_resep_bahan.php" aria-label="Kelola Resep Bahan" class="active"><i data-feather="link"></i><span>Resep Bahan</span></a></li>
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
            <h2>Kelola Resep Bahan</h2>
            <p>Atur hubungan antara resep dan bahan untuk sistem WellnessPlate.</p>
            <button class="btn btn-tambah" onclick="openPopup('add-resep-bahan-popup')"><i data-feather="plus"></i> Tambah Resep Bahan</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Resep Bahan</th>
                            <th>Nama Resep</th>
                            <th>Nama Bahan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="table-row">
                                <td><?php echo htmlspecialchars($row['id_resep_bahan']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_resep'] ?: 'Tidak ada resep'); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_bahan'] ?: 'Tidak ada bahan'); ?></td>
                                <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                                <td>
                                    <a href="edit_resep_bahan.php?id=<?php echo $row['id_resep_bahan']; ?>" class="btn btn-edit" data-tooltip="Edit resep bahan"><i data-feather="edit"></i></a>
                                    <a href="?hapus=<?php echo $row['id_resep_bahan']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus?')" data-tooltip="Hapus resep bahan"><i data-feather="trash-2"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5">Belum ada data resep bahan.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="add-resep-bahan-popup" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('add-resep-bahan-popup')">×</span>
                    <h3>Tambah Resep Bahan</h3>
                    <form id="add-resep-bahan-form">
                        <div class="input-group">
                            <input type="text" id="id_resep_bahan" name="id_resep_bahan" placeholder=" " required>
                            <label for="id_resep_bahan">ID Resep Bahan</label>
                        </div>
                        <div class="input-group">
                            <select id="id_resep" name="id_resep" required>
                                <option value="" disabled selected>Pilih Resep</option>
                                <?php
                                $resep_sql = "SELECT id_resep, nama_resep FROM resep";
                                $resep_result = $conn->query($resep_sql);
                                while ($resep = $resep_result->fetch_assoc()) {
                                    echo "<option value='{$resep['id_resep']}'>" . htmlspecialchars($resep['nama_resep']) . "</option>";
                                }
                                ?>
                            </select>
                            <label for="id_resep">Nama Resep</label>
                        </div>
                        <div class="input-group">
                            <select id="id_bahan" name="id_bahan" required>
                                <option value="" disabled selected>Pilih Bahan</option>
                                <?php
                                $bahan_sql = "SELECT id_bahan, nama_bahan FROM bahan";
                                $bahan_result = $conn->query($bahan_sql);
                                while ($bahan = $bahan_result->fetch_assoc()) {
                                    echo "<option value='{$bahan['id_bahan']}'>" . htmlspecialchars($bahan['nama_bahan']) . "</option>";
                                }
                                ?>
                            </select>
                            <label for="id_bahan">Nama Bahan</label>
                        </div>
                        <div class="input-group">
                            <input type="number" id="jumlah" name="jumlah" placeholder=" " required step="0.01">
                            <label for="jumlah">Jumlah</label>
                        </div>
                        <button type="submit" class="btn btn-tambah"><i data-feather="save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
    <script>
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