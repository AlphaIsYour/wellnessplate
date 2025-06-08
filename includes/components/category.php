<section class="popular-categories-section" style="padding: 30px 0; text-align: center;">
        <div class="container" style="max-width: 1000px; margin: auto;">
            <h2>Kategori Populer</h2>
            <div class="categories-grid" style="display: flex; justify-content: space-around; flex-wrap: wrap; margin-top: 20px;">
                <?php
                $categories = [
                    ['name' => 'Makanan Diet', 'icon' => '🥗', 'tag' => 'sayuran'],
                    ['name' => 'Menu Diabetes', 'icon' => '🍽️', 'tag' => 'diabetes'],
                    ['name' => 'Menu Jantung', 'icon' => '❤️', 'tag' => 'jantung'],
                    ['name' => 'Menu Kolesterol', 'icon' => '🥑', 'tag' => 'kolesterol'],
                ];
                foreach ($categories as $category):
                ?>
                <a href="<?= BASE_URL ?>/kategori?tag=<?= urlencode($category['tag']) ?>" class="category-item" style="text-decoration: none; color: #333; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin: 10px; width: 200px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <div style="font-size: 2em; margin-bottom: 10px;"><?php echo $category['icon']; ?></div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>