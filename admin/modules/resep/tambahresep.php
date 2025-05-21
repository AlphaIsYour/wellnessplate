<?php
// Pastikan session sudah dimulai (biasanya di koneksi.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan BASE_URL sudah terdefinisi (dari config/koneksi.php)
// Jika file ini dipanggil sebelum koneksi.php, BASE_URL tidak akan ada.
// Solusi terbaik: pastikan koneksi.php di-include pertama di file halaman utama.
if (!defined('BASE_URL')) {
    // Fallback sederhana jika BASE_URL belum ada, tapi ini bukan solusi ideal
    // Sebaiknya pastikan koneksi.php di-include sebelum header ini
    // Jika kamu pakai folder 'wellnessplate2'
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host_name = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host_name .''); // Ganti 'wellnessplate2' jika perlu
}

$page_title_default = "WellnessPlate - Resep Sehat Untukmu";
$current_page_title = $page_title ?? $page_title_default; // Gunakan $page_title dari halaman atau default

$body_class = ''; // Untuk class body spesifik halaman
if (isset($is_auth_page) && $is_auth_page === true) { // Variabel ini bisa diset di halaman auth
    $body_class = 'auth-page';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_page_title); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if ($body_class === 'auth-page' && file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url( BASE_URL,PHP_URL_PATH) . '/assets/css/style_login.css')): ?>
        <link rel="stylesheet" href="/assets/css/style_login.css">
    <?php endif; ?>
    <link rel="icon" href="/assets/images/favicon.png" type="image/png">
    <!-- Tambahkan link CSS atau font lain jika perlu -->
</head>
<body class="<?php echo $body_class; ?>">
    <header class="site-header-frontend">
        <div class="container-navbar">
            <div class="logo-frontend">
                <a href="<?php echo BASE_URL; ?>/index.php">
                    <img src="/assets/images/logo-wellnessplate.png" alt="WellnessPlate Logo">
                    <span>WellnessPlate</span>
                </a>
            </div>
            <nav class="main-navigation-frontend">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/search.php">Cari Resep</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/artikel/index.php">Artikel</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/kategori/index.php">Kategori</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/about.php">Tentang Kami</a></li>
                </ul>
            </nav>
            <div class="user-actions-frontend">
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) : ?>
                    <span class="welcome-user">Halo, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?>!</span>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/logout_user.php" class="btn-nav-action">Logout</a>
                <?php else : ?>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php" class="btn-nav-action">Login</a>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php?form=register" class="btn-nav-action btn-register">Daftar</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-toggle" aria-label="Toggle Menu">☰</button>
        </div>
    </header>
    <div class="main-content-area-frontend">
    <?php // Konten utama akan dimulai setelah ini di file halaman spesifik ?>
<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once '../../templates/header.php';

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
unset($_SESSION['form_input_resep']);
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
            <form action="<?php echo $base_url; ?>konfirmasitambahresep.php" method="POST">
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
                <div id="bahan-repeater">
                    <?php foreach ($resep_bahans_input as $index => $item_bahan_input) : ?>
                    <div class="bahan-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #eee;">
                        <div style="flex: 5; margin-right: 10px;">
                            <label for="resep_bahan_<?php echo $index; ?>_id_bahan" class="sr-only">Bahan</label>
                            <select name="resep_bahan[<?php echo $index; ?>][id_bahan]" id="resep_bahan_<?php echo $index; ?>_id_bahan" class="form-control" required>
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
                <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left:10px;">Batal</a>
            </form>
        </div>
    </div>
</div>
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
                <select name="resep_bahan[${index}][id_bahan]" class="form-control" required>
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
});
</script>
<?php
require_once '../../templates/footer.php';
?>