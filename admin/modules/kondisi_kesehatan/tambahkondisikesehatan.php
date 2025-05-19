<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Tambah Kondisi Kesehatan Baru";
$base_url = "/modules/kondisi_kesehatan/";
$form_input = isset($_SESSION['form_input_kondisi']) ? $_SESSION['form_input_kondisi'] : [];
unset($_SESSION['form_input_kondisi']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah Kondisi Kesehatan Baru</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasitambahkondisikesehatan.php" method="POST">
                <div class="form-group">
                    <label for="nama_kondisi">Nama Kondisi</label>
                    <input type="text" id="nama_kondisi" name="nama_kondisi" value="<?php echo htmlspecialchars($form_input['nama_kondisi'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" required><?php echo htmlspecialchars($form_input['deskripsi'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn">Simpan Kondisi</button>
                <a href="<?php echo $base_url; ?>kondisikesehatan.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left:10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../templates/footer.php';
?>