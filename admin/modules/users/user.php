<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    $base_url = "/"; 

    header("Location: " . $base_url . "/index.php?error=Silakan login terlebih dahulu.");
    exit;
}

if (!isset($base_url)) {
    $base_url = "/"; 
}

$page_title = isset($page_title) ? $page_title : 'Admin WellnessPlate';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body class="dashboard-body">
    <header class="page-header">
        <div class="logo-area">
            <h1><a href="<?php echo $base_url; ?>/dashboard.php" style="color: inherit; text-decoration: none;">WellnessPlate Admin</a></h1>
        </div>
        <div class="admin-info">
            <span class="welcome-admin" style="margin-right: 10px;">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</span>
            <a href="/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="main-wrapper">
        <?php
        include_once  '../../templates/sidebar.php';
        ?>
        <main class="content-area">
            <!-- Konten utama halaman akan ada di sini -->
<?php
// modules/users/user.php
require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Kelola Pengguna";

// --- LOGIKA DELETE (pindahkan ke atas sebelum output HTML) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_user_to_delete = $_GET['id'];

    if (empty($id_user_to_delete) || strlen($id_user_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Pengguna tidak valid untuk dihapus.";
    } else {
        $stmt_delete_user = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user = ?");
        if ($stmt_delete_user) {
            mysqli_stmt_bind_param($stmt_delete_user, "s", $id_user_to_delete); // "s" untuk VARCHAR
            if (mysqli_stmt_execute($stmt_delete_user)) {
                if (mysqli_stmt_affected_rows($stmt_delete_user) > 0) {
                    $_SESSION['success_message'] = "Pengguna berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Pengguna tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal menghapus pengguna: " . mysqli_stmt_error($stmt_delete_user);
            }
            mysqli_stmt_close($stmt_delete_user);
        } else {
            $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus: " . mysqli_error($koneksi);
        }
    }
    // Redirect kembali ke user.php tanpa parameter action dan id di URL
    header('Location: ' . MODULE_USERS_URL . 'user.php');
    exit;
}
// --- END LOGIKA DELETE ---


// Fetch data pengguna
$query = "SELECT id_user, username, email, nama_lengkap, tanggal_lahir, jenis_kelamin FROM users ORDER BY nama_lengkap ASC";
$result_users = mysqli_query($koneksi, $query);

if (!$result_users) {
    die("Query gagal mengambil data users: " . mysqli_error($koneksi));
}
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Daftar Pengguna Aplikasi</h2>
            <a href="<?php echo MODULE_USERS_URL; ?>tambahuser.php" class="btna btn-sm">Tambah Pengguna Baru</a>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
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
                        <?php if (mysqli_num_rows($result_users) > 0) : ?>
                            <?php $counter = 1; ?>
                            <?php while ($user = mysqli_fetch_assoc($result_users)) : ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo htmlspecialchars($user['id_user']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y', strtotime($user['tanggal_lahir']))); ?></td>
                                    <td><?php echo htmlspecialchars(getJenisKelaminText($user['jenis_kelamin'])); // Panggil fungsi dari koneksi.php ?></td>
                                    <td class="actions">
                                        <a href="<?php echo MODULE_USERS_URL; ?>edituser.php?id=<?php echo urlencode($user['id_user']); ?>" class="edit">Edit</a>
                                        <a href="<?php echo MODULE_USERS_URL; ?>user.php?action=delete&id=<?php echo urlencode($user['id_user']); ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini: <?php echo htmlspecialchars(addslashes($user['username'])); ?>?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" style="text-align:center;">Belum ada data pengguna.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_users);
if (!isset($base_url)) {
    $base_url = "/wellnessplate";
}
?>
        </main> 
    </div> 
<div  style="background-color:rgb(98, 98, 98);">
    <p style="margin-left: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
</div>
    </footer>
    <script src="../../script.js"></script>
</body>
</html>