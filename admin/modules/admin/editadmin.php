<?php
// modules/admin/editadmin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../koneksi.php'; // Pastikan $base_url sudah ada di sini atau didefinisikan
require_once '../../templates/header.php';

$page_title = "Edit Admin";
$base_url = "/modules/admin";

// Ambil id_admin dari URL. id_admin adalah VARCHAR.
$id_admin_to_edit = isset($_GET['id']) ? trim(mysqli_real_escape_string($koneksi, $_GET['id'])) : '';

if (empty($id_admin_to_edit)) {
    $_SESSION['error_message'] = "ID Admin tidak valid atau tidak disediakan.";
    header('Location: ' . $base_url . '/admin.php');
    exit;
}

// Ambil data admin dari database
$stmt = mysqli_prepare($koneksi, "SELECT username, nama, email FROM admin WHERE id_admin = ?");
$admin_data = null; // Inisialisasi $admin_data

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
    // Sebaiknya log error ini, jangan tampilkan mysqli_error ke user di production
    $_SESSION['error_message'] = "Gagal mempersiapkan query untuk mengambil data admin.";
    // error_log("MySQL Prep Error (get admin for edit): " . mysqli_error($koneksi));
    header('Location: ' . $base_url . '/admin.php');
    exit;
}

// Jika $admin_data null setelah query (meskipun seharusnya sudah ditangani di atas), handle sebagai error
if ($admin_data === null) {
     $_SESSION['error_message'] = "Terjadi kesalahan saat mengambil data admin.";
     header('Location: ' . $base_url . '/admin.php');
     exit;
}


// Jika ada input sebelumnya karena error validasi, gunakan itu. Jika tidak, gunakan data dari DB.
// $_SESSION['form_input_admin_edit'] akan di-set di konfirmasieditadmin.php jika ada error
$form_input = isset($_SESSION['form_input_admin_edit']) ? $_SESSION['form_input_admin_edit'] : [];

// Menentukan nilai untuk ditampilkan di form
// Prioritaskan data dari session (jika ada error sebelumnya), lalu data dari DB
$username_val = isset($form_input['username']) ? htmlspecialchars($form_input['username']) : htmlspecialchars($admin_data['username']);
$nama_val = isset($form_input['nama']) ? htmlspecialchars($form_input['nama']) : htmlspecialchars($admin_data['nama']);
$email_val = isset($form_input['email']) ? htmlspecialchars($form_input['email']) : htmlspecialchars($admin_data['email']);
// Password tidak diisi ulang, jadi tidak perlu diambil dari $form_input['password_baru'] untuk ditampilkan

unset($_SESSION['form_input_admin_edit']); // Hapus session setelah digunakan

?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold">Form Edit Admin: <?php echo htmlspecialchars($admin_data['username']); ?></h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                // Pesan error dari konfirmasi biasanya sudah dalam format HTML (dengan <br>)
                echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']);
            }
            if (isset($_SESSION['success_message'])) { // Jarang ada success message di halaman edit, tapi bisa saja
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

<?php
require_once '../../templates/footer.php';
?>