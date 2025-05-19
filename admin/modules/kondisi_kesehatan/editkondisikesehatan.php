<?php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Edit Kondisi Kesehatan";
$id_kondisi_to_edit = $_GET['id'] ?? null;
$base_url = "/modules/kondisi_kesehatan/";
if (empty($id_kondisi_to_edit)) {
    $_SESSION['error_message'] = "ID Kondisi Kesehatan tidak valid.";
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

$stmt_get_kondisi = mysqli_prepare($koneksi, "SELECT id_kondisi, nama_kondisi, deskripsi FROM kondisi_kesehatan WHERE id_kondisi = ?");
if (!$stmt_get_kondisi) {
    $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get_kondisi, "s", $id_kondisi_to_edit);
mysqli_stmt_execute($stmt_get_kondisi);
$result_kondisi_db = mysqli_stmt_get_result($stmt_get_kondisi);
$kondisi_data_db = mysqli_fetch_assoc($result_kondisi_db);
mysqli_stmt_close($stmt_get_kondisi);

if (!$kondisi_data_db) {
    $_SESSION['error_message'] = "Kondisi kesehatan dengan ID '" . htmlspecialchars($id_kondisi_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'kondisikesehatan.php');
    exit;
}

$form_input = isset($_SESSION['form_input_kondisi_edit']) ? $_SESSION['form_input_kondisi_edit'] : $kondisi_data_db;
unset($_SESSION['form_input_kondisi_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Kondisi Kesehatan: <?php echo htmlspecialchars($kondisi_data_db['nama_kondisi']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditkondisikesehatan.php" method="POST">
                <input type="hidden" name="id_kondisi" value="<?php echo htmlspecialchars($kondisi_data_db['id_kondisi']); ?>">

                <div class="form-group">
                    <label for="nama_kondisi">Nama Kondisi</label>
                    <input type="text" id="nama_kondisi" name="nama_kondisi" value="<?php echo htmlspecialchars($form_input['nama_kondisi'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" required><?php echo htmlspecialchars($form_input['deskripsi'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn">Update Kondisi</button>
                <a href="<?php echo $base_url; ?>kondisikesehatan.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_kondisi_db);
require_once '../../templates/footer.php';
?>