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
<?php
// admin/modules/bahan/bahan.php
require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Kelola Bahan Makanan";
$base_url = "/admin/modules/bahan/";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_bahan_to_delete = $_GET['id'];

    if (empty($id_bahan_to_delete) || strlen($id_bahan_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Bahan tidak valid untuk dihapus.";
    } else {
        $stmt_delete_bahan = mysqli_prepare($koneksi, "DELETE FROM bahan WHERE id_bahan = ?");
        if ($stmt_delete_bahan) {
            mysqli_stmt_bind_param($stmt_delete_bahan, "s", $id_bahan_to_delete);
            if (mysqli_stmt_execute($stmt_delete_bahan)) {
                if (mysqli_stmt_affected_rows($stmt_delete_bahan) > 0) {
                    $_SESSION['success_message'] = "Bahan berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Bahan tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal menghapus bahan: " . mysqli_stmt_error($stmt_delete_bahan);
            }
            mysqli_stmt_close($stmt_delete_bahan);
        } else {
            $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus bahan: " . mysqli_error($koneksi);
        }
    }
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$query_bahan = "SELECT id_bahan, nama_bahan, satuan FROM bahan ORDER BY nama_bahan ASC";
$result_bahan = mysqli_query($koneksi, $query_bahan);

if (!$result_bahan) {
    die("Query gagal mengambil data bahan: " . mysqli_error($koneksi));
}
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Daftar Bahan Makanan</h2>
            <a href="<?php echo $base_url; ?>tambahbahan.php" class="btna btn-sm">Tambah Bahan Baru</a>
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
                            <th>ID Bahan</th>
                            <th>Nama Bahan</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_bahan) > 0) : ?>
                            <?php while ($bahan = mysqli_fetch_assoc($result_bahan)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bahan['id_bahan']); ?></td>
                                    <td><?php echo htmlspecialchars($bahan['nama_bahan']); ?></td>
                                    <td><?php echo htmlspecialchars($bahan['satuan']); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $base_url; ?>editbahan.php?id=<?php echo urlencode($bahan['id_bahan']); ?>" class="edit">Edit</a>
                                        <a href="<?php echo $base_url; ?>bahan.php?action=delete&id=<?php echo urlencode($bahan['id_bahan']); ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini: <?php echo htmlspecialchars(addslashes($bahan['nama_bahan'])); ?>?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada data bahan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_bahan);
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