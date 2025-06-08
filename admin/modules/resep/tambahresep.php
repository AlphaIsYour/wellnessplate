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

$page_title = "Tambah Resep Baru";
$base_url = "/admin/modules/resep/";
$users_admin = [];
$query_users_admin = "SELECT id_user, nama_lengkap FROM users ORDER BY nama_lengkap ASC";
$result_users_admin = mysqli_query($koneksi, $query_users_admin);
if ($result_users_admin) {
    while ($row = mysqli_fetch_assoc($result_users_admin)) {
        $users_admin[] = $row;
    }
    mysqli_free_result($result_users_admin);
}

$kondisi_kesehatans = [];
$query_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);
if ($result_kondisi) {
    while ($row = mysqli_fetch_assoc($result_kondisi)) {
        $kondisi_kesehatans[] = $row;
    }
    mysqli_free_result($result_kondisi);
}

$bahans_all = [];
$query_bahan_all = "SELECT id_bahan, nama_bahan, satuan FROM bahan ORDER BY nama_bahan ASC";
$result_bahan_all_q = mysqli_query($koneksi, $query_bahan_all);
if ($result_bahan_all_q) {
    while ($row = mysqli_fetch_assoc($result_bahan_all_q)) {
        $bahans_all[] = $row;
    }
    mysqli_free_result($result_bahan_all_q);
}

$form_input = isset($_SESSION['form_input_resep']) ? $_SESSION['form_input_resep'] : [];
$resep_bahans_input = isset($form_input['resep_bahan']) && is_array($form_input['resep_bahan']) ? $form_input['resep_bahan'] : [['id_bahan' => '', 'jumlah' => '']];
$gizi_input = $form_input['gizi'] ?? [];
$tags_input = isset($form_input['tags']) ? json_decode($form_input['tags'], true) : [];
unset($_SESSION['form_input_resep']);

// Predefined tags
$tag_categories = [
    'jenis' => [
        'mie' => 'Mie',
        'jus' => 'Jus',
        'sayuran' => 'Sayuran',
        'daging' => 'Daging',
        'seafood' => 'Seafood',
        'buah' => 'Buah',
        'nasi' => 'Nasi',
        'sup' => 'Sup',
        'camilan' => 'Camilan',
        'sarapan' => 'Sarapan'
    ],
    'kondisi' => [
        'diabetes' => 'Diabetes',
        'diet' => 'Diet',
        'kolesterol' => 'Kolesterol',
        'asam_urat' => 'Asam Urat',
        'darah_tinggi' => 'Darah Tinggi',
        'jantung' => 'Jantung',
        'ginjal' => 'Ginjal',
        'maag' => 'Maag'
    ],
    'karakteristik' => [
        'rendah_kalori' => 'Rendah Kalori',
        'tinggi_protein' => 'Tinggi Protein',
        'rendah_garam' => 'Rendah Garam',
        'vegetarian' => 'Vegetarian',
        'vegan' => 'Vegan',
        'bebas_gluten' => 'Bebas Gluten'
    ]
];
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah Resep Baru</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasitambahresep.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_resep">Nama Resep</label>
                    <input type="text" id="nama_resep" name="nama_resep" value="<?php echo htmlspecialchars($form_input['nama_resep'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Resep</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($form_input['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Foto Resep</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <small>Format yang diperbolehkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB</small>
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
                    <label>Tags</label>
                    <?php foreach ($tag_categories as $category => $tags): ?>
                        <div class="tag-category">
                            <h4><?php echo ucfirst($category); ?></h4>
                            <div class="tag-options">
                                <?php foreach ($tags as $value => $label): ?>
                                    <label class="tag-checkbox">
                                        <input type="checkbox" name="tags[]" value="<?php echo $value; ?>"
                                            <?php echo (in_array($value, $tags_input)) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label for="cara_buat">Cara Membuat</label>
                    <textarea id="cara_buat" name="cara_buat" rows="8" required><?php echo htmlspecialchars($form_input['cara_buat'] ?? ''); ?></textarea>
                </div>

                <hr>
                <h4>Bahan-bahan Resep</h4>
                <div id="bahan-repeater">
                    <?php foreach ($resep_bahans_input as $index => $item_bahan_input) : ?>
                    <div class="bahan-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #eee;">
                        <div style="flex: 5; margin-right: 10px;">
                            <label for="resep_bahan_<?php echo $index; ?>_id_bahan" class="sr-only">Bahan</label>
                            <select name="resep_bahan[<?php echo $index; ?>][id_bahan]" id="resep_bahan_<?php echo $index; ?>_id_bahan" class="form-control bahan-select" required>
                                <option value="">-- Pilih Bahan --</option>
                                <?php foreach ($bahans_all as $bahan_opt) : ?>
                                    <option value="<?php echo $bahan_opt['id_bahan']; ?>" <?php echo (isset($item_bahan_input['id_bahan']) && $item_bahan_input['id_bahan'] == $bahan_opt['id_bahan']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($bahan_opt['nama_bahan']) . " (" . htmlspecialchars($bahan_opt['satuan']) . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="flex: 3; margin-right: 10px;">
                            <label for="resep_bahan_<?php echo $index; ?>_jumlah" class="sr-only">Jumlah</label>
                            <input type="text" name="resep_bahan[<?php echo $index; ?>][jumlah]" id="resep_bahan_<?php echo $index; ?>_jumlah" value="<?php echo htmlspecialchars($item_bahan_input['jumlah'] ?? ''); ?>" placeholder="Jumlah (cth: 100)" class="form-control" required>
                        </div>
                        <div style="flex: 1;">
                            <button type="button" class="btn btn-sm remove-bahan-item" style="background-color: #dc3545;">×</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-bahan-btn" class="btn btn-sm" style="background-color: var(--primary-green); color:white;">+ Tambah Bahan</button>
                <small style="display:block; margin-top:5px;">Satuan akan mengikuti bahan yang dipilih.</small>

                <hr>
                <h4>Informasi Gizi (Opsional, Per Porsi)</h4>
                <div class="form-group">
                    <label for="kalori">Kalori (kkal)</label>
                    <input type="number" step="0.01" id="kalori" name="gizi[kalori]" value="<?php echo htmlspecialchars($gizi_input['kalori'] ?? ''); ?>" placeholder="Contoh: 250.5">
                </div>
                <div class="form-group">
                    <label for="protein">Protein (gram)</label>
                    <input type="number" step="0.01" id="protein" name="gizi[protein]" value="<?php echo htmlspecialchars($gizi_input['protein'] ?? ''); ?>" placeholder="Contoh: 20.2">
                </div>
                <div class="form-group">
                    <label for="karbohidrat">Karbohidrat (gram)</label>
                    <input type="number" step="0.01" id="karbohidrat" name="gizi[karbohidrat]" value="<?php echo htmlspecialchars($gizi_input['karbohidrat'] ?? ''); ?>" placeholder="Contoh: 30.0">
                </div>
                <div class="form-group">
                    <label for="lemak">Lemak (gram)</label>
                    <input type="number" step="0.01" id="lemak" name="gizi[lemak]" value="<?php echo htmlspecialchars($gizi_input['lemak'] ?? ''); ?>" placeholder="Contoh: 10.7">
                </div>
                
                <button type="submit" class="btn">Simpan Resep</button>
                <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bahanRepeater = document.getElementById('bahan-repeater');
    const addBahanBtn = document.getElementById('add-bahan-btn');
    let bahanIndex = bahanRepeater.querySelectorAll('.bahan-item').length;

    const bahanOptionsHtml = `
        <option value="">-- Pilih Bahan --</option>
        <?php foreach ($bahans_all as $bahan_opt_js) : ?>
            <option value="<?php echo $bahan_opt_js['id_bahan']; ?>"><?php echo htmlspecialchars(addslashes($bahan_opt_js['nama_bahan'])) . " (" . htmlspecialchars(addslashes($bahan_opt_js['satuan'])) . ")"; ?></option>
        <?php endforeach; ?>
    `;

    function createBahanItem(index) {
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
                    ${bahanOptionsHtml}
                </select>
            </div>
            <div style="flex: 3; margin-right: 10px;">
                <input type="text" name="resep_bahan[${index}][jumlah]" placeholder="Jumlah" class="form-control" required>
            </div>
            <div style="flex: 1;">
                <button type="button" class="btn btn-sm remove-bahan-item" style="background-color: #dc3545;">×</button>
            </div>
        `;
        return newItem;
    }

    addBahanBtn.addEventListener('click', function() {
        const newItem = createBahanItem(bahanIndex);
        bahanRepeater.appendChild(newItem);
        $(newItem).find('.bahan-select').select2();
        bahanIndex++;
    });

    bahanRepeater.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-bahan-item')) {
            if (bahanRepeater.querySelectorAll('.bahan-item').length > 1) {
                event.target.closest('.bahan-item').remove();
            } else {
                alert('Minimal harus ada satu bahan dalam resep.');
            }
        }
    });

    if (bahanRepeater.querySelectorAll('.bahan-item').length === 0) {
         const firstItem = createBahanItem(0);
         bahanRepeater.appendChild(firstItem);
         bahanIndex = 1;
    }

    // Initialize Select2 for existing selects
    $('.bahan-select').select2();
});
</script>

<style>
.tag-category {
    margin-bottom: 15px;
}

.tag-category h4 {
    margin-bottom: 10px;
    color: #333;
}

.tag-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-checkbox {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    background-color: #f5f5f5;
    border-radius: 15px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.tag-checkbox:hover {
    background-color: #e9e9e9;
}

.tag-checkbox input[type="checkbox"] {
    margin-right: 5px;
}

.select2-container {
    width: 100% !important;
}
</style>

<?php
if (!isset($base_url)) {
    $base_url = "/wellnessplate";
}
?>
        </main> 
    </div> 
    <footer>
        <div style="background-color:rgb(98, 98, 98);">
            <p style="margin-left: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
        </div>
    </footer>
    <script src="../../script.js"></script>
</body>
</html>