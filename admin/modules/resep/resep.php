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

$page_title = "Kelola Resep Makanan";
$base_url = "/admin/modules/resep/";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_resep_to_delete = $_GET['id'];

    if (empty($id_resep_to_delete) || strlen($id_resep_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Resep tidak valid untuk dihapus.";
    } else {
        mysqli_autocommit($koneksi, false);
        $error_flag = false;

        // Get image filename before deleting
        $stmt_get_image = mysqli_prepare($koneksi, "SELECT image FROM resep WHERE id_resep = ?");
        $image_filename = null;
        if ($stmt_get_image) {
            mysqli_stmt_bind_param($stmt_get_image, "s", $id_resep_to_delete);
            mysqli_stmt_execute($stmt_get_image);
            $result_image = mysqli_stmt_get_result($stmt_get_image);
            if ($row = mysqli_fetch_assoc($result_image)) {
                $image_filename = $row['image'];
            }
            mysqli_stmt_close($stmt_get_image);
        }

        $stmt_delete_gizi = mysqli_prepare($koneksi, "DELETE FROM gizi_resep WHERE id_resep = ?");
        if ($stmt_delete_gizi) {
            mysqli_stmt_bind_param($stmt_delete_gizi, "s", $id_resep_to_delete);
            if (!mysqli_stmt_execute($stmt_delete_gizi)) {
                $error_flag = true;
                $_SESSION['error_message'] = "Gagal menghapus data gizi terkait: " . mysqli_stmt_error($stmt_delete_gizi);
            }
            mysqli_stmt_close($stmt_delete_gizi);
        } else {
            $error_flag = true;
            $_SESSION['error_message'] = "Gagal mempersiapkan hapus gizi: " . mysqli_error($koneksi);
        }

        // disini query untuk menghapus bahan 
        if (!$error_flag) {
            $stmt_delete_bahan = mysqli_prepare($koneksi, "DELETE FROM resep_bahan WHERE id_resep = ?");
            if ($stmt_delete_bahan) {
                mysqli_stmt_bind_param($stmt_delete_bahan, "s", $id_resep_to_delete);
                if (!mysqli_stmt_execute($stmt_delete_bahan)) {
                    $error_flag = true;
                    $_SESSION['error_message'] = "Gagal menghapus data bahan terkait: " . mysqli_stmt_error($stmt_delete_bahan);
                }
                mysqli_stmt_close($stmt_delete_bahan);
            } else {
                $error_flag = true;
                $_SESSION['error_message'] = "Gagal mempersiapkan hapus bahan resep: " . mysqli_error($koneksi);
            }
        }

        if (!$error_flag) {
            $stmt_delete_resep = mysqli_prepare($koneksi, "DELETE FROM resep WHERE id_resep = ?");
            if ($stmt_delete_resep) {
                mysqli_stmt_bind_param($stmt_delete_resep, "s", $id_resep_to_delete);
                if (mysqli_stmt_execute($stmt_delete_resep)) {
                    if (mysqli_stmt_affected_rows($stmt_delete_resep) > 0) {
                        // Delete image file if exists
                        if ($image_filename && file_exists(__DIR__ . '/../../../assets/images/menu/' . $image_filename)) {
                            unlink(__DIR__ . '/../../../assets/images/menu/' . $image_filename);
                        }
                        $_SESSION['success_message'] = "Resep dan data terkait berhasil dihapus.";
                    } else {
                        $error_flag = true; 
                        $_SESSION['error_message'] = "Resep tidak ditemukan atau sudah dihapus.";
                    }
                } else {
                    $error_flag = true;
                    $_SESSION['error_message'] = "Gagal menghapus resep utama: " . mysqli_stmt_error($stmt_delete_resep);
                }
                mysqli_stmt_close($stmt_delete_resep);
            } else {
                $error_flag = true;
                $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus resep: " . mysqli_error($koneksi);
            }
        }

        if ($error_flag) {
            mysqli_rollback($koneksi);
        } else {
            mysqli_commit($koneksi);
        }
        mysqli_autocommit($koneksi, true);
    }
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

// query untuk mengambil data resep
$query_resep = "SELECT 
                    r.id_resep, 
                    r.nama_resep,
                    r.deskripsi,
                    r.image, 
                    r.tanggal_dibuat, 
                    u.nama_lengkap AS nama_admin, 
                    k.nama_kondisi 
                FROM 
                    resep r
                LEFT JOIN 
                    users u ON r.id_admin = u.id_user
                LEFT JOIN 
                    kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi
                ORDER BY 
                    r.tanggal_dibuat DESC, r.nama_resep ASC";
$result_resep = mysqli_query($koneksi, $query_resep);

if (!$result_resep) {
    die("Query gagal mengambil data resep: " . mysqli_error($koneksi));
}
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Daftar Resep Makanan</h2>
            <a href="<?php echo $base_url; ?>tambahresep.php" class="btna btn-sm">Tambah Resep Baru</a>
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
            <div class="recipe-grid">
                <?php if (mysqli_num_rows($result_resep) > 0) : ?>
                    <?php while ($resep = mysqli_fetch_assoc($result_resep)) : ?>
                        <div class="recipe-card">
                            <div class="recipe-image">
                                <?php if (!empty($resep['image'])): ?>
                                    <img src="<?php echo BASE_URL . '/assets/images/menu/' . htmlspecialchars($resep['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($resep['nama_resep']); ?>">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </div>
                            <div class="recipe-content">
                                <h3><?php echo htmlspecialchars($resep['nama_resep']); ?></h3>
                                <p class="recipe-description"><?php echo htmlspecialchars(substr($resep['deskripsi'], 0, 100)) . (strlen($resep['deskripsi']) > 100 ? '...' : ''); ?></p>
                                <div class="recipe-meta">
                                    <span class="condition"><?php echo htmlspecialchars($resep['nama_kondisi'] ?? 'N/A'); ?></span>
                                    <span class="date"><?php echo htmlspecialchars(date('d M Y', strtotime($resep['tanggal_dibuat']))); ?></span>
                                </div>
                                <div class="recipe-actions">
                                    <a href="<?php echo $base_url; ?>detailresep.php?id=<?php echo urlencode($resep['id_resep']); ?>" class="btn-detail">Detail</a>
                                    <a href="<?php echo $base_url; ?>editresep.php?id=<?php echo urlencode($resep['id_resep']); ?>" class="btn-edit">Edit</a>
                                    <a href="<?php echo $base_url; ?>resep.php?action=delete&id=<?php echo urlencode($resep['id_resep']); ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus resep ini: <?php echo htmlspecialchars(addslashes($resep['nama_resep'])); ?>? Ini juga akan menghapus semua data bahan dan gizi terkait.');">
                                        Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="no-data">Belum ada data resep.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.recipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.recipe-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s;
}

.recipe-card:hover {
    transform: translateY(-5px);
}

.recipe-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.recipe-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
    color: #666;
}

.recipe-content {
    padding: 15px;
}

.recipe-content h3 {
    margin: 0 0 10px 0;
    font-size: 1.2em;
    color: #333;
}

.recipe-description {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 10px;
    line-height: 1.4;
}

.recipe-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 0.85em;
}

.condition {
    color: #2196F3;
    font-weight: 500;
}

.date {
    color: #666;
}

.recipe-actions {
    display: flex;
    gap: 10px;
}

.recipe-actions a {
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.9em;
    flex: 1;
    text-align: center;
    transition: background-color 0.2s;
}

.btn-detail {
    background-color: #17a2b8;
    color: white;
}

.btn-edit {
    background-color: #ffc107;
    color: #000;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-detail:hover { background-color: #138496; }
.btn-edit:hover { background-color: #e0a800; }
.btn-delete:hover { background-color: #c82333; }

.no-data {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    background: #f9f9f9;
    border-radius: 8px;
    color: #666;
}
</style>

<?php
mysqli_free_result($result_resep);
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