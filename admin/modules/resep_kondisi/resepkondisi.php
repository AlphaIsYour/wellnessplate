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
        include_once '../../templates/sidebar.php';
        ?>
        <main class="content-area">
            <?php
            require_once __DIR__ . '/../../../config/koneksi.php';

            $page_title = "Kelola Resep - Kondisi Kesehatan";
            $base_url = "/admin/modules/resep_kondisi/";

            // Handle delete action
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
                $id_to_delete = $_GET['id'];
                
                $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM resep_kondisi WHERE id_resep_kondisi = ?");
                if ($stmt_delete) {
                    mysqli_stmt_bind_param($stmt_delete, "i", $id_to_delete);
                    if (mysqli_stmt_execute($stmt_delete)) {
                        $_SESSION['success_message'] = "Data berhasil dihapus.";
                    } else {
                        $_SESSION['error_message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
                    }
                    mysqli_stmt_close($stmt_delete);
                }
                header('Location: ' . $base_url . 'resepkondisi.php');
                exit;
            }

            // Query untuk mengambil data resep_kondisi dengan join ke tabel terkait
            $query = "SELECT rk.id_resep_kondisi, r.nama_resep, k.nama_kondisi, rk.created_at
                     FROM resep_kondisi rk
                     JOIN resep r ON rk.id_resep = r.id_resep
                     JOIN kondisi_kesehatan k ON rk.id_kondisi = k.id_kondisi
                     ORDER BY rk.created_at DESC";
            
            $result = mysqli_query($koneksi, $query);
            ?>

            <div class="container mx-auto py-8">
                <div class="card">
                    <div class="card-header">
                        <h2>Daftar Resep - Kondisi Kesehatan</h2>
                        <a href="<?php echo $base_url; ?>tambahresepkondisi.php" class="btna btn-sm">Tambah Data Baru</a>
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
                                        <th>ID</th>
                                        <th>Nama Resep</th>
                                        <th>Kondisi Kesehatan</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id_resep_kondisi']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_resep']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_kondisi']); ?></td>
                                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                                <td class="actions">
                                                    <a href="<?php echo $base_url; ?>editresepkondisi.php?id=<?php echo urlencode($row['id_resep_kondisi']); ?>" class="edit">Edit</a>
                                                    <a href="<?php echo $base_url; ?>resepkondisi.php?action=delete&id=<?php echo urlencode($row['id_resep_kondisi']); ?>" 
                                                       class="delete" 
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align:center;">Belum ada data resep kondisi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
    <footer>
        <div style="background-color:rgb(98, 98, 98);">
            <p style="margin-left: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
        </div>
    </footer>
    <script src="../../script.js"></script>
</body>
</html> 