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
// modules/users/edituser.php
require_once __DIR__ . '/../../../config/koneksi.php';

$page_title = "Edit Pengguna";

$id_user_to_edit = $_GET['id'] ?? null;
$base_url = "/admin/modules/users/";
if (empty($id_user_to_edit)) {
    $_SESSION['error_message'] = "ID Pengguna tidak valid.";
    header('Location: ' . $base_url . 'user.php');
    exit;
}

// Ambil data pengguna dari database
$stmt_get_user = mysqli_prepare($koneksi, "SELECT username, email, nama_lengkap, tanggal_lahir, jenis_kelamin FROM users WHERE id_user = ?");
if (!$stmt_get_user) {
    $_SESSION['error_message'] = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
    header('Location: ' . $base_url . 'user.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get_user, "s", $id_user_to_edit);
mysqli_stmt_execute($stmt_get_user);
$result_user = mysqli_stmt_get_result($stmt_get_user);
$user_data_db = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_get_user);

if (!$user_data_db) {
    $_SESSION['error_message'] = "Pengguna dengan ID '" . htmlspecialchars($id_user_to_edit) . "' tidak ditemukan.";
    header('Location: ' . $base_url . 'user.php');
    exit;
}

// Konversi jenis_kelamin DB ke teks form
$user_data_db['jenis_kelamin_text'] = '';
if ($user_data_db['jenis_kelamin'] === 'L') $user_data_db['jenis_kelamin_text'] = 'Laki-laki';
elseif ($user_data_db['jenis_kelamin'] === 'P') $user_data_db['jenis_kelamin_text'] = 'Perempuan';

// Ambil data form sebelumnya jika ada (misal, setelah validasi gagal) atau gunakan data dari DB
$form_input = isset($_SESSION['form_input_user_edit']) ? $_SESSION['form_input_user_edit'] : $user_data_db;

// Jika session form_input_user_edit ADA, berarti ada error sebelumnya,
// nilai jenis_kelamin_text dari session lebih prioritas.
// Jika TIDAK ADA session, berarti baru load, jadi $user_data_db['jenis_kelamin_text'] yang digunakan.
if (!isset($_SESSION['form_input_user_edit'])) {
    $form_input['jenis_kelamin_text'] = $user_data_db['jenis_kelamin_text'];
} else {
    // Pastikan 'jenis_kelamin_text' ada di form_input dari session
    // Jika tidak, fallback ke data DB
    $form_input['jenis_kelamin_text'] = $form_input['jenis_kelamin_text'] ?? $user_data_db['jenis_kelamin_text'];
}


unset($_SESSION['form_input_user_edit']);

?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Edit Pengguna: <?php echo htmlspecialchars($user_data_db['username']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="konfirmasiedituser.php" method="POST">
                <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user_to_edit); ?>">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_input['username'] ?? ''); ?>" required maxlength="50">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_input['email'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($form_input['nama_lengkap'] ?? ''); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($form_input['tanggal_lahir'] ?? ''); ?>" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin_text" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" <?php echo (isset($form_input['jenis_kelamin_text']) && $form_input['jenis_kelamin_text'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php echo (isset($form_input['jenis_kelamin_text']) && $form_input['jenis_kelamin_text'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <hr style="margin: 20px 0;">
                <p><strong>Ubah Password (Opsional):</strong></p>
                <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru" minlength="8">
                    <small>Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</small>
                </div>
                <div class="form-group">
                    <label for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                    <input type="password" id="konfirmasi_password_baru" name="konfirmasi_password_baru">
                </div>
                
                <button type="submit" class="btn">Update Pengguna</button>
                <a href="<?php echo $base_url; ?>user.php" class="btn" style="background-color: #6c757d;"> Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result_user);
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