<?php
require_once __DIR__ . '/config/koneksi.php';

$page_title = "WellnessPlate - Jaga Kesehatanmu, Mulai Dari Piringmu!";
$slider_banners = [
    [
        'image' => BASE_URL . '/assets/images/slider/1.png',
        'alt' => 'Promo Makanan Sehat Minggu Ini',
        'link' => BASE_URL . '/produk/salad'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/2.png', // Ganti dengan path gambar aslimu
        'alt' => 'Resep Baru Setiap Hari',
        'link' => BASE_URL . '/resep'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/3.png', // Ganti dengan path gambar aslimu
        'alt' => 'Tips Gaya Hidup Sehat',
        'link' => BASE_URL . '/artikel'
    ],
    [
        'image' => BASE_URL . '/assets/images/slider/4.png', // Ganti dengan path gambar aslimu
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
?>

<div class="main-content">

    <!-- 1. Search Bar Section -->
<?php 
require_once __DIR__ . '/includes/components/search-bar.php';
?>


    <!-- 2. Slider Banner Section -->
<?php 
require_once __DIR__ . '/includes/components/slider.php';
?>

    <!-- 4. Komponen Lain (Contoh: Artikel Terbaru) -->
<?php 
require_once __DIR__ . '/includes/components/article.php';
?>
    

    

    <!-- 3. Komponen Lain (Contoh: Kategori Populer) -->


    <?php 
require_once __DIR__ . '/includes/components/category.php';
?>
    <?php 
require_once __DIR__ . '/includes/components/about.php';
?>
    <?php 
require_once __DIR__ . '/includes/components/faq.php';
?>

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