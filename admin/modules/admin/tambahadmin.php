<?php
// modules/admin/tambahadmin.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/koneksi.php';
require_once '../../templates/header.php';

$page_title = "Tambah Admin Baru";
?>

<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold">Form Tambah Admin Baru</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']);
            }
            $username_val = isset($_SESSION['form_input']['username']) ? htmlspecialchars($_SESSION['form_input']['username']) : '';
            $nama_val = isset($_SESSION['form_input']['nama']) ? htmlspecialchars($_SESSION['form_input']['nama']) : '';
            $email_val = isset($_SESSION['form_input']['email']) ? htmlspecialchars($_SESSION['form_input']['email']) : '';
            unset($_SESSION['form_input']);
            ?>
            <form action="konfirmasitambahadmin.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $username_val; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $nama_val; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required>
                </div>
                <button type="submit" class="btn margin-bottom: 20px;">Simpan Admin</button>
                <a href="admin.php" class="btn" style="background-color: #6c757d;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../templates/footer.php';
?>