<?php
require_once __DIR__ . '/config/koneksi.php';

$account_deleted_message = '';
$delete_error_message = '';

if (isset($_COOKIE['account_deleted'])) {
    $account_deleted_message = $_COOKIE['account_deleted'];
    setcookie('account_deleted', '', time() - 3600, '/');
}

if (isset($_COOKIE['delete_error'])) {
    $delete_error_message = $_COOKIE['delete_error'];
    setcookie('delete_error', '', time() - 3600, '/');
}

$page_title = "WellnessPlate - Jaga Kesehatanmu, Mulai Dari Piringmu!";
$slider_banners = [
    [
        'image' => BASE_URL . '/assets/images/slider/1.png',
        'alt' => 'Promo Makanan Sehat Minggu Ini',
        'link' => BASE_URL . '/produk/salad'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/2.png',
        'alt' => 'Resep Baru Setiap Hari',
        'link' => BASE_URL . '/resep'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/3.png',
        'alt' => 'Tips Gaya Hidup Sehat',
        'link' => BASE_URL . '/artikel'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/4.png',
        'alt' => 'Tips Gaya Hidup Sehat',
        'link' => BASE_URL . '/artikel'
    ]
];

$slider_options = [
    'id' => 'homepage_slider',
    'autoplay' => true,
    'autoplay_speed' => 5000,
    'show_captions' => true,
    'aspect_ratio' => '16:9'
];

require_once __DIR__ . '/includes/header.php';

if ($account_deleted_message) {
    echo "<script>
        Swal.fire({
            title: 'Akun Berhasil Dihapus',
            text: '" . addslashes($account_deleted_message) . "',
            icon: 'success',
            confirmButtonColor: '#28a745'
        });
    </script>";
}

if ($delete_error_message) {
    echo "<script>
        Swal.fire({
            title: 'Gagal Menghapus Akun',
            text: '" . addslashes($delete_error_message) . "',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    </script>";
}
?>

<div class="main-content">

    <!-- 1. ini Search Bar Section -->
    <?php 
    require_once __DIR__ . '/includes/components/search-bar.php';
    ?>

    <!-- 2. ini Slider Banner Section -->
    <?php 
    require_once __DIR__ . '/includes/components/slider.php';
    ?>

    <!-- 3. ini Article Section -->
    <?php 
    require_once __DIR__ . '/includes/components/article.php';
    ?>

    <!-- 4. ini Category Section -->
    <?php 
    require_once __DIR__ . '/includes/components/category.php';
    ?>

    <!-- 5. ini About Section -->
    <?php 
    require_once __DIR__ . '/includes/components/about.php';
    ?>

    <!-- 6. ini Faq Section -->
    <?php 
    require_once __DIR__ . '/includes/components/faq.php';
    ?>

    <!-- 7. ini Footer -->
    <?php
    if (isset($_GET['message'])) {
        echo "<p style='text-align: center; color: green; border: 1px solid green; padding: 10px; margin: 15px auto; max-width: 600px;'>" . htmlspecialchars($_GET['message']) . "</p>";
    }
    ?>

    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <div style="text-align: center; padding: 20px;">
            <p>Halo, <strong><?php echo htmlspecialchars($_SESSION['user_nama']); ?></strong>! Selamat datang kembali.</p>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 20px; background-color: #e9ecef; margin-top: 30px;">
            <p>Untuk pengalaman terbaik, <a href="<?php echo BASE_URL; ?>/pages/auth/index.php?form=login">login</a> atau <a href="<?php echo BASE_URL; ?>/pages/auth/index.php?form=register">daftar</a> sekarang!</p>
        </div>
    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>