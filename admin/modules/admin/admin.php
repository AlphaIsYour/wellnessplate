<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    $base_url = "/"; 

    header("Location: " . $base_url . "/index.php?error=Silakan login terlebih dahulu coy.");
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
// admin/modules/admin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/koneksi.php';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($_GET['id'] == $_SESSION['admin_id']) {
        $_SESSION['error_message'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    } else {
        $id_admin_to_delete = mysqli_real_escape_string($koneksi, $_GET['id']);

        $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM admin WHERE id_admin = ?");
        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "s", $id_admin_to_delete); 
            if (mysqli_stmt_execute($stmt_delete)) {
                if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                    $_SESSION['success_message'] = "Admin berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Admin tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal menghapus admin: " . mysqli_stmt_error($stmt_delete);
            }
            mysqli_stmt_close($stmt_delete);
        } else {
            $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus: " . mysqli_error($koneksi);
        }
    }

    if (!isset($base_url)) { $base_url = "/admin/modules/admin/"; }
    header('Location: '. $base_url .'admin.php');
    exit;
}

$query = "SELECT id_admin, username, nama, email FROM admin ORDER BY nama ASC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$page_title = "Kelola Admin";
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold">Daftar Admin</h2>
            <a href="tambahadmin.php" class="btna btn-sm">Tambah Admin Baru</a>
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
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php $counter = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="actions">
                                        <a href="editadmin.php?id=<?php echo $row['id_admin']; ?>" class="edit">Edit</a>
                                        <?php // Tombol hapus tidak boleh ditampilkan untuk admin yang sedang login ?>
                                        <?php if ($row['id_admin'] != $_SESSION['admin_id']) : ?>
                                            <a href="admin.php?action=delete&id=<?php echo $row['id_admin']; ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini: <?php echo htmlspecialchars($row['username']); ?>?');">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada data admin.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    </div> 

    <?php
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
