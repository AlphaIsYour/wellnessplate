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

// Ambil informasi admin yang login
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT nama FROM admin WHERE id_admin = '$admin_id'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();
$admin_name = $admin['nama'];

// Fungsi untuk menghitung jumlah record di tabel
function get_record_count($conn, $table) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - WellnessPlate</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="header">
        <div class="logo">WellnessPlate Admin</div>
        <div class="admin-info">
            <span>Selamat datang, <?php echo $admin_name; ?></span>
            <div class="dropdown">
                <button class="dropbtn">Menu</button>
                <div class="dropdown-content">
                    <a href="edit_profile.php">Edit Profil</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <h3>Manajemen Data</h3>
        <ul>
            <li><a href="manage_kondisi.php"><i data-feather="heart"></i> Kelola Kondisi Kesehatan</a></li>
            <li><a href="manage_resep.php"><i data-feather="book"></i> Kelola Resep</a></li>
            <li><a href="manage_bahan.php"><i data-feather="shopping-bag"></i> Kelola Bahan</a></li>
            <li><a href="manage_gizi.php"><i data-feather="bar-chart-2"></i> Kelola Gizi</a></li>
            <li><a href="manage_resep_bahan.php"><i data-feather="link"></i> Kelola Resep Bahan</a></li>
            <li><a href="manage_users.php"><i data-feather="users"></i> Kelola Users</a></li>
            <li><a href="manage_admins.php"><i data-feather="user-check"></i> Kelola Admins</a></li>
        </ul>
    </div>
    <div class="content">
        <h2>Dashboard</h2>
        <p>Selamat datang di dashboard admin. Berikut adalah ringkasan data:</p>
        <div class="card-container">
            <div class="card">
                <h4>Kondisi Kesehatan</h4>
                <p><?php echo get_record_count($conn, 'kondisi_kesehatan'); ?></p>
            </div>
            <div class="card">
                <h4>Resep</h4>
                <p><?php echo get_record_count($conn, 'resep'); ?></p>
            </div>
            <div class="card">
                <h4>Bahan</h4>
                <p><?php echo get_record_count($conn, 'bahan'); ?></p>
            </div>
            <div class="card">
                <h4>Gizi</h4>
                <p><?php echo get_record_count($conn, 'gizi'); ?></p>
            </div>
            <div class="card">
                <h4>Resep Bahan</h4>
                <p><?php echo get_record_count($conn, 'resep_bahan'); ?></p>
            </div>
            <div class="card">
                <h4>Users</h4>
                <p><?php echo get_record_count($conn, 'users'); ?></p>
            </div>
            <div class="card">
                <h4>Admins</h4>
                <p><?php echo get_record_count($conn, 'admin'); ?></p>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>
</html>

<?php $conn->close(); ?>