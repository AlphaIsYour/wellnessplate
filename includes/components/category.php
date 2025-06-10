<?php
require_once __DIR__ . '/../../config/koneksi.php';

// Ambil 4 tags populer dari database
$query_tags = "SELECT t.id_tag, t.nama_tag, t.slug, 
               COUNT(rt.id_resep) as recipe_count 
               FROM tags t 
               LEFT JOIN resep_tags rt ON t.id_tag = rt.id_tag 
               GROUP BY t.id_tag 
               ORDER BY recipe_count DESC 
               LIMIT 4";

$result_tags = mysqli_query($koneksi, $query_tags);
$categories = [];

// Daftar emoticon berdasarkan kata kunci dalam nama tag
$icon_mapping = [
    'diet' => '🥗',
    'diabetes' => '🍽️',
    'jantung' => '❤️',
    'kolesterol' => '🥑',
    'sayur' => '🥬',
    'buah' => '🍎',
    'protein' => '🥩',
    'seafood' => '🐟',
    'ikan' => '🐟',
    'ayam' => '🍗',
    'daging' => '🥩',
    'nasi' => '🍚',
    'mie' => '🍜',
    'sup' => '🥣',
    'juice' => '🥤',
    'jus' => '🥤',
    'vegetarian' => '🥬',
    'vegan' => '🥬',
    'sehat' => '💪',
    'rendah' => '⭐',
    'tinggi' => '⭐'
];

if ($result_tags) {
    while ($row = mysqli_fetch_assoc($result_tags)) {
        // Cari icon yang sesuai berdasarkan nama tag
        $icon = '🍽️'; // Default icon
        $nama_tag_lower = strtolower($row['nama_tag']);
        
        foreach ($icon_mapping as $keyword => $emoji) {
            if (strpos($nama_tag_lower, $keyword) !== false) {
                $icon = $emoji;
                break;
            }
        }

        $categories[] = [
            'name' => $row['nama_tag'],
            'icon' => $icon,
            'tag' => $row['slug'],
            'count' => $row['recipe_count']
        ];
    }
    mysqli_free_result($result_tags);
}

// Jika tidak ada tags di database, gunakan default
if (empty($categories)) {
    $categories = [
        ['name' => 'Makanan Diet', 'icon' => '🥗', 'tag' => 'diet'],
        ['name' => 'Menu Diabetes', 'icon' => '🍽️', 'tag' => 'diabetes'],
        ['name' => 'Menu Jantung', 'icon' => '❤️', 'tag' => 'jantung'],
        ['name' => 'Menu Kolesterol', 'icon' => '🥑', 'tag' => 'kolesterol'],
    ];
}
?>

<section class="popular-categories-section" style="padding: 30px 0; text-align: center;">
    <div class="container" style="max-width: 1200px; margin: auto;">
        <h2 style="color: #333; margin-bottom: 30px;">Kategori Populer</h2>
        <div class="categories-grid" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 40px; margin-top: 20px;">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo BASE_URL; ?>/search.php?jenis[]=<?php echo urlencode($category['tag']); ?>" 
               class="category-item" 
               style="text-decoration: none; 
                      color: #333; 
                      background-color: #fff;
                      border: 1px solid #eee; 
                      border-radius: 12px; 
                      padding: 25px 20px; 
                      width: 200px; 
                      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                      transition: all 0.3s ease;">
                <div style="font-size: 2.5em; margin-bottom: 15px;"><?php echo $category['icon']; ?></div>
                <h3 style="margin: 0 0 10px 0; font-size: 1.2em;"><?php echo htmlspecialchars($category['name']); ?></h3>
                <?php if (isset($category['count']) && $category['count'] > 0): ?>
                <p style="margin: 0; color: #666; font-size: 0.9em;">
                    <?php echo $category['count']; ?> Resep
                </p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.category-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: var(--primary-green);
}

@media (max-width: 768px) {
    .categories-grid {
        padding: 0 15px;
    }
    .category-item {
        width: calc(50% - 30px);
        min-width: 150px;
    }
}

@media (max-width: 480px) {
    .category-item {
        width: 100%;
    }
}
</style>