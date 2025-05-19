<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php?error=Akses ditolak. Silakan login.");
    exit;
}

$page_title = "Dashboard Admin"; 
include_once 'templates/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h2>Selamat Datang di Dashboard WellnessPlate</h2>
    </div>
    <div class="card-body">
        <p>Halo, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</p>
        <p>Anda dapat mengelola semua data yang terkait dengan aplikasi WellnessPlate.</p>
        <p>Silakan gunakan menu navigasi di sebelah kiri untuk mengakses berbagai modul pengelolaan data.</p>

        <?php
        if (isset($_GET['message'])) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['message']) . "</div>";
        }
        if (isset($_GET['error_msg'])) { 
            echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error_msg']) . "</div>";
        }
        ?>
    </div>
</div>

<?php
include_once 'templates/footer.php';
?>