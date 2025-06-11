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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

$page_title = "Edit Resep";
$id_resep_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/resep/";
if (empty($id_resep_to_edit)) {
    $_SESSION['error_message'] = "ID Resep tidak valid.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$stmt_get_resep = mysqli_prepare($koneksi, "SELECT id_resep, id_admin, id_kondisi, nama_resep, deskripsi, image, cara_buat, tags FROM resep WHERE id_resep = ?");
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

// First, get the admin table structure
$admin_columns = [];
$describe_result = mysqli_query($koneksi, "DESCRIBE admin");
if ($describe_result) {
    while ($row = mysqli_fetch_assoc($describe_result)) {
        $admin_columns[] = $row['Field'];
    }
}

// Build the query based on available columns
$select_fields = "id_admin";
if (in_array('username', $admin_columns)) {
    $select_fields .= ", username as nama_lengkap";
} elseif (in_array('nama', $admin_columns)) {
    $select_fields .= ", nama as nama_lengkap";
} elseif (in_array('name', $admin_columns)) {
    $select_fields .= ", name as nama_lengkap";
} else {
    $select_fields .= ", id_admin as nama_lengkap"; // Fallback to showing ID if no name column found
}

$query_users_admin = "SELECT $select_fields FROM admin ORDER BY id_admin ASC";
$result_users_admin = mysqli_query($koneksi, $query_users_admin);
if ($result_users_admin) {
    while ($row = mysqli_fetch_assoc($result_users_admin)) {
        $users_admin[] = $row;
    }
}

// Debug information
echo "<!-- Available admin columns: " . implode(", ", $admin_columns) . " -->";

$kondisi_kesehatans = [];
$query_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);
if ($result_kondisi) while ($row = mysqli_fetch_assoc($result_kondisi)) $kondisi_kesehatans[] = $row;

$bahans_all = [];
$query_bahan_all = "SELECT id_bahan, nama_bahan, satuan FROM bahan ORDER BY nama_bahan ASC";
$result_bahan_all_q = mysqli_query($koneksi, $query_bahan_all);
if ($result_bahan_all_q) while ($row = mysqli_fetch_assoc($result_bahan_all_q)) $bahans_all[] = $row;

// Get all tags from database
$all_tags = [];
$query_tags = "SELECT id_tag, nama_tag, slug FROM tags ORDER BY nama_tag ASC";
$result_tags = mysqli_query($koneksi, $query_tags);
if ($result_tags) {
    while ($row = mysqli_fetch_assoc($result_tags)) {
        $all_tags[] = $row;
    }
}


$form_input = isset($_SESSION['form_input_resep_edit']) ? $_SESSION['form_input_resep_edit'] : $resep_data_db;
$resep_bahans_input = isset($_SESSION['form_input_resep_edit']['resep_bahan']) && is_array($_SESSION['form_input_resep_edit']['resep_bahan']) 
                        ? $_SESSION['form_input_resep_edit']['resep_bahan'] 
                        : (empty($resep_bahans_db) ? [['id_bahan' => '', 'jumlah' => '']] : $resep_bahans_db);
$gizi_input = isset($_SESSION['form_input_resep_edit']['gizi']) ? $_SESSION['form_input_resep_edit']['gizi'] : $gizi_data_db;

// Get selected tags for this recipe from database
$selected_tags = [];
if (isset($_SESSION['form_input_resep_edit']['tags'])) {
    // If from session (form submission), it should be an array of tag IDs
    $selected_tags = is_array($_SESSION['form_input_resep_edit']['tags']) 
                    ? $_SESSION['form_input_resep_edit']['tags'] 
                    : [];
} else {
    // Get selected tags from resep_tags table
    $query_selected_tags = "SELECT rt.id_tag FROM resep_tags rt WHERE rt.id_resep = ?";
    $stmt_selected_tags = mysqli_prepare($koneksi, $query_selected_tags);
    if ($stmt_selected_tags) {
        mysqli_stmt_bind_param($stmt_selected_tags, "s", $id_resep_to_edit);
        mysqli_stmt_execute($stmt_selected_tags);
        $result_selected_tags = mysqli_stmt_get_result($stmt_selected_tags);
        while ($row = mysqli_fetch_assoc($result_selected_tags)) {
            $selected_tags[] = $row['id_tag'];
        }
        mysqli_stmt_close($stmt_selected_tags);
    }
}

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
            <form action="<?php echo $base_url; ?>konfirmasieditresep.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_resep" value="<?php echo htmlspecialchars($resep_data_db['id_resep']); ?>">

                <div class="form-group">
                    <label for="nama_resep">Nama Resep</label>
                    <input type="text" id="nama_resep" name="nama_resep" value="<?php echo htmlspecialchars($form_input['nama_resep'] ?? $resep_data_db['nama_resep'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Resep</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($form_input['deskripsi'] ?? $resep_data_db['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Foto Resep</label>
                    <?php if (!empty($resep_data_db['image'])): ?>
                        <div class="current-image">
                            <img src="<?php echo BASE_URL . '/assets/images/menu/' . htmlspecialchars($resep_data_db['image']); ?>" alt="Foto resep saat ini" style="max-width: 200px; margin: 10px 0;">
                            <p>Foto saat ini: <?php echo htmlspecialchars($resep_data_db['image']); ?></p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($resep_data_db['image']); ?>">
                    <small>Format yang diperbolehkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB. Biarkan kosong jika tidak ingin mengubah foto.</small>
                </div>

                <div class="form-group">
                    <label for="id_admin">Dibuat Oleh (Admin)</label>
                    <select id="id_admin" name="id_admin" required>
                        <option value="">-- Pilih Admin --</option>
                        <?php foreach ($users_admin as $ua) : ?>
                            <option value="<?php echo $ua['id_admin']; ?>" <?php echo (isset($form_input['id_admin']) ? $form_input['id_admin'] == $ua['id_admin'] : $resep_data_db['id_admin'] == $ua['id_admin']) ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $kondisi['id_kondisi']; ?>" <?php echo (isset($form_input['id_kondisi']) ? $form_input['id_kondisi'] == $kondisi['id_kondisi'] : $resep_data_db['id_kondisi'] == $kondisi['id_kondisi']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kondisi['nama_kondisi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tags Resep</label>
                    <div class="tags-container">
                        <div class="tag-options">
                            <?php foreach ($all_tags as $tag): ?>
                                <label class="tag-checkbox">
                                    <input type="checkbox" name="tags[]" value="<?php echo htmlspecialchars($tag['id_tag']); ?>"
                                        <?php echo (in_array($tag['id_tag'], $selected_tags)) ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    <?php echo htmlspecialchars($tag['nama_tag']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (empty($all_tags)): ?>
                            <p class="no-tags-message">Belum ada tags yang tersedia. Silakan buat tags terlebih dahulu di menu Tags.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cara_buat">Cara Membuat</label>
                    <textarea id="cara_buat" name="cara_buat" rows="8" required><?php echo htmlspecialchars($form_input['cara_buat'] ?? $resep_data_db['cara_buat'] ?? ''); ?></textarea>
                </div>

                <hr>
                <h4>Bahan-bahan Resep</h4>
                <div id="bahan-repeater-edit">
                    <?php foreach ($resep_bahans_input as $index => $item_bahan_input) : ?>
                    <div class="bahan-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #eee;">
                        <div style="flex: 5; margin-right: 10px;">
                            <select name="resep_bahan[<?php echo $index; ?>][id_bahan]" class="form-control bahan-select" required>
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
                <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                <select name="resep_bahan[${index}][id_bahan]" class="form-control bahan-select" required>
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
        $(newItem).find('.bahan-select').select2();
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

    // Initialize Select2 for existing selects
    $('.bahan-select').select2();
});
</script>

<style>
/* Tags styling */
.tags-container {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.tag-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.tag-checkbox {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background-color: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    font-size: 14px;
    user-select: none;
}

.tag-checkbox:hover {
    background-color: #e8f5e8;
    border-color: #28a745;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.tag-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    height: 18px;
    width: 18px;
    background-color: #ffffff;
    border: 2px solid #ced4da;
    border-radius: 3px;
    margin-right: 8px;
    position: relative;
    transition: all 0.3s ease;
}

.tag-checkbox input[type="checkbox"]:checked ~ .checkmark {
    background-color: #28a745;
    border-color: #28a745;
}

.tag-checkbox input[type="checkbox"]:checked ~ .checkmark:after {
    content: "";
    position: absolute;
    display: block;
    left: 5px;
    top: 2px;
    width: 4px;
    height: 8px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.tag-checkbox:has(input[type="checkbox"]:checked) {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.no-tags-message {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    margin: 20px 0;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    border: 1px dashed #dee2e6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tag-options {
        grid-template-columns: 1fr;
    }
    
    .tag-checkbox {
        padding: 10px 15px;
    }
}

.select2-container {
    width: 100% !important;
}

.current-image {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.current-image img {
    display: block;
    margin-bottom: 5px;
}
</style>

<?php
if(isset($result_resep_db)) mysqli_free_result($result_resep_db);
if(isset($result_bahan_resep_db)) mysqli_free_result($result_bahan_resep_db);
if(isset($result_gizi_db)) mysqli_free_result($result_gizi_db);
if(isset($result_users_admin)) mysqli_free_result($result_users_admin);
if(isset($result_kondisi)) mysqli_free_result($result_kondisi);
if(isset($result_bahan_all_q)) mysqli_free_result($result_bahan_all_q);
if(isset($result_tags)) mysqli_free_result($result_tags);
if(isset($result_selected_tags)) mysqli_free_result($result_selected_tags);
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