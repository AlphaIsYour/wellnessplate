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

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);
    $sql = "DELETE FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: manage_users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Users - WellnessPlate</title>
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
            <li><a href="manage_resep_bahan.php" aria-label="Kelola Resep Bahan"><i data-feather="link"></i><span>Resep Bahan</span></a></li>
            <li><a href="manage_users.php" aria-label="Kelola Users" class="active"><i data-feather="users"></i><span>Users</span></a></li>
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
            <h2>Kelola Users</h2>
            <p>Atur data pengguna untuk sistem WellnessPlate.</p>
            <button class="btn btn-tambah" onclick="openPopup('add-user-popup')"><i data-feather="plus"></i> Tambah User</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID User</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Nama Lengkap</th>
                            <th>Tanggal Lahir</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="table-row">
                                <td><?php echo htmlspecialchars($row['id_user']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal_lahir'] ?: '-'); ?></td>
                                <td><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($row['jenis_kelamin'] == 'P' ? 'Perempuan' : '-'); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $row['id_user']; ?>" class="btn btn-edit" data-tooltip="Edit user"><i data-feather="edit"></i></a>
                                    <a href="?hapus=<?php echo $row['id_user']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus?')" data-tooltip="Hapus user"><i data-feather="trash-2"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7">Belum ada data user.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="add-user-popup" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('add-user-popup')">×</span>
                    <h3>Tambah User</h3>
                    <form id="add-user-form">
                        <div class="input-group">
                            <input type="text" id="id_user" name="id_user" placeholder=" " required>
                            <label for="id_user">ID User</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="username" name="username" placeholder=" " required>
                            <label for="username">Username</label>
                        </div>
                        <div class="input-group">
                            <input type="email" id="email" name="email" placeholder=" " required>
                            <label for="email">Email</label>
                        </div>
                        <div class="input-group">
                            <input type="password" id="password" name="password" placeholder=" " required>
                            <label for="password">Password</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder=" ">
                            <label for="nama_lengkap">Nama Lengkap</label>
                        </div>
                        <div class="input-group">
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" placeholder=" ">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                        </div>
                        <div class="input-group">
                            <select id="jenis_kelamin" name="jenis_kelamin">
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <label for="jenis_kelamin">Jenis Kelamin</label>
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