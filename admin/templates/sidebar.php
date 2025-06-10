<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

function isActive($module, $page_name) {
    global $current_dir, $current_page;
    return ($current_dir == $module && $current_page == $page_name);
}
?>
<aside class="sidebar">
    <h3>Menu Navigasi</h3>
    <ul>
        <li><a href="/admin/dashboard.php" class="<?php echo ($current_page == 'dashboard.php' && $current_dir == basename($base_url)) ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="/admin/modules/admin/admin.php" class="<?php echo isActive('admin', 'admin.php') ? 'active' : ''; ?>">Kelola Admin</a></li>
        <li><a href="/admin/modules/users/user.php" class="<?php echo isActive('users', 'user.php') ? 'active' : ''; ?>">Kelola User</a></li>
        <li><a href="/admin/modules/bahan/bahan.php" class="<?php echo isActive('bahan', 'bahan.php') ? 'active' : ''; ?>">Kelola Bahan</a></li>
        <li><a href="/admin/modules/kondisi_kesehatan/kondisikesehatan.php" class="<?php echo isActive('kondisi_kesehatan', 'kondisikesehatan.php') ? 'active' : ''; ?>">Kelola Kondisi Kesehatan</a></li>
        <li><a href="/admin/modules/resep/resep.php" class="<?php echo isActive('resep', 'resep.php') ? 'active' : ''; ?>">Kelola Resep</a></li>
        <li><a href="/admin/modules/resep_kondisi/resepkondisi.php" class="<?php echo isActive('resepkondisi', 'resepkondisi.php') ? 'active' : ''; ?>">Kelola Resep Kondisi</a></li>
        <li><a href="/admin/modules/gizi/gizi.php" class="<?php echo isActive('gizi', 'gizi.php') ? 'active' : ''; ?>">Kelola Gizi</a></li>
    </ul>
</aside>