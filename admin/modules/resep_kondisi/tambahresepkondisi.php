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
        include_once '../../templates/sidebar.php';
        ?>
        <main class="content-area">
            <?php
            require_once __DIR__ . '/../../../config/koneksi.php';

            $page_title = "Tambah Resep - Kondisi Kesehatan";
            $base_url = "/admin/modules/resep_kondisi/";

            // Ambil daftar resep
            $query_resep = "SELECT id_resep, nama_resep FROM resep ORDER BY nama_resep ASC";
            $result_resep = mysqli_query($koneksi, $query_resep);
            $resep_list = [];
            while ($row = mysqli_fetch_assoc($result_resep)) {
                $resep_list[] = $row;
            }

            // Ambil daftar kondisi kesehatan
            $query_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
            $result_kondisi = mysqli_query($koneksi, $query_kondisi);
            $kondisi_list = [];
            while ($row = mysqli_fetch_assoc($result_kondisi)) {
                $kondisi_list[] = $row;
            }

            $form_input = isset($_SESSION['form_input']) ? $_SESSION['form_input'] : [];
            unset($_SESSION['form_input']);
            ?>

            <div class="container mx-auto py-8">
                <div class="card">
                    <div class="card-header">
                        <h2>Tambah Resep - Kondisi Kesehatan</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_SESSION['error_message'])) {
                            echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <form action="<?php echo $base_url; ?>konfirmasitambahresepkondisi.php" method="POST">
                            <div class="form-group">
                                <label for="id_resep">Pilih Resep</label>
                                <select id="id_resep" name="id_resep" required>
                                    <option value="">-- Pilih Resep --</option>
                                    <?php foreach ($resep_list as $resep): ?>
                                        <option value="<?php echo $resep['id_resep']; ?>" 
                                            <?php echo (isset($form_input['id_resep']) && $form_input['id_resep'] == $resep['id_resep']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($resep['nama_resep']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="id_kondisi">Pilih Kondisi Kesehatan</label>
                                <select id="id_kondisi" name="id_kondisi" required>
                                    <option value="">-- Pilih Kondisi Kesehatan --</option>
                                    <?php foreach ($kondisi_list as $kondisi): ?>
                                        <option value="<?php echo $kondisi['id_kondisi']; ?>"
                                            <?php echo (isset($form_input['id_kondisi']) && $form_input['id_kondisi'] == $kondisi['id_kondisi']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kondisi['nama_kondisi']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn">Simpan</button>
                            <a href="<?php echo $base_url; ?>resepkondisi.php" class="btn btn-secondary" style="background-color: #6c757d;">Batal</a>
                        </form>
                    </div>
                </div>
            </div>

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