<?php
require_once '../config/koneksi.php';

// Get menu ID from URL and validate
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: " . BASE_URL . "/search.php");
    exit;
}

// Fetch menu details
$query = "SELECT r.*, a.nama as nama_admin, k.nama_kondisi 
          FROM resep r 
          LEFT JOIN admin a ON r.id_admin = a.id_admin
          LEFT JOIN kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi
          WHERE r.id_resep = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: " . BASE_URL . "/search.php");
    exit;
}

$menu = mysqli_fetch_assoc($result);
$menu['tags'] = json_decode($menu['tags'], true);

// Fetch related recipes (same health condition)
$query_related = "SELECT r.id_resep, r.nama_resep, r.image, r.tags 
                 FROM resep r
                 WHERE r.id_kondisi = ? AND r.id_resep != ? 
                 ORDER BY r.tanggal_dibuat DESC 
                 LIMIT 5";
$stmt_related = mysqli_prepare($koneksi, $query_related);
mysqli_stmt_bind_param($stmt_related, "ii", $menu['id_kondisi'], $id);
mysqli_stmt_execute($stmt_related);
$result_related = mysqli_stmt_get_result($stmt_related);
$related_menus = [];
while ($row = mysqli_fetch_assoc($result_related)) {
    $row['tags'] = json_decode($row['tags'], true);
    $related_menus[] = $row;
}

// Set page title
$page_title = $menu['nama_resep'];
require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/detail.css?v=<?= time() ?>">

<div class="detail-page-container">
    <nav class="breadcrumb">
        <a href="<?= BASE_URL ?>">Home</a> &gt;
        <a href="<?= BASE_URL ?>/search.php">Menu</a> &gt;
        <span><?= htmlspecialchars($menu['nama_resep']) ?></span>
    </nav>

    <h1 class="menu-title"><?= htmlspecialchars($menu['nama_resep']) ?></h1>

    <div class="menu-detail-layout">
        <div class="menu-main-content">
            <div class="menu-header">
                <div class="menu-image">
                    <img src="<?= BASE_URL ?>/assets/images/menu/<?= htmlspecialchars($menu['image']) ?>" 
                         alt="<?= htmlspecialchars($menu['nama_resep']) ?>">
                </div>
                <div class="menu-info">
                    <?php if (!empty($menu['tags'])): ?>
                    <div class="menu-tags">
                        <?php foreach ($menu['tags'] as $tag): ?>
                            <span class="tag"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $tag))) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="menu-meta">
                        <p><strong>Kondisi:</strong> <?= htmlspecialchars($menu['nama_kondisi']) ?></p>
                        <p><strong>Dibuat oleh:</strong> <?= htmlspecialchars($menu['nama_admin']) ?></p>
                        <p><strong>Tanggal dibuat:</strong> <?= date('d F Y', strtotime($menu['tanggal_dibuat'])) ?></p>
                    </div>

                    <div class="menu-description">
                        <h2>Deskripsi</h2>
                        <p><?= nl2br(htmlspecialchars($menu['deskripsi'])) ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($menu['cara_buat'])): ?>
            <section class="menu-steps">
                <h2>Cara Pembuatan</h2>
                <div class="steps-list">
                    <?php 
                    $langkah_list = explode("\n", $menu['cara_buat']);
                    foreach ($langkah_list as $index => $langkah): 
                        if (trim($langkah) !== ''): 
                    ?>
                        <div class="step-item">
                            <span class="step-number"><?= $index + 1 ?></span>
                            <p><?= htmlspecialchars(trim($langkah)) ?></p>
                        </div>
                    <?php 
                        endif;
                    endforeach;
                    ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <aside class="menu-sidebar">
            <h2>Menu Terkait untuk <?= htmlspecialchars($menu['nama_kondisi']) ?></h2>
            <div class="related-menus">
                <?php if (empty($related_menus)): ?>
                    <p class="no-related">Belum ada menu terkait untuk kondisi ini.</p>
                <?php else: ?>
                    <?php foreach ($related_menus as $related): ?>
                    <a href="<?= BASE_URL ?>/menu/detail.php?id=<?= $related['id_resep'] ?>" class="related-menu-card">
                        <div class="related-menu-image">
                            <img src="<?= BASE_URL ?>/assets/images/menu/<?= htmlspecialchars($related['image']) ?>" 
                                 alt="<?= htmlspecialchars($related['nama_resep']) ?>">
                        </div>
                        <div class="related-menu-info">
                            <h3><?= htmlspecialchars($related['nama_resep']) ?></h3>
                            <?php if (!empty($related['tags'])): ?>
                            <div class="related-menu-tags">
                                <?php 
                                $displayed_tags = array_slice($related['tags'], 0, 2);
                                foreach ($displayed_tags as $tag): 
                                ?>
                                    <span class="tag"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $tag))) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 