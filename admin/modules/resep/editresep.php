<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once '../../templates/header.php';

$page_title = "Edit Resep";
$id_resep_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/resep/";
if (empty($id_resep_to_edit)) {
    $_SESSION['error_message'] = "ID Resep tidak valid.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$stmt_get_resep = mysqli_prepare($koneksi, "SELECT id_resep, id_admin, id_kondisi, nama_resep, cara_buat FROM resep WHERE id_resep = ?");
if (!$stmt_get_resep) die("Prepare resep gagal: " . mysqli_error($koneksi));
mysqli_stmt_bind_param($stmt_get_resep, "s", $id_resep_to_edit);
mysqli_stmt_execute($stmt_get_resep);
$result_resep_db = mysqli_stmt_get_result($stmt_get_resep);
$resep_data_db = mysqli_fetch_assoc($result_resep_db);
mysqli_stmt_close($stmt_get_resep);

if (!$resep_data_db) {
    $_SESSION['error_message'] = "Resep dengan ID '" . htmlspecialchars($id_resep_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$resep_bahans_db = [];
$stmt_get_bahan_resep = mysqli_prepare($koneksi, "SELECT rb.id_bahan, rb.jumlah, b.nama_bahan, b.satuan FROM resep_bahan rb JOIN bahan b ON rb.id_bahan = b.id_bahan WHERE rb.id_resep = ?");
if (!$stmt_get_bahan_resep) die("Prepare bahan resep gagal: " . mysqli_error($koneksi));
mysqli_stmt_bind_param($stmt_get_bahan_resep, "s", $id_resep_to_edit);
mysqli_stmt_execute($stmt_get_bahan_resep);
$result_bahan_resep_db = mysqli_stmt_get_result($stmt_get_bahan_resep);
while ($row = mysqli_fetch_assoc($result_bahan_resep_db)) {
    $resep_bahans_db[] = $row;
}
mysqli_stmt_close($stmt_get_bahan_resep);

$gizi_data_db = [];
$stmt_get_gizi = mysqli_prepare($koneksi, "SELECT kalori, protein, karbohidrat, lemak FROM gizi_resep WHERE id_resep = ?");
if (!$stmt_get_gizi) die("Prepare gizi gagal: " . mysqli_error($koneksi));
mysqli_stmt_bind_param($stmt_get_gizi, "s", $id_resep_to_edit);
mysqli_stmt_execute($stmt_get_gizi);
$result_gizi_db = mysqli_stmt_get_result($stmt_get_gizi);
$gizi_data_db = mysqli_fetch_assoc($result_gizi_db); 
mysqli_stmt_close($stmt_get_gizi);
if (!$gizi_data_db) $gizi_data_db = [];


$users_admin = [];
$query_users_admin = "SELECT id_user, nama_lengkap FROM users ORDER BY nama_lengkap ASC";
$result_users_admin = mysqli_query($koneksi, $query_users_admin);
if ($result_users_admin) while ($row = mysqli_fetch_assoc($result_users_admin)) $users_admin[] = $row;

$kondisi_kesehatans = [];
$query_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);
if ($result_kondisi) while ($row = mysqli_fetch_assoc($result_kondisi)) $kondisi_kesehatans[] = $row;

$bahans_all = [];
$query_bahan_all = "SELECT id_bahan, nama_bahan, satuan FROM bahan ORDER BY nama_bahan ASC";
$result_bahan_all_q = mysqli_query($koneksi, $query_bahan_all);
if ($result_bahan_all_q) while ($row = mysqli_fetch_assoc($result_bahan_all_q)) $bahans_all[] = $row;


$form_input = isset($_SESSION['form_input_resep_edit']) ? $_SESSION['form_input_resep_edit'] : $resep_data_db;
$resep_bahans_input = isset($_SESSION['form_input_resep_edit']['resep_bahan']) && is_array($_SESSION['form_input_resep_edit']['resep_bahan']) 
                        ? $_SESSION['form_input_resep_edit']['resep_bahan'] 
                        : (empty($resep_bahans_db) ? [['id_bahan' => '', 'jumlah' => '']] : $resep_bahans_db);
$gizi_input = isset($_SESSION['form_input_resep_edit']['gizi']) ? $_SESSION['form_input_resep_edit']['gizi'] : $gizi_data_db;
unset($_SESSION['form_input_resep_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Resep: <?php echo htmlspecialchars($resep_data_db['nama_resep']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditresep.php" method="POST">
                <input type="hidden" name="id_resep" value="<?php echo htmlspecialchars($resep_data_db['id_resep']); ?>">

                <div class="form-group">
                    <label for="nama_resep">Nama Resep</label>
                    <input type="text" id="nama_resep" name="nama_resep" value="<?php echo htmlspecialchars($form_input['nama_resep'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="id_admin">Dibuat Oleh (Admin)</label>
                    <select id="id_admin" name="id_admin" required>
                        <option value="">-- Pilih Admin --</option>
                        <?php foreach ($users_admin as $ua) : ?>
                            <option value="<?php echo $ua['id_user']; ?>" <?php echo (isset($form_input['id_admin']) && $form_input['id_admin'] == $ua['id_user']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ua['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_kondisi">Untuk Kondisi Kesehatan</label>
                    <select id="id_kondisi" name="id_kondisi" required>
                        <option value="">-- Pilih Kondisi Kesehatan --</option>
                        <?php foreach ($kondisi_kesehatans as $kondisi) : ?>
                            <option value="<?php echo $kondisi['id_kondisi']; ?>" <?php echo (isset($form_input['id_kondisi']) && $form_input['id_kondisi'] == $kondisi['id_kondisi']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kondisi['nama_kondisi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cara_buat">Cara Membuat</label>
                    <textarea id="cara_buat" name="cara_buat" rows="8" required><?php echo htmlspecialchars($form_input['cara_buat'] ?? ''); ?></textarea>
                </div>

                <hr>
                <h4>Bahan-bahan Resep</h4>
                <div id="bahan-repeater-edit">
                    <?php foreach ($resep_bahans_input as $index => $item_bahan_input) : ?>
                    <div class="bahan-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #eee;">
                        <div style="flex: 5; margin-right: 10px;">
                            <select name="resep_bahan[<?php echo $index; ?>][id_bahan]" class="form-control" required>
                                <option value="">-- Pilih Bahan --</option>
                                <?php foreach ($bahans_all as $bahan_opt) : ?>
                                    <option value="<?php echo $bahan_opt['id_bahan']; ?>" <?php echo (isset($item_bahan_input['id_bahan']) && $item_bahan_input['id_bahan'] == $bahan_opt['id_bahan']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($bahan_opt['nama_bahan']) . " (" . htmlspecialchars($bahan_opt['satuan']) . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="flex: 3; margin-right: 10px;">
                            <input type="text" name="resep_bahan[<?php echo $index; ?>][jumlah]" value="<?php echo htmlspecialchars($item_bahan_input['jumlah'] ?? ''); ?>" placeholder="Jumlah" class="form-control" required>
                        </div>
                        <div style="flex: 1;">
                             <button type="button" class="btn btn-sm remove-bahan-item-edit" style="background-color: #dc3545;">×</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-bahan-btn-edit" class="btn btn-sm" style="background-color: var(--primary-green); color:white;">+ Tambah Bahan</button>
                <small style="display:block; margin-top:5px;">Satuan akan mengikuti bahan yang dipilih.</small>


                <hr>
                <h4>Informasi Gizi (Opsional, Per Porsi)</h4>
                <div class="form-group">
                    <label for="kalori_edit">Kalori (kkal)</label>
                    <input type="number" step="0.01" id="kalori_edit" name="gizi[kalori]" value="<?php echo htmlspecialchars($gizi_input['kalori'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="protein_edit">Protein (gram)</label>
                    <input type="number" step="0.01" id="protein_edit" name="gizi[protein]" value="<?php echo htmlspecialchars($gizi_input['protein'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="karbohidrat_edit">Karbohidrat (gram)</label>
                    <input type="number" step="0.01" id="karbohidrat_edit" name="gizi[karbohidrat]" value="<?php echo htmlspecialchars($gizi_input['karbohidrat'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="lemak_edit">Lemak (gram)</label>
                    <input type="number" step="0.01" id="lemak_edit" name="gizi[lemak]" value="<?php echo htmlspecialchars($gizi_input['lemak'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn">Update Resep</button>
                <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bahanRepeaterEdit = document.getElementById('bahan-repeater-edit');
    const addBahanBtnEdit = document.getElementById('add-bahan-btn-edit');
    let bahanIndexEdit = bahanRepeaterEdit.querySelectorAll('.bahan-item').length;

    const bahanOptionsHtmlEdit = `
        <option value="">-- Pilih Bahan --</option>
        <?php foreach ($bahans_all as $bahan_opt_js) : ?>
            <option value="<?php echo $bahan_opt_js['id_bahan']; ?>"><?php echo htmlspecialchars(addslashes($bahan_opt_js['nama_bahan'])) . " (" . htmlspecialchars(addslashes($bahan_opt_js['satuan'])) . ")"; ?></option>
        <?php endforeach; ?>
    `;

    function createBahanItemEdit(index) {
        const newItem = document.createElement('div');
        newItem.classList.add('bahan-item');
        newItem.style.display = 'flex';
        newItem.style.alignItems = 'center';
        newItem.style.marginBottom = '10px';
        newItem.style.padding = '10px';
        newItem.style.border = '1px solid #eee';
        newItem.innerHTML = `
            <div style="flex: 5; margin-right: 10px;">
                <select name="resep_bahan[${index}][id_bahan]" class="form-control" required>
                    ${bahanOptionsHtmlEdit}
                </select>
            </div>
            <div style="flex: 3; margin-right: 10px;">
                <input type="text" name="resep_bahan[${index}][jumlah]" placeholder="Jumlah" class="form-control" required>
            </div>
            <div style="flex: 1;">
                <button type="button" class="btn btn-sm remove-bahan-item-edit" style="background-color: #dc3545;">×</button>
            </div>
        `;
        return newItem;
    }

    addBahanBtnEdit.addEventListener('click', function() {
        const newItem = createBahanItemEdit(bahanIndexEdit);
        bahanRepeaterEdit.appendChild(newItem);
        bahanIndexEdit++;
    });

    bahanRepeaterEdit.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-bahan-item-edit')) {
             if (bahanRepeaterEdit.querySelectorAll('.bahan-item').length > 1) {
                event.target.closest('.bahan-item').remove();
            } else {
                alert('Minimal harus ada satu bahan dalam resep.');
            }
        }
    });
    
    if (bahanRepeaterEdit.querySelectorAll('.bahan-item').length === 0) {
         const firstItem = createBahanItemEdit(0);
         bahanRepeaterEdit.appendChild(firstItem);
         bahanIndexEdit = 1;
    }
});
</script>
<?php
if(isset($result_resep_db)) mysqli_free_result($result_resep_db);
if(isset($result_bahan_resep_db)) mysqli_free_result($result_bahan_resep_db);
if(isset($result_gizi_db)) mysqli_free_result($result_gizi_db);
if(isset($result_users_admin)) mysqli_free_result($result_users_admin);
if(isset($result_kondisi)) mysqli_free_result($result_kondisi);
if(isset($result_bahan_all_q)) mysqli_free_result($result_bahan_all_q);
require_once '../../templates/footer.php';
?>