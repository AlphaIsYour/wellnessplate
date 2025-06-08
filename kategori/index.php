<?php
require_once '../config/koneksi.php';

$tag = isset($_GET['tag']) ? trim(htmlspecialchars($_GET['tag'])) : '';

if (empty($tag)) {
    header("Location: " . BASE_URL);
    exit;
}

// Convert tag to display format
$display_tag = ucwords(str_replace('_', ' ', $tag));

// Set page title
$page_title = "Menu untuk $display_tag";
require_once __DIR__ . '/../includes/header.php';

// Fetch recipes with the specified tag
$query = "SELECT r.*, a.nama as nama_admin, k.nama_kondisi 
          FROM resep r 
          LEFT JOIN admin a ON r.id_admin = a.id_admin
          LEFT JOIN kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi
          WHERE JSON_CONTAINS(r.tags, ?)
          ORDER BY r.tanggal_dibuat DESC";

$stmt = mysqli_prepare($koneksi, $query);
$json_tag = json_encode($tag);
mysqli_stmt_bind_param($stmt, "s", $json_tag);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$menus = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['tags'] = json_decode($row['tags'], true);
    $menus[] = $row;
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/search.css?v=<?= time() ?>">

<div class="search-page-container">
    <nav class="breadcrumb">
        <a href="<?= BASE_URL ?>">Home</a> &gt;
        <a href="<?= BASE_URL ?>/search.php">Menu</a> &gt;
        <span><?= htmlspecialchars($display_tag) ?></span>
    </nav>

    <div class="category-header">
        <h1>Menu untuk <?= htmlspecialchars($display_tag) ?></h1>
        <p class="results-count">Ditemukan <?= count($menus) ?> menu</p>
    </div>

    <?php if (empty($menus)): ?>
        <div class="no-results">
            <p>Belum ada menu yang tersedia untuk kategori ini.</p>
            <a href="<?= BASE_URL ?>/search.php" class="btn-back">Kembali ke Pencarian</a>
        </div>
    <?php else: ?>
        <div class="menu-grid">
            <?php foreach ($menus as $menu): ?>
                <div class="menu-card">
                    <a href="<?= BASE_URL ?>/menu/detail.php?id=<?= $menu['id_resep'] ?>">
                        <img src="<?= BASE_URL ?>/assets/images/menu/<?= htmlspecialchars($menu['image']) ?>" 
                             alt="<?= htmlspecialchars($menu['nama_resep']) ?>">
                    </a>
                    <div class="menu-card-content">
                        <div class="menu-info">
                            <h3>
                                <a href="<?= BASE_URL ?>/menu/detail.php?id=<?= $menu['id_resep'] ?>" 
                                   title="<?= htmlspecialchars($menu['nama_resep']) ?>">
                                    <?= htmlspecialchars($menu['nama_resep']) ?>
                                </a>
                            </h3>
                            <?php if (!empty($menu['tags'])): ?>
                                <div class="menu-tags">
                                    <?php 
                                    $tag_limit = 2;
                                    $displayed_tags = 0;
                                    foreach ($menu['tags'] as $menu_tag): 
                                        if ($displayed_tags >= $tag_limit) break;
                                        $is_active = $menu_tag === $tag;
                                    ?>
                                        <span class="tag <?= $is_active ? 'active' : '' ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $menu_tag))) ?>
                                        </span>
                                    <?php 
                                        $displayed_tags++;
                                    endforeach;
                                    
                                    $remaining_tags = count($menu['tags']) - $tag_limit;
                                    if ($remaining_tags > 0) echo '<span class="tag">...</span>';
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="<?= BASE_URL ?>/menu/detail.php?id=<?= $menu['id_resep'] ?>" class="btn-details">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 