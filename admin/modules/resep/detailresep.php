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

$page_title = "Detail Resep";
$id_resep_to_view = $_GET['id'] ?? null;
$base_url = "/admin/modules/resep/";
if (empty($id_resep_to_view)) {
    $_SESSION['error_message'] = "ID Resep tidak valid untuk dilihat.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$resep_detail = null;
$stmt_resep_detail = mysqli_prepare($koneksi, "SELECT r.id_resep, r.nama_resep, r.deskripsi, r.image, r.cara_buat, r.tags, r.tanggal_dibuat, u.nama_lengkap AS nama_admin, k.nama_kondisi 
                                              FROM resep r 
                                              LEFT JOIN users u ON r.id_admin = u.id_user 
                                              LEFT JOIN kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi 
                                              WHERE r.id_resep = ?");
if ($stmt_resep_detail) {
    mysqli_stmt_bind_param($stmt_resep_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_resep_detail);
    $result_resep = mysqli_stmt_get_result($stmt_resep_detail);
    $resep_detail = mysqli_fetch_assoc($result_resep);
    mysqli_stmt_close($stmt_resep_detail);
} else {
    die("Gagal mempersiapkan query detail resep: " . mysqli_error($koneksi));
}

if (!$resep_detail) {
    $_SESSION['error_message'] = "Resep tidak ditemukan.";
    header('Location: ' . $base_url . 'resep.php');
    exit;
}

$bahan_list_detail = [];
$stmt_bahan_detail = mysqli_prepare($koneksi, "SELECT b.nama_bahan, rb.jumlah, b.satuan 
                                             FROM resep_bahan rb 
                                             JOIN bahan b ON rb.id_bahan = b.id_bahan 
                                             WHERE rb.id_resep = ? ORDER BY b.nama_bahan ASC");
if ($stmt_bahan_detail) {
    mysqli_stmt_bind_param($stmt_bahan_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_bahan_detail);
    $result_bahan = mysqli_stmt_get_result($stmt_bahan_detail);
    while ($row = mysqli_fetch_assoc($result_bahan)) {
        $bahan_list_detail[] = $row;
    }
    mysqli_stmt_close($stmt_bahan_detail);
} else {
    die("Gagal mempersiapkan query bahan resep: " . mysqli_error($koneksi));
}

$gizi_detail = null;
$stmt_gizi_detail = mysqli_prepare($koneksi, "SELECT kalori, protein, karbohidrat, lemak FROM gizi_resep WHERE id_resep = ?");
if ($stmt_gizi_detail) {
    mysqli_stmt_bind_param($stmt_gizi_detail, "s", $id_resep_to_view);
    mysqli_stmt_execute($stmt_gizi_detail);
    $result_gizi = mysqli_stmt_get_result($stmt_gizi_detail);
    $gizi_detail = mysqli_fetch_assoc($result_gizi);
    mysqli_stmt_close($stmt_gizi_detail);
} else {
    die("Gagal mempersiapkan query gizi resep: " . mysqli_error($koneksi));
}
if (!$gizi_detail) $gizi_detail = [];

// Predefined tags for display
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

$tags = json_decode($resep_detail['tags'] ?? '[]', true);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Detail Resep: <?php echo htmlspecialchars($resep_detail['nama_resep']); ?></h2>
            <a href="<?php echo $base_url; ?>editresep.php?id=<?php echo urlencode($id_resep_to_view); ?>" class="btn btn-sm btna">Edit Resep Ini</a>
        </div>
        <div class="card-body">
            <?php if (!empty($resep_detail['image'])): ?>
                <div class="recipe-image">
                    <img src="<?php echo BASE_URL . '/assets/images/menu/' . htmlspecialchars($resep_detail['image']); ?>" 
                         alt="<?php echo htmlspecialchars($resep_detail['nama_resep']); ?>"
                         style="max-width: 400px; width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
                </div>
            <?php endif; ?>

            <p><strong>Nama Resep:</strong> <?php echo htmlspecialchars($resep_detail['nama_resep']); ?></p>
            <p><strong>Deskripsi:</strong> <?php echo nl2br(htmlspecialchars($resep_detail['deskripsi'])); ?></p>
            <p><strong>Dibuat Oleh:</strong> <?php echo htmlspecialchars($resep_detail['nama_admin'] ?? 'N/A'); ?></p>
            <p><strong>Untuk Kondisi:</strong> <?php echo htmlspecialchars($resep_detail['nama_kondisi'] ?? 'N/A'); ?></p>
            <p><strong>Tanggal Dibuat:</strong> <?php echo htmlspecialchars(date('d F Y H:i', strtotime($resep_detail['tanggal_dibuat']))); ?></p>

            <?php if (!empty($tags)): ?>
                <div class="recipe-tags">
                    <h4>Tags:</h4>
                    <div class="tags-container">
                        <?php foreach ($tags as $tag): ?>
                            <?php
                            $tag_label = '';
                            $tag_category = '';
                            foreach ($tag_categories as $category => $category_tags) {
                                if (isset($category_tags[$tag])) {
                                    $tag_label = $category_tags[$tag];
                                    $tag_category = $category;
                                    break;
                                }
                            }
                            if (!empty($tag_label)):
                            ?>
                                <span class="tag <?php echo htmlspecialchars($tag_category); ?>">
                                    <?php echo htmlspecialchars($tag_label); ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
                
            <hr>
            <h4>Cara Membuat:</h4>
            <div style="white-space: pre-wrap; background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 5px;"><?php echo htmlspecialchars($resep_detail['cara_buat']); ?></div>

            <hr>
            <h4>Bahan-bahan:</h4>
            <?php if (!empty($bahan_list_detail)) : ?>
                <ul>
                    <?php foreach ($bahan_list_detail as $bahan_item) : ?>
                        <li><?php echo htmlspecialchars($bahan_item['jumlah']) . " " . htmlspecialchars($bahan_item['satuan']) . " — " . htmlspecialchars($bahan_item['nama_bahan']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>Tidak ada data bahan untuk resep ini.</p>
            <?php endif; ?>

            <hr>
            <h4>Informasi Gizi (Per Porsi):</h4>
            <?php if (!empty($gizi_detail) && (isset($gizi_detail['kalori']) || isset($gizi_detail['protein']) || isset($gizi_detail['karbohidrat']) || isset($gizi_detail['lemak']) )) : ?>
                <ul>
                    <?php if (isset($gizi_detail['kalori']) && $gizi_detail['kalori'] !== null): ?>
                        <li>Kalori: <?php echo htmlspecialchars(number_format($gizi_detail['kalori'], 1)); ?> kkal</li>
                    <?php endif; ?>
                    <?php if (isset($gizi_detail['protein']) && $gizi_detail['protein'] !== null): ?>
                        <li>Protein: <?php echo htmlspecialchars(number_format($gizi_detail['protein'], 1)); ?> gram</li>
                    <?php endif; ?>
                    <?php if (isset($gizi_detail['karbohidrat']) && $gizi_detail['karbohidrat'] !== null): ?>
                        <li>Karbohidrat: <?php echo htmlspecialchars(number_format($gizi_detail['karbohidrat'], 1)); ?> gram</li>
                    <?php endif; ?>
                    <?php if (isset($gizi_detail['lemak']) && $gizi_detail['lemak'] !== null): ?>
                        <li>Lemak: <?php echo htmlspecialchars(number_format($gizi_detail['lemak'], 1)); ?> gram</li>
                    <?php endif; ?>
                </ul>
            <?php else : ?>
                <p>Informasi gizi tidak tersedia atau belum diisi.</p>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <a href="<?php echo $base_url; ?>resep.php" class="btn btn-secondary" style="background-color: #6c757d;">Kembali ke Daftar Resep</a>
            </div>
        </div>
    </div>
</div>

<style>
.recipe-tags {
    margin: 15px 0;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.tag {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
    color: white;
}

.tag.jenis {
    background-color: #4CAF50;
}

.tag.kondisi {
    background-color: #2196F3;
}

.tag.karakteristik {
    background-color: #9C27B0;
}

.recipe-image {
    text-align: center;
    margin-bottom: 20px;
}

.recipe-image img {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<?php
if(isset($result_resep)) mysqli_free_result($result_resep);
if(isset($result_bahan)) mysqli_free_result($result_bahan);
if(isset($result_gizi)) mysqli_free_result($result_gizi);
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