<?php
// File: wellnessplate/includes/config.php

// Atur error reporting untuk development (hapus atau set ke 0 untuk produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tentukan BASE_URL situs Anda
// Ganti dengan URL domain Anda saat di-deploy.
// Untuk localhost, biasanya seperti ini:
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = dirname($_SERVER['SCRIPT_NAME']); // Mendapatkan direktori root dari skrip

// Jika situs ada di subfolder, script_name akan berisi nama subfolder tersebut.
// Jika situs ada di root domain, script_name akan '/'.
// Kita perlu memastikan tidak ada double slash jika script_name adalah '/'
$base_path = ($script_name == '/' || $script_name == '\\') ? '' : $script_name;

define('BASE_URL', $protocol . $host . $base_path);

// Anda juga bisa menambahkan konfigurasi database di sini jika diperlukan nanti
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wellnessplate');
?>