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
require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Kelola Kondisi Kesehatan";
$base_url = "/admin/modules/kondisi_kesehatan/";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_kondisi_to_delete = $_GET['id'];

    if (empty($id_kondisi_to_delete) || strlen($id_kondisi_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Kondisi Kesehatan tidak valid untuk dihapus.";
    } else {
        $stmt_delete_kondisi = mysqli_prepare($koneksi, "DELETE FROM kondisi_kesehatan WHERE id_kondisi = ?");
        if ($stmt_delete_kondisi) {
            mysqli_stmt_bind_param($stmt_delete_kondisi, "s", $id_kondisi_to_delete);
            if (mysqli_stmt_execute($stmt_delete_kondisi)) {
                if (mysqli_stmt_affected_rows($stmt_delete_kondisi) > 0) {
                    $_SESSION['success_message'] = "Kondisi kesehatan berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Kondisi kesehatan tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal menghapus kondisi kesehatan: " . mysqli_stmt_error($stmt_delete_kondisi);
            }
            mysqli_stmt_close($stmt_delete_kondisi);
        } else {
            $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus kondisi: " . mysqli_error($koneksi);
        }
    }
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

$query_kondisi = "SELECT id_kondisi, nama_kondisi, deskripsi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);

if (!$result_kondisi) {
    die("Query gagal mengambil data kondisi kesehatan: " . mysqli_error($koneksi));
}
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Daftar Kondisi Kesehatan</h2>
            <a href="<?php echo $base_url; ?>tambahkondisikesehatan.php" class="btna btn-sm">Tambah Kondisi Baru</a>
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
                            <th>ID Kondisi</th>
                            <th>Nama Kondisi</th>
                            <th>Deskripsi (Singkat)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_kondisi) > 0) : ?>
                            <?php while ($kondisi = mysqli_fetch_assoc($result_kondisi)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($kondisi['id_kondisi']); ?></td>
                                    <td><?php echo htmlspecialchars($kondisi['nama_kondisi']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($kondisi['deskripsi'], 0, 100))); echo strlen($kondisi['deskripsi']) > 100 ? '...' : ''; ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $base_url; ?>editkondisikesehatan.php?id=<?php echo urlencode($kondisi['id_kondisi']); ?>" class="edit">Edit</a>
                                        <a href="<?php echo $base_url; ?>kondisikesehatan.php?action=delete&id=<?php echo urlencode($kondisi['id_kondisi']); ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus kondisi ini: <?php echo htmlspecialchars(addslashes($kondisi['nama_kondisi'])); ?>?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada data kondisi kesehatan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_kondisi);
if (!isset($base_url)) {
    $base_url = "/wellnessplate";
}
?>
        </main> 
    </div> 
    <footer>
        <div style="background-color:rgb(98, 98, 98);">
            <p style="text-align: right; margin-right: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
        </div>
    </footer>
    <script src="../../script.js"></script>
</body>
</html>