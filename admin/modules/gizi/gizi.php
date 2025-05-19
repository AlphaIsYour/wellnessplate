<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Kelola Data Gizi Resep";
$base_url = "/admin/modules/gizi/";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_gizi_resep_to_delete = $_GET['id'];

    if (empty($id_gizi_resep_to_delete) || strlen($id_gizi_resep_to_delete) > 10) {
        $_SESSION['error_message'] = "ID Gizi tidak valid untuk dihapus.";
    } else {
        $stmt_delete_gizi = mysqli_prepare($koneksi, "DELETE FROM gizi_resep WHERE id_gizi_resep = ?");
        if ($stmt_delete_gizi) {
            mysqli_stmt_bind_param($stmt_delete_gizi, "s", $id_gizi_resep_to_delete);
            if (mysqli_stmt_execute($stmt_delete_gizi)) {
                if (mysqli_stmt_affected_rows($stmt_delete_gizi) > 0) {
                    $_SESSION['success_message'] = "Data gizi berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Data gizi tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data gizi: " . mysqli_stmt_error($stmt_delete_gizi);
            }
            mysqli_stmt_close($stmt_delete_gizi);
        } else {
            $_SESSION['error_message'] = "Gagal mempersiapkan statement hapus gizi: " . mysqli_error($koneksi);
        }
    }
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$query_all_gizi = "SELECT 
                        gr.id_gizi_resep, 
                        r.nama_resep, 
                        gr.kalori, 
                        gr.protein, 
                        gr.karbohidrat, 
                        gr.lemak,
                        r.id_resep 
                    FROM 
                        gizi_resep gr
                    JOIN 
                        resep r ON gr.id_resep = r.id_resep
                    ORDER BY 
                        r.nama_resep ASC, gr.id_gizi_resep ASC";
$result_all_gizi = mysqli_query($koneksi, $query_all_gizi);

if (!$result_all_gizi) {
    die("Query gagal mengambil data gizi: " . mysqli_error($koneksi));
}
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Daftar Data Gizi per Resep</h2>
            <a href="<?php echo $base_url; ?>tambahgizi.php" class="btna btn-sm">Tambah Data Gizi Baru</a>
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
                            <th>ID Gizi</th>
                            <th>Nama Resep</th>
                            <th>Kalori (kkal)</th>
                            <th>Protein (g)</th>
                            <th>Karbohidrat (g)</th>
                            <th>Lemak (g)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_all_gizi) > 0) : ?>
                            <?php while ($gizi_item = mysqli_fetch_assoc($result_all_gizi)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($gizi_item['id_gizi_resep']); ?></td>
                                    <td><a href="/modules/resep/detailresep.php?id=<?php echo urlencode($gizi_item['id_resep']); ?>"><?php echo htmlspecialchars($gizi_item['nama_resep']); ?></a></td>
                                    <td><?php echo htmlspecialchars(is_null($gizi_item['kalori']) ? '-' : number_format($gizi_item['kalori'], 1)); ?></td>
                                    <td><?php echo htmlspecialchars(is_null($gizi_item['protein']) ? '-' : number_format($gizi_item['protein'], 1)); ?></td>
                                    <td><?php echo htmlspecialchars(is_null($gizi_item['karbohidrat']) ? '-' : number_format($gizi_item['karbohidrat'], 1)); ?></td>
                                    <td><?php echo htmlspecialchars(is_null($gizi_item['lemak']) ? '-' : number_format($gizi_item['lemak'], 1)); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $base_url; ?>editgizi.php?id=<?php echo urlencode($gizi_item['id_gizi_resep']); ?>" class="edit">Edit</a>
                                        <a href="<?php echo $base_url; ?>gizi.php?action=delete&id=<?php echo urlencode($gizi_item['id_gizi_resep']); ?>" class="delete delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus data gizi untuk resep \'<?php echo htmlspecialchars(addslashes($gizi_item['nama_resep'])); ?>\' ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Belum ada data gizi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_all_gizi);
require_once '../../templates/footer.php';
?>