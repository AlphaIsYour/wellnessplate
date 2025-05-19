<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Tambah Data Gizi Resep";
$base_url = "/modules/gizi/";
$reseps_tanpa_gizi = [];
$query_resep_opt = "SELECT r.id_resep, r.nama_resep 
                    FROM resep r
                    LEFT JOIN gizi_resep gr ON r.id_resep = gr.id_resep
                    WHERE gr.id_gizi_resep IS NULL
                    ORDER BY r.nama_resep ASC";
$result_resep_opt = mysqli_query($koneksi, $query_resep_opt);
if ($result_resep_opt) {
    while ($row = mysqli_fetch_assoc($result_resep_opt)) {
        $reseps_tanpa_gizi[] = $row;
    }
    mysqli_free_result($result_resep_opt);
}

$form_input = isset($_SESSION['form_input_gizi']) ? $_SESSION['form_input_gizi'] : [];
unset($_SESSION['form_input_gizi']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah Data Gizi Resep</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <?php if (empty($reseps_tanpa_gizi) && !isset($form_input['id_resep'])) : ?>
                <div class="alert alert-warning">Semua resep sudah memiliki data gizi, atau tidak ada resep yang tersedia untuk ditambahkan data gizinya.</div>
            <?php else : ?>
                <form action="<?php echo $base_url; ?>konfirmasitambahgizi.php" method="POST">
                    <div class="form-group">
                        <label for="id_resep">Pilih Resep</label>
                        <select id="id_resep" name="id_resep" required>
                            <option value="">-- Pilih Resep --</option>
                            <?php foreach ($reseps_tanpa_gizi as $resep_opt) : ?>
                                <option value="<?php echo $resep_opt['id_resep']; ?>" <?php echo (isset($form_input['id_resep']) && $form_input['id_resep'] == $resep_opt['id_resep']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($resep_opt['nama_resep']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small>Hanya menampilkan resep yang belum memiliki data gizi.</small>
                    </div>

                    <div class="form-group">
                        <label for="kalori">Kalori (kkal)</label>
                        <input type="number" step="0.01" id="kalori" name="kalori" value="<?php echo htmlspecialchars($form_input['kalori'] ?? ''); ?>" placeholder="Contoh: 250.50">
                    </div>
                    <div class="form-group">
                        <label for="protein">Protein (gram)</label>
                        <input type="number" step="0.01" id="protein" name="protein" value="<?php echo htmlspecialchars($form_input['protein'] ?? ''); ?>" placeholder="Contoh: 20.20">
                    </div>
                    <div class="form-group">
                        <label for="karbohidrat">Karbohidrat (gram)</label>
                        <input type="number" step="0.01" id="karbohidrat" name="karbohidrat" value="<?php echo htmlspecialchars($form_input['karbohidrat'] ?? ''); ?>" placeholder="Contoh: 30.00">
                    </div>
                    <div class="form-group">
                        <label for="lemak">Lemak (gram)</label>
                        <input type="number" step="0.01" id="lemak" name="lemak" value="<?php echo htmlspecialchars($form_input['lemak'] ?? ''); ?>" placeholder="Contoh: 10.70">
                    </div>
                    
                    <button type="submit" class="btn">Simpan Data Gizi</button>
                    <a href="<?php echo $base_url; ?>gizi.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left:10px;">Batal</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once '../../templates/footer.php';
?>