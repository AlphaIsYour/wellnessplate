<?php
require_once '../config/koneksi.php';

// Get menu ID from URL and validate
$id = isset($_GET['id']) ? trim($_GET['id']) : ''; // Ambil sebagai string dan hapus spasi

if (empty($id)) { // Cek apakah ID-nya kosong
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
mysqli_stmt_bind_param($stmt, "s", $id); 
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: " . BASE_URL . "/search.php");
    exit;
}

$menu = mysqli_fetch_assoc($result);
$menu['tags'] = json_decode($menu['tags'], true);

// Fetch ingredients for this recipe
$query_bahan = "SELECT b.nama_bahan, b.satuan, rb.jumlah 
                FROM resep_bahan rb
                LEFT JOIN bahan b ON rb.id_bahan = b.id_bahan
                WHERE rb.id_resep = ?
                ORDER BY rb.id_resep_bahan";
$stmt_bahan = mysqli_prepare($koneksi, $query_bahan);
mysqli_stmt_bind_param($stmt_bahan, "s", $id);
mysqli_stmt_execute($stmt_bahan);
$result_bahan = mysqli_stmt_get_result($stmt_bahan);
$bahan_list = [];
while ($row = mysqli_fetch_assoc($result_bahan)) {
    $bahan_list[] = $row;
}

// Fetch nutrition information
$query_gizi = "SELECT kalori, protein, karbohidrat, lemak 
               FROM gizi_resep 
               WHERE id_resep = ?";
$stmt_gizi = mysqli_prepare($koneksi, $query_gizi);
mysqli_stmt_bind_param($stmt_gizi, "s", $id);
mysqli_stmt_execute($stmt_gizi);
$result_gizi = mysqli_stmt_get_result($stmt_gizi);
$gizi = mysqli_fetch_assoc($result_gizi);

// Fetch related recipes (same health condition)
$query_related = "SELECT r.id_resep, r.nama_resep, r.image, r.tags 
                 FROM resep r
                 WHERE r.id_kondisi = ? AND r.id_resep != ? 
                 ORDER BY r.tanggal_dibuat DESC 
                 LIMIT 5";
$stmt_related = mysqli_prepare($koneksi, $query_related);
mysqli_stmt_bind_param($stmt_related, "is", $menu['id_kondisi'], $id);
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

<style>
/* Clean & Minimal Styles */
.menu-ingredients {
    margin: 2.5rem 0;
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
}

.menu-ingredients h2 {
    color: #2d3748;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.menu-ingredients h2::before {
    content: '🥗';
    font-size: 1.2rem;
}

.ingredients-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 0.8rem;
}

.ingredient-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem 1.2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}

.ingredient-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e0;
    transform: translateY(-1px);
}

.ingredient-name {
    font-weight: 500;
    color: #2d3748;
    font-size: 0.95rem;
}

.ingredient-amount {
    background: #e2e8f0;
    color: #4a5568;
    padding: 0.3rem 0.8rem;
    border-radius: 16px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Nutrition Section */
.menu-nutrition {
    margin: 2.5rem 0;
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
}

.menu-nutrition h2 {
    color: #2d3748;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.menu-nutrition h2::before {
    content: '📊';
    font-size: 1.2rem;
}

.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
}

.nutrition-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem 1rem;
    text-align: center;
    transition: all 0.2s ease;
}

.nutrition-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e0;
    transform: translateY(-2px);
}

.nutrition-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #718096;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nutrition-value {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.2rem;
}

.nutrition-unit {
    color: #a0aec0;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Steps Section */
.menu-steps {
    margin: 2.5rem 0;
}

.menu-steps h2 {
    color: #2d3748;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.menu-steps h2::before {
    content: '👨‍🍳';
    font-size: 1.2rem;
}

.steps-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    position: relative;
    padding-left: 3rem;
}

.step-item::before {
    content: '→';
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: #4299e1;
    font-weight: bold;
}

.step-item:hover {
    border-color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateX(4px);
}

.step-item p {
    margin: 0;
    font-size: 1rem;
    line-height: 1.6;
    color: #4a5568;
}

/* Responsive Design */
@media (max-width: 768px) {
    .ingredients-list {
        grid-template-columns: 1fr;
    }
    
    .nutrition-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .menu-ingredients, .menu-nutrition {
        padding: 1.5rem;
        margin: 2rem 0;
    }
    
    .nutrition-value {
        font-size: 1.5rem;
    }
    
    .step-item:hover {
        transform: none;
    }
}

@media (max-width: 480px) {
    .nutrition-grid {
        grid-template-columns: 1fr;
    }
    
    .ingredient-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .step-item {
        padding-left: 1.5rem;
    }
    
    .step-item::before {
        display: none;
    }
}
</style>

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

            <?php if (!empty($bahan_list)): ?>
            <section class="menu-ingredients">
                <h2>Bahan-Bahan</h2>
                <div class="ingredients-list">
                    <?php foreach ($bahan_list as $bahan): ?>
                        <div class="ingredient-item">
                            <span class="ingredient-name"><?= htmlspecialchars($bahan['nama_bahan']) ?></span>
                            <span class="ingredient-amount"><?= htmlspecialchars($bahan['jumlah']) ?> <?= htmlspecialchars($bahan['satuan']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($menu['cara_buat'])): ?>
            <section class="menu-steps">
                <h2>Cara Pembuatan</h2>
                <div class="steps-list">
                    <?php 
                    $langkah_list = explode("\n", $menu['cara_buat']);
                    foreach ($langkah_list as $langkah): 
                        if (trim($langkah) !== ''): 
                    ?>
                        <div class="step-item">
                            <p><?= htmlspecialchars(trim($langkah)) ?></p>
                        </div>
                    <?php 
                        endif;
                    endforeach;
                    ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($gizi)): ?>
            <section class="menu-nutrition">
                <h2>Informasi Gizi</h2>
                <div class="nutrition-grid">
                    <div class="nutrition-item">
                        <span class="nutrition-label">Kalori</span>
                        <span class="nutrition-value"><?= htmlspecialchars($gizi['kalori']) ?></span>
                        <span class="nutrition-unit">kcal</span>
                    </div>
                    <div class="nutrition-item">
                        <span class="nutrition-label">Protein</span>
                        <span class="nutrition-value"><?= htmlspecialchars($gizi['protein']) ?></span>
                        <span class="nutrition-unit">gram</span>
                    </div>
                    <div class="nutrition-item">
                        <span class="nutrition-label">Karbohidrat</span>
                        <span class="nutrition-value"><?= htmlspecialchars($gizi['karbohidrat']) ?></span>
                        <span class="nutrition-unit">gram</span>
                    </div>
                    <div class="nutrition-item">
                        <span class="nutrition-label">Lemak</span>
                        <span class="nutrition-value"><?= htmlspecialchars($gizi['lemak']) ?></span>
                        <span class="nutrition-unit">gram</span>
                    </div>
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
                <?php 
                // Get tags for related menu
                $tags_query = "SELECT t.nama_tag 
                     FROM resep_tags rt 
                     JOIN tags t ON rt.id_tag = t.id_tag 
                     WHERE rt.id_resep = ?
                     LIMIT 3";
                $stmt_tags = mysqli_prepare($koneksi, $tags_query);
                mysqli_stmt_bind_param($stmt_tags, "s", $related['id_resep']);
                mysqli_stmt_execute($stmt_tags);
                $tags_result = mysqli_stmt_get_result($stmt_tags);
                $tags = [];
                while ($tag = mysqli_fetch_assoc($tags_result)) {
                $tags[] = $tag['nama_tag'];
                }
                
                if (!empty($tags)): 
                ?>
                <div class="related-menu-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
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