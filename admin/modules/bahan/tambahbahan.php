<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Tambah Bahan Baru";
$base_url = "/modules/bahan/";
$form_input = isset($_SESSION['form_input_bahan']) ? $_SESSION['form_input_bahan'] : [];
unset($_SESSION['form_input_bahan']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah Bahan Baru</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasitambahbahan.php" method="POST">
                <div class="form-group">
                    <label for="nama_bahan">Nama Bahan</label>
                    <input type="text" id="nama_bahan" name="nama_bahan" value="<?php echo htmlspecialchars($form_input['nama_bahan'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" id="satuan" name="satuan" value="<?php echo htmlspecialchars($form_input['satuan'] ?? ''); ?>" placeholder="Contoh: gram, ml, buah, sdt" required maxlength="20">
                </div>
                
                <button type="submit" class="btn">Simpan Bahan</button>
                <a href="<?php echo $base_url; ?>bahan.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left:10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../templates/footer.php';
?>