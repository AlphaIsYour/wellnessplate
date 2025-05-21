<?php
// File: pages/auth/logout_user.php
require_once __DIR__ . '/../../config/koneksi.php'; // Untuk BASE_URL dan session sudah start

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session cookie jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan sukses
header("Location: " . BASE_URL . "/pages/auth/index.php?message=Anda+telah+berhasil+logout.");
exit;
?>