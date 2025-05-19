<?php
// koneksi.php
$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "wellnessplate"; // Pastikan nama database benar

$koneksi = mysqli_connect($host, $username_db, $password_db, $database, 3307);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error()); // Tampilkan error koneksi untuk dev
}
mysqli_set_charset($koneksi, "utf8mb4");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- KONFIGURASI URL ---
// Sesuaikan 'wellnessplate2' jika nama folder aplikasimu berbeda

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host_name = $_SERVER['HTTP_HOST'];

define('MODULE_USERS_URL', '/modules/users/');
// --- END KONFIGURASI URL ---


// --- FUNGSI HELPER UMUM ---
// Pastikan fungsi ini hanya didefinisikan SEKALI di seluruh aplikasi
if (!function_exists('getJenisKelaminText')) {
    function getJenisKelaminText($kode) {
        if ($kode === 'L') return 'Laki-laki';
        if ($kode === 'P') return 'Perempuan';
        return 'Tidak Diketahui';
    }
}
// --- END FUNGSI HELPER ---
?>