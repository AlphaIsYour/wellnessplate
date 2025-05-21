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
// modules/admin/editadmin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Edit Admin";
$base_url = "/admin/modules/admin";

$id_admin_to_edit = isset($_GET['id']) ? trim(mysqli_real_escape_string($koneksi, $_GET['id'])) : '';

if (empty($id_admin_to_edit)) {
    $_SESSION['error_message'] = "ID Admin tidak valid atau tidak disediakan.";
    header('Location: ' . $base_url . '/admin.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT username, nama, email FROM admin WHERE id_admin = ?");
$admin_data = null;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $id_admin_to_edit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $admin_data = $row;
    } else {
        $_SESSION['error_message'] = "Data admin dengan ID '" . htmlspecialchars($id_admin_to_edit) . "' tidak ditemukan.";
        header('Location: ' . $base_url . '/admin.php');
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error_message'] = "Gagal mempersiapkan query untuk mengambil data admin.";
    header('Location: ' . $base_url . '/admin.php');
    exit;
}

if ($admin_data === null) {
     $_SESSION['error_message'] = "Terjadi kesalahan saat mengambil data admin.";
     header('Location: ' . $base_url . '/admin.php');
     exit;
}

$form_input = isset($_SESSION['form_input_admin_edit']) ? $_SESSION['form_input_admin_edit'] : [];

$username_val = isset($form_input['username']) ? htmlspecialchars($form_input['username']) : htmlspecialchars($admin_data['username']);
$nama_val = isset($form_input['nama']) ? htmlspecialchars($form_input['nama']) : htmlspecialchars($admin_data['nama']);
$email_val = isset($form_input['email']) ? htmlspecialchars($form_input['email']) : htmlspecialchars($admin_data['email']);

unset($_SESSION['form_input_admin_edit']);

?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold">Form Edit Admin: <?php echo htmlspecialchars($admin_data['username']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']);
            }
            if (isset($_SESSION['success_message'])) { 
                echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
                unset($_SESSION['success_message']);
            }
            ?>
            <form action="<?php echo $base_url; ?>/konfirmasieditadmin.php" method="POST">
                <input type="hidden" name="id_admin" value="<?php echo htmlspecialchars($id_admin_to_edit); ?>">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $username_val; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $nama_val; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required>
                </div>

                <hr class="my-4">
                <p class="text-sm text-gray-600 mb-2">Kosongkan password jika tidak ingin mengubahnya.</p>
                
                <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter">
                </div>

                <div class="form-group">
                    <label for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                    <input type="password" id="konfirmasi_password_baru" name="konfirmasi_password_baru" placeholder="Ulangi Password Baru">
                </div>
                
                <button type="submit" class="btn margin-bottom: 20px;">Update Admin</button>
                <a href="<?php echo $base_url; ?>/admin.php" class="btn" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>
    <?php ?>
    </div> 

    <?php
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