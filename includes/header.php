<?php
// ===== HEADER.PHP =====
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host_name = $_SERVER['HTTP_HOST'];
    $base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    define('BASE_URL', $protocol . $host_name . $base_path);
}

$page_title_default = "WellnessPlate - Resep Sehat Untukmu";
$current_page_title = $page_title ?? $page_title_default;

$body_class = '';
if (isset($is_auth_page) && $is_auth_page === true) { 
    $body_class = 'auth-page';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Content Security Policy untuk handle eval error -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; font-src 'self' https://unpkg.com; img-src 'self' data: https:;">
    
    <title><?php echo htmlspecialchars($current_page_title); ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/search.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main_style.css">
    
    <!-- External CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Conditional CSS for auth pages -->
    <?php if ($body_class === 'auth-page' && file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url(BASE_URL, PHP_URL_PATH) . '/assets/css/style_login.css')): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style_login.css">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>/assets/images/logo.svg" type="image/svg+xml">
</head>
<body class="<?php echo $body_class; ?>">
    <header class="site-header-frontend">
        <div class="container-navbar">
            <div class="logo-frontend">
                <a href="<?php echo BASE_URL; ?>/index.php">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logo.svg" alt="WellnessPlate Logo">
                    <span>WellnessPlate</span>
                </a>
            </div>
            <nav class="main-navigation-frontend">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/search.php">Cari Resep</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/artikel.php">Artikel</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/about.php">Tentang Kami</a></li>
                </ul>
            </nav>
            <div class="user-actions-frontend">
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) : ?>
                    <div class="user-dropdown">
                        <button class="dropdown-trigger">
                            <span class="welcome-user">Halo, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?></span>
                            <i class='bx bx-chevron-down'></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?php echo BASE_URL; ?>/pages/auth/logout_user.php" class="dropdown-item">
                                <i class='bx bx-log-out'></i> Logout
                            </a>
                            <button onclick="confirmDeleteAccount()" class="dropdown-item text-danger">
                                <i class='bx bx-trash'></i> Hapus Akun
                            </button>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php" class="btn-nav-action">Login</a>
                    <a href="<?php echo BASE_URL; ?>/pages/auth/index.php?form=register" class="btn-nav-action btn-register">Daftar</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-toggle" aria-label="Toggle Menu">☰</button>
        </div>
    </header>
    <div class="main-content-area-frontend">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownTrigger = document.querySelector('.dropdown-trigger');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownTrigger && dropdownMenu) {
        // Toggle dropdown on click
        dropdownTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownMenu.contains(e.target) && !dropdownTrigger.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
});

function confirmDeleteAccount() {
    Swal.fire({
        title: 'Verifikasi Penghapusan Akun',
        html: `
            <p class="mb-3">Untuk menghapus akun Anda, masukkan password Anda untuk konfirmasi:</p>
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password Anda">
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus akun saya!',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const password = document.getElementById('password').value;
            if (!password) {
                Swal.showValidationMessage('Password harus diisi');
                return false;
            }
            
            // Buat form untuk submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo BASE_URL; ?>/pages/auth/delete_account.php';
            
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;
            
            form.appendChild(passwordInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<style>
.user-dropdown {
    position: relative;
    display: inline-block;
}

.swal2-popup {
    font-family: inherit;
}

.swal2-input {
    margin: 1em auto !important;
    max-width: 300px;
    box-shadow: none !important;
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
}

.swal2-input:focus {
    border-color: #3085d6 !important;
    box-shadow: 0 0 0 2px rgba(48,133,214,0.2) !important;
}

.dropdown-trigger {
    display: flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 4px;
    color: inherit;
    transition: background-color 0.2s;
}

.dropdown-trigger:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 8px 0;
    min-width: 180px;
    display: none;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
    animation: fadeIn 0.2s ease;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.2s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    font-size: 1.2em;
}

.text-danger {
    color: #dc3545;
}

.text-danger:hover {
    background-color: #dc35451a;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .dropdown-menu {
        position: static;
        box-shadow: none;
        border: 1px solid #eee;
        margin-top: 5px;
    }
    
    .user-dropdown {
        width: 100%;
    }
    
    .dropdown-trigger {
        width: 100%;
        justify-content: space-between;
    }
}
</style>