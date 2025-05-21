<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    $base_url = "/wellnessplate"; 

    header("Location: " . $base_url . "/admin/index.php?error=Silakan login terlebih dahulu coy.");
    exit;
}

if (!isset($base_url)) {
    $base_url = "/wellnessplate"; 
}

$page_title = isset($page_title) ? $page_title : 'Admin WellnessPlate';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="./style.css">
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
        include_once __DIR__ . '/templates/sidebar.php';
        ?>
        <main class="content-area">
            <!-- Konten utama halaman akan ada di sini -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php?error=Akses ditolak. Silakan login.");
    exit;
}

$page_title = "Dashboard Admin"; 

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