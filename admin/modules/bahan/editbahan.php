<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Edit Bahan";
$id_bahan_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/bahan/";

if (empty($id_bahan_to_edit)) {
    $_SESSION['error_message'] = "ID Bahan tidak valid.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$stmt_get_bahan = mysqli_prepare($koneksi, "SELECT id_bahan, nama_bahan, satuan FROM bahan WHERE id_bahan = ?");
if (!$stmt_get_bahan) {
    $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get_bahan, "s", $id_bahan_to_edit);
mysqli_stmt_execute($stmt_get_bahan);
$result_bahan_db = mysqli_stmt_get_result($stmt_get_bahan);
$bahan_data_db = mysqli_fetch_assoc($result_bahan_db);
mysqli_stmt_close($stmt_get_bahan);

if (!$bahan_data_db) {
    $_SESSION['error_message'] = "Bahan dengan ID '" . htmlspecialchars($id_bahan_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$form_input = isset($_SESSION['form_input_bahan_edit']) ? $_SESSION['form_input_bahan_edit'] : $bahan_data_db;
unset($_SESSION['form_input_bahan_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Bahan: <?php echo htmlspecialchars($bahan_data_db['nama_bahan']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditbahan.php" method="POST">
                <input type="hidden" name="id_bahan" value="<?php echo htmlspecialchars($bahan_data_db['id_bahan']); ?>">

                <div class="form-group">
                    <label for="nama_bahan">Nama Bahan</label>
                    <input type="text" id="nama_bahan" name="nama_bahan" value="<?php echo htmlspecialchars($form_input['nama_bahan'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" id="satuan" name="satuan" value="<?php echo htmlspecialchars($form_input['satuan'] ?? ''); ?>" placeholder="Contoh: gram, ml, buah, sdt" required maxlength="20">
                </div>
                
                <button type="submit" class="btn">Update Bahan</button>
                <a href="<?php echo $base_url; ?>bahan.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_bahan_db);
require_once '../../templates/footer.php';
?>