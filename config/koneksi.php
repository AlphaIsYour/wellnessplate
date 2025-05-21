<?php
// config/koneksi.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// --- PENGATURAN KONEKSI DATABASE ---
$host_db = "localhost";         // Biasanya 'localhost'
$username_db = "root";          // Username database MySQL kamu
$password_db = "";              // Password database MySQL kamu (kosongkan jika tidak ada)
$nama_database = "wellnessplate"; // Nama database yang kamu gunakan

// Membuat koneksi ke database
$koneksi = mysqli_connect($host_db, $username_db, $password_db, $nama_database, 3307);

// Cek koneksi
if (!$koneksi) {
    // Untuk lingkungan development, tampilkan error detail.
    // Untuk produksi, sebaiknya log error dan tampilkan pesan umum.
    die("Koneksi ke database gagal: " . mysqli_connect_error() . " (Error No: " . mysqli_connect_errno() . ")");
}

// Set karakter set koneksi ke UTF-8 (disarankan)
mysqli_set_charset($koneksi, "utf8mb4");

// --- PENGATURAN SESSION ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- PENGATURAN URL DASAR APLIKASI ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// Tentukan host (nama domain atau localhost)
$host_server = $_SERVER['HTTP_HOST'];
// Tentukan base path jika proyek ada di subfolder. Jika di root, biarkan kosong atau '/'.
// Contoh jika proyek ada di wellnessplate.test/proyekku/, maka $subfolder = '/proyekku';
$subfolder = ''; // Jika langsung di root domain (wellnessplate.test), biarkan kosong
                 // Jika proyek ada di subfolder (misal wellnessplate.test/wellnessplate), maka: $subfolder = '/wellnessplate';

if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $host_server . $subfolder);
}
// --- URL MODUL ADMIN ---
// Diasumsikan folder admin ada di dalam folder utama proyek
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', BASE_URL . '/admin'); // Jika folder admin bernama 'admin'
}
if (!defined('MODULE_ADMIN_URL')) { // Untuk kelola admin (user admin)
    define('MODULE_ADMIN_URL', ADMIN_BASE_URL . '/modules/admin/');
}
if (!defined('MODULE_USERS_URL')) { // Untuk kelola pengguna biasa
    define('MODULE_USERS_URL', ADMIN_BASE_URL . '/modules/users/');
}
if (!defined('MODULE_BAHAN_URL')) {
    define('MODULE_BAHAN_URL', ADMIN_BASE_URL . '/modules/bahan/');
}
if (!defined('MODULE_KONDISI_URL')) {
    define('MODULE_KONDISI_URL', ADMIN_BASE_URL . '/modules/kondisi_kesehatan/');
}
if (!defined('MODULE_RESEP_URL')) {
    define('MODULE_RESEP_URL', ADMIN_BASE_URL . '/modules/resep/');
}
if (!defined('MODULE_GIZI_URL')) {
    define('MODULE_GIZI_URL', ADMIN_BASE_URL . '/modules/gizi/');
}

// --- URL HALAMAN FRONTEND (jika ada struktur pages/) ---
// Contoh: BASE_URL . '/pages/artikel/', BASE_URL . '/pages/auth/', dll.
// Ini bisa juga didefinisikan sesuai kebutuhan nanti saat membangun frontend.

// --- FUNGSI HELPER UMUM (jika ada) ---
// Contoh fungsi yang sudah ada:
if (!function_exists('getJenisKelaminText')) {
    function getJenisKelaminText($kode) {
        if ($kode === 'L') return 'Laki-laki';
        if ($kode === 'P') return 'Perempuan';
        return 'Tidak Diketahui';
    }
}

// Tambahkan fungsi helper lain di sini jika diperlukan
// Contoh: fungsi untuk memformat tanggal, sanitasi input, dll.

// --- PENGATURAN WAKTU (TIMEZONE) ---
// Set timezone default untuk fungsi date/time PHP
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktumu

?>