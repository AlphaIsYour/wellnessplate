<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Kelola Resep Makanan";
$base_url = "/modules/resep/";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_resep_to_delete = $_GET['id'];

    if (empty($id_resep_to_delete) || strlen($id_resep_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Resep tidak valid untuk dihapus.";
    } else {
        mysqli_autocommit($koneksi, false);
        $error_flag = false;

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

$query_resep = "SELECT 
                    r.id_resep, 
                    r.nama_resep, 
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
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Resep</th>
                            <th>Nama Resep</th>
                            <th>Kondisi Kesehatan</th>
                            <th>Dibuat Oleh (Admin)</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_resep) > 0) : ?>
                            <?php while ($resep = mysqli_fetch_assoc($result_resep)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resep['id_resep']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_resep']); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_kondisi'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($resep['nama_admin'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($resep['tanggal_dibuat']))); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $base_url; ?>detailresep.php?id=<?php echo urlencode($resep['id_resep']); ?>" class="edit" style="background-color:#17a2b8; color:white;">Detail</a>
                                        <a href="<?php echo $base_url; ?>editresep.php?id=<?php echo urlencode($resep['id_resep']); ?>" class="edit">Edit</a>
                                        <a href="<?php echo $base_url; ?>resep.php?action=delete&id=<?php echo urlencode($resep['id_resep']); ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus resep ini: <?php echo htmlspecialchars(addslashes($resep['nama_resep'])); ?>? Ini juga akan menghapus semua data bahan dan gizi terkait.');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">Belum ada data resep.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_resep);
require_once '../../templates/footer.php';
?>