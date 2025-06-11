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
<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Edit Bahan";
$id_bahan_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/bahan/";

if (empty($id_bahan_to_edit)) {
    $_SESSION['error_message'] = "ID Bahan tidak valid.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$stmt_get_bahan = mysqli_prepare($koneksi, "SELECT id_bahan, nama_bahan, satuan FROM bahan WHERE id_bahan = ?");
if (!$stmt_get_bahan) {
    $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get_bahan, "s", $id_bahan_to_edit);
mysqli_stmt_execute($stmt_get_bahan);
$result_bahan_db = mysqli_stmt_get_result($stmt_get_bahan);
$bahan_data_db = mysqli_fetch_assoc($result_bahan_db);
mysqli_stmt_close($stmt_get_bahan);

if (!$bahan_data_db) {
    $_SESSION['error_message'] = "Bahan dengan ID '" . htmlspecialchars($id_bahan_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'bahan.php');
    exit;
}

$form_input = isset($_SESSION['form_input_bahan_edit']) ? $_SESSION['form_input_bahan_edit'] : $bahan_data_db;
unset($_SESSION['form_input_bahan_edit']);
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Bahan: <?php echo htmlspecialchars($bahan_data_db['nama_bahan']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>konfirmasieditbahan.php" method="POST">
                <input type="hidden" name="id_bahan" value="<?php echo htmlspecialchars($bahan_data_db['id_bahan']); ?>">

                <div class="form-group">
                    <label for="nama_bahan">Nama Bahan</label>
                    <input type="text" id="nama_bahan" name="nama_bahan" value="<?php echo htmlspecialchars($form_input['nama_bahan'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" id="satuan" name="satuan" value="<?php echo htmlspecialchars($form_input['satuan'] ?? ''); ?>" placeholder="Contoh: gram, ml, buah, sdt" required maxlength="20">
                </div>
                
                <button type="submit" class="btn">Update Bahan</button>
                <a href="<?php echo $base_url; ?>bahan.php" class="btn btn-secondary" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_bahan_db);
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