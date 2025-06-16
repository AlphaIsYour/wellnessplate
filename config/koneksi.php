<?php
// config/koneksi.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host_db = "localhost";  
$username_db = "root"; 
$password_db = "";  
$nama_database = "wellnessplate";

$koneksi = mysqli_connect($host_db, $username_db, $password_db, $nama_database, 3307);
define('BASE_URL', 'http://wellnessplate.test');

if (!$koneksi) {

    die("Koneksi ke database gagal: " . mysqli_connect_error() . " (Error No: " . mysqli_connect_errno() . ")");
}

mysqli_set_charset($koneksi, "utf8mb4");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host_server = $_SERVER['HTTP_HOST'];
$subfolder = '';

if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $host_server . $subfolder);
}

if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', BASE_URL . '/admin');
}
if (!defined('MODULE_ADMIN_URL')) {
    define('MODULE_ADMIN_URL', ADMIN_BASE_URL . '/modules/admin/');
}
if (!defined('MODULE_USERS_URL')) {
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

if (!function_exists('getJenisKelaminText')) {
    function getJenisKelaminText($kode) {
        if ($kode === 'L') return 'Laki-laki';
        if ($kode === 'P') return 'Perempuan';
        return 'Tidak Diketahui';
    }
}

date_default_timezone_set('Asia/Jakarta');

?>