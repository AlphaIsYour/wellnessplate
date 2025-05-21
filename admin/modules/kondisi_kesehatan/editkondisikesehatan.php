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

$page_title = "Edit Kondisi Kesehatan";
$id_kondisi_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/kondisi_kesehatan/";
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
                <a href="<?php echo $base_url; ?>kondisikesehatan.php" class="btn btn-secondary" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_kondisi_db);
if (!isset($base_url)) {
    $base_url = "/wellnessplate";
}
?>
        </main> 
    </div> 
<div  style="background-color:rgb(98, 98, 98);">
    <p style="margin-left: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
</div>
    </footer>
    <script src="../../script.js"></script>
</body>
</html>
