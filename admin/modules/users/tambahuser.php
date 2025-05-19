<?php
// modules/users/tambahuser.php
require_once '../../koneksi.php';
require_once '../../templates/header.php';

$page_title = "Tambah Pengguna Baru";

$form_input = isset($_SESSION['form_input_user']) ? $_SESSION['form_input_user'] : [];
unset($_SESSION['form_input_user']);
?>
<div class="container mx-auto py-8">
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah Pengguna Baru</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . nl2br(htmlspecialchars($_SESSION['error_message'])) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="<?php echo MODULE_USERS_URL; ?>konfirmasitambahuser.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_input['username'] ?? ''); ?>" required maxlength="50">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    <small>Minimal 8 karakter.</small>
                </div>
                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
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
                
                <button type="submit" class="btn">Simpan Pengguna</button>
                <a href="<?php echo MODULE_USERS_URL; ?>user.php" class="btn btn-secondary" style="background-color: #6c757d; margin-left:10px;">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../templates/footer.php';
?>