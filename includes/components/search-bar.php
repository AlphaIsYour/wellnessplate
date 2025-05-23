<?php
// Pastikan BASE_URL sudah didefinisikan, misalnya dari config.php
if (!defined('BASE_URL')) {
    // Anda bisa include config.php di sini jika komponen ini dipanggil secara independen
    // atau pastikan file yang memanggil komponen ini sudah include config.php
    // require_once __DIR__ . '/../config.php'; // Contoh jika struktur foldernya seperti di atas
    // Untuk sementara, jika belum ada, kita set default agar tidak error saat testing komponen saja
    // define('BASE_URL', 'http://localhost/wellnessplate'); // HAPUS INI JIKA CONFIG.PHP SUDAH DI-INCLUDE DI FILE UTAMA
}
?>
<section class="search-section" style="padding: 5px 0; background-color: #fff; text-align: center; border-radius: 10px; margin-bottom: 20px;">
    <div class="container" style="max-width: 700px; margin: auto;">
        <form action="<?php echo BASE_URL; ?>/search.php" method="GET" style="display: flex; margin-top: 10px;">
            <input type="text" name="keyword" placeholder="Masukkan kata kunci pencarian..." style="flex-grow: 1; padding: 12px 15px; border: 1px solid #ccc; border-radius: 8px 0 0 8px; font-size: 1rem;" required>
            <button type="submit" style="padding: 12px 25px; background-color: #28a745; color: white; border: none; border-radius: 0 8px 8px 0; cursor: pointer; font-size: 1rem; font-weight: bold;">Cari</button>
        </form>
    </div>
</section>