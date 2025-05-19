<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Edit Data Gizi Resep";
$id_gizi_resep_to_edit = $_GET['id'] ?? null;
$base_url = "/modules/gizi/";
if (empty($id_gizi_resep_to_edit) || strlen($id_gizi_resep_to_edit) > 10) {
    $_SESSION['error_message'] = "ID Gizi tidak valid.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$stmt_get_gizi = mysqli_prepare($koneksi, "SELECT gr.id_gizi_resep, gr.id_resep, r.nama_resep, gr.kalori, gr.protein, gr.karbohidrat, gr.lemak 
                                          FROM gizi_resep gr 
                                          JOIN resep r ON gr.id_resep = r.id_resep 
                                          WHERE gr.id_gizi_resep = ?");
if (!$stmt_get_gizi) {
    die("Gagal mempersiapkan query edit gizi: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt_get_gizi, "s", $id_gizi_resep_to_edit);
mysqli_stmt_execute($stmt_get_gizi);
$result_gizi_db = mysqli_stmt_get_result($stmt_get_gizi);
$gizi_data_db = mysqli_fetch_assoc($result_gizi_db);
mysqli_stmt_close($stmt_get_gizi);

if (!$gizi_data_db) {
    $_SESSION['error_message'] = "Data gizi dengan ID '" . htmlspecialchars($id_gizi_resep_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'gizi.php');
    exit;
}

$form_input = isset($_SESSION['form_input_gizi_edit']) ? $_SESSION['form_input_gizi_edit'] : $gizi_data_db;
unset($_SESSION['form_input_gizi_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Data Gizi untuk Resep: <?php echo htmlspecialchars($gizi_data_db['nama_resep']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditgizi.php" method="POST">
                <input type="hidden" name="id_gizi_resep" value="<?php echo htmlspecialchars($gizi_data_db['id_gizi_resep']); ?>">
                
                <div class="form-group">
                    <label>Resep</label>
                    <input type="text" value="<?php echo htmlspecialchars($gizi_data_db['nama_resep']); ?>" readonly class="form-control-plaintext" style="background-color: #e9ecef; padding: .375rem .75rem; border-radius: .25rem; border: 1px solid #ced4da;">
                </div>

                <div class="form-group">
                    <label for="kalori">Kalori (kkal)</label>
                    <input type="number" step="0.01" id="kalori" name="kalori" value="<?php echo htmlspecialchars($form_input['kalori'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="protein">Protein (gram)</label>
                    <input type="number" step="0.01" id="protein" name="protein" value="<?php echo htmlspecialchars($form_input['protein'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="karbohidrat">Karbohidrat (gram)</label>
                    <input type="number" step="0.01" id="karbohidrat" name="karbohidrat" value="<?php echo htmlspecialchars($form_input['karbohidrat'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="lemak">Lemak (gram)</label>
                    <input type="number" step="0.01" id="lemak" name="lemak" value="<?php echo htmlspecialchars($form_input['lemak'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn">Update Data Gizi</button>
                <a href="<?php echo $base_url; ?>gizi.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
if(isset($result_gizi_db)) mysqli_free_result($result_gizi_db);
require_once '../../templates/footer.php';
?>