<?php
// File: index.php (di root folder proyek wellnessplate/)

// Pastikan koneksi.php di-include untuk session_start() dan BASE_URL
// Path ini mengasumsikan koneksi.php ada di wellnessplate/config/koneksi.php
require_once __DIR__ . '/config/koneksi.php';

$page_title = "WellnessPlate - Jaga Kesehatanmu, Mulai Dari Piringmu!";

// --- Data untuk Slider Banner (Contoh) ---
// Di aplikasi nyata, ini bisa diambil dari database
$slider_banners = [
    [
        'image' => BASE_URL . '/assets/images/slider/1.png', // Ganti dengan path gambar aslimu
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
// Pastikan kamu punya folder assets/images/slider/ dan gambar-gambarnya.

// Panggil header.php untuk struktur HTML dasar
// Path ini mengasumsikan header.php ada di wellnessplate/includes/header.php
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">

    <!-- 1. Search Bar Section -->
<?php 
require_once __DIR__ . '/includes/components/search-bar.php'; // Panggil komponen slider
?>


    <!-- 2. Slider Banner Section -->
<?php 
require_once __DIR__ . '/includes/components/slider.php'; // Panggil komponen slider
?>

    <!-- 4. Komponen Lain (Contoh: Artikel Terbaru) -->
<?php 
require_once __DIR__ . '/includes/components/article.php'; // Panggil komponen slider
?>
    

    

    <!-- 3. Komponen Lain (Contoh: Kategori Populer) -->
    <section class="popular-categories-section" style="padding: 30px 0; text-align: center;">
        <div class="container" style="max-width: 1000px; margin: auto;">
            <h2>Kategori Populer</h2>
            <div class="categories-grid" style="display: flex; justify-content: space-around; flex-wrap: wrap; margin-top: 20px;">
                <?php
                // Contoh data kategori, bisa dari database
                $categories = [
                    ['name' => 'Makanan Diet', 'icon' => '🥗', 'link' => BASE_URL . '/kategori/diet'],
                    ['name' => 'Resep Vegan', 'icon' => '🥕', 'link' => BASE_URL . '/kategori/vegan'],
                    ['name' => 'Minuman Sehat', 'icon' => '🥤', 'link' => BASE_URL . '/kategori/minuman'],
                    ['name' => 'Camilan Sehat', 'icon' => '🥜', 'link' => BASE_URL . '/kategori/camilan'],
                ];
                foreach ($categories as $category):
                ?>
                <a href="<?php echo htmlspecialchars($category['link']); ?>" class="category-item" style="text-decoration: none; color: #333; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin: 10px; width: 200px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <div style="font-size: 2em; margin-bottom: 10px;"><?php echo $category['icon']; ?></div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php 
require_once __DIR__ . '/includes/components/about.php'; // Panggil komponen slider
?>
    <?php 
require_once __DIR__ . '/includes/components/faq.php'; // Panggil komponen slider
?>

    <?php
    // Tampilkan pesan jika ada (misalnya setelah login berhasil dari redirect)
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

</div> <!-- .main-content -->

<?php
// Panggil footer.php
require_once __DIR__ . '/includes/footer.php';
?>