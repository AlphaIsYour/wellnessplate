<?php
require_once 'config/koneksi.php';
$page_title = "Artikel Kesehatan dan Gizi";
require_once 'includes/header.php';

// Pagination settings
$articles_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $articles_per_page;

// All articles data
$all_articles = [
    [
        'title' => 'Manfaat Sarapan Pagi untuk Produktivitas Optimal',
        'excerpt' => 'Sarapan sering disebut sebagai waktu makan terpenting, dan bukan tanpa alasan. Memulai hari dengan nutrisi yang tepat dapat meningkatkan fokus, energi, dan produktivitas Anda sepanjang hari.',
        'image' => BASE_URL . '/assets/images/menu/1.jpg',
        'category' => 'Pola Makan',
        'date' => '15 Mar 2024',
        'link' => BASE_URL . '/artikel/manfaat-sarapan'
    ],
    [
        'title' => '5 Olahraga Ringan yang Efektif Dilakukan di Rumah',
        'excerpt' => 'Tetap aktif secara fisik tidak harus selalu pergi ke gym. Ada banyak olahraga ringan namun efektif yang bisa Anda lakukan dari kenyamanan rumah Anda sendiri.',
        'image' => BASE_URL . '/assets/images/menu/2.jpg',
        'category' => 'Olahraga',
        'date' => '14 Mar 2024',
        'link' => BASE_URL . '/artikel/olahraga-rumah'
    ],
    [
        'title' => 'Panduan Praktis Memilih Buah dan Sayur Segar',
        'excerpt' => 'Mengonsumsi buah dan sayur segar adalah kunci diet seimbang. Namun, bagaimana cara memilih yang terbaik di pasar atau supermarket? Simak panduan lengkapnya.',
        'image' => BASE_URL . '/assets/images/menu/3.jpg',
        'category' => 'Tips & Trik',
        'date' => '13 Mar 2024',
        'link' => BASE_URL . '/artikel/tips-buah-sayur'
    ],
    [
        'title' => 'Mengenal Manfaat Probiotik untuk Kesehatan Pencernaan',
        'excerpt' => 'Probiotik adalah bakteri baik yang membantu menjaga kesehatan sistem pencernaan. Pelajari manfaat dan sumber-sumber probiotik alami untuk kesehatan optimal.',
        'image' => BASE_URL . '/assets/images/menu/4.jpg',
        'category' => 'Kesehatan',
        'date' => '12 Mar 2024',
        'link' => BASE_URL . '/artikel/manfaat-probiotik'
    ],
    [
        'title' => 'Tips Menjaga Pola Makan Sehat saat Work From Home',
        'excerpt' => 'Bekerja dari rumah bisa membuat pola makan menjadi tidak teratur. Simak tips praktis untuk menjaga pola makan sehat dan bergizi selama WFH.',
        'image' => BASE_URL . '/assets/images/menu/5.jpg',
        'category' => 'Pola Makan',
        'date' => '11 Mar 2024',
        'link' => BASE_URL . '/artikel/pola-makan-wfh'
    ],
    [
        'title' => 'Resep Smoothie Bowl Sehat dan Bergizi',
        'excerpt' => 'Smoothie bowl tidak hanya Instagram-worthy, tapi juga kaya nutrisi. Coba resep smoothie bowl sehat ini untuk sarapan yang lezat dan bergizi.',
        'image' => BASE_URL . '/assets/images/menu/6.jpg',
        'category' => 'Resep Sehat',
        'date' => '10 Mar 2024',
        'link' => BASE_URL . '/artikel/resep-smoothie-bowl'
    ],
    [
        'title' => 'Pentingnya Hidrasi: Berapa Banyak Air yang Harus Diminum?',
        'excerpt' => 'Air adalah sumber kehidupan. Pelajari mengapa hidrasi penting dan berapa banyak air yang seharusnya Anda minum setiap hari untuk kesehatan optimal.',
        'image' => BASE_URL . '/assets/images/menu/7.jpg',
        'category' => 'Kesehatan',
        'date' => '9 Mar 2024',
        'link' => BASE_URL . '/artikel/pentingnya-hidrasi'
    ],
    [
        'title' => 'Makanan yang Harus Dihindari Penderita Asam Lambung',
        'excerpt' => 'Asam lambung bisa sangat mengganggu aktivitas. Kenali makanan apa saja yang sebaiknya dihindari untuk mencegah kambuhnya gejala asam lambung.',
        'image' => BASE_URL . '/assets/images/menu/8.jpg',
        'category' => 'Tips & Trik',
        'date' => '8 Mar 2024',
        'link' => BASE_URL . '/artikel/makanan-asam-lambung'
    ],
    [
        'title' => 'Manfaat Yoga untuk Kesehatan Mental dan Fisik',
        'excerpt' => 'Yoga tidak hanya baik untuk kesehatan fisik, tapi juga mental. Pelajari bagaimana yoga dapat membantu meredakan stress, anxiety, dan meningkatkan fleksibilitas.',
        'image' => BASE_URL . '/assets/images/menu/9.jpg',
        'category' => 'Olahraga',
        'date' => '7 Mar 2024',
        'link' => BASE_URL . '/artikel/manfaat-yoga'
    ],
    [
        'title' => 'Menu Sehat untuk Diet Rendah Kalori',
        'excerpt' => 'Ingin menurunkan berat badan dengan cara yang sehat? Simak panduan menu diet rendah kalori yang tetap lezat dan bergizi untuk tubuh Anda.',
        'image' => BASE_URL . '/assets/images/menu/10.jpg',
        'category' => 'Gizi',
        'date' => '6 Mar 2024',
        'link' => BASE_URL . '/artikel/menu-diet-sehat'
    ],
    [
        'title' => 'Cara Memasak Sayuran agar Nutrisi Tidak Hilang',
        'excerpt' => 'Teknik memasak yang salah bisa mengurangi kandungan nutrisi sayuran. Pelajari cara memasak sayuran yang tepat untuk mempertahankan nutrisinya.',
        'image' => BASE_URL . '/assets/images/menu/1.jpg',
        'category' => 'Tips & Trik',
        'date' => '5 Mar 2024',
        'link' => BASE_URL . '/artikel/memasak-sayuran'
    ],
    [
        'title' => 'Superfood Indonesia yang Wajib Anda Ketahui',
        'excerpt' => 'Indonesia kaya akan superfood lokal yang tak kalah bergizi dari superfood impor. Kenali berbagai superfood Indonesia yang mudah ditemukan dan harganya terjangkau.',
        'image' => BASE_URL . '/assets/images/menu/2.jpg',
        'category' => 'Gizi',
        'date' => '4 Mar 2024',
        'link' => BASE_URL . '/artikel/superfood-indonesia'
    ]
];

// Calculate pagination
$total_articles = count($all_articles);
$total_pages = ceil($total_articles / $articles_per_page);
$current_articles = array_slice($all_articles, $offset, $articles_per_page);

// Featured articles (first 3 articles)
$featured_articles = array_slice($all_articles, 0, 3);
?>

<div class="article-page-container" style="max-width: 1250px; margin: 40px auto; padding: 0 20px;">
    <header class="article-header" style="text-align: center; margin-bottom: 50px;">
        <h1 style="color: #333; font-size: 2.5rem; margin-bottom: 20px;">Artikel Kesehatan dan Gizi</h1>
        <p style="color: #666; font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
            Temukan informasi terkini seputar kesehatan, gizi, dan pola hidup sehat untuk membantu Anda menjalani hidup yang lebih baik.
        </p>
    </header>

    <div class="article-categories" style="margin-bottom: 40px;">
        <h2 style="color: #333; font-size: 1.5rem; margin-bottom: 20px;">Kategori Artikel</h2>
        <div class="category-tags" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php
            $categories = ['Semua', 'Kesehatan', 'Gizi', 'Pola Makan', 'Olahraga', 'Tips & Trik', 'Resep Sehat'];
            foreach ($categories as $category):
            ?>
            <a href="<?php echo ($category == 'Semua') ? BASE_URL . '/artikel.php' : '#'; ?>" class="category-tag" style="
                text-decoration: none;
                padding: 8px 16px;
                background-color: #f8f9fa;
                color: #333;
                border-radius: 20px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                border: 1px solid #dee2e6;
            "><?php echo $category; ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($current_page == 1): ?>
    <div class="featured-articles" style="margin-bottom: 60px;">
        <h2 style="color: #333; font-size: 1.5rem; margin-bottom: 30px;">
            <i class="fas fa-star" style="color: #ffc107; margin-right: 8px;"></i>
            Artikel Unggulan
        </h2>
        <div class="featured-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <?php foreach ($featured_articles as $article): ?>
            <article class="article-card featured-card" style="
                border: 2px solid #28a745;
                border-radius: 12px;
                overflow: hidden;
                background-color: white;
                box-shadow: 0 6px 16px rgba(36, 255, 7, 0.2);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                position: relative;
            ">
                <div class="featured-badge" style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background-color: #ffc107;
                    color: #333;
                    padding: 4px 12px;
                    border-radius: 15px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    z-index: 2;
                ">
                    <i class="fas fa-star" style="margin-right: 4px;"></i>Unggulan
                </div>
                <a href="<?php echo htmlspecialchars($article['link']); ?>" style="display: block;">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>"
                         alt="<?php echo htmlspecialchars($article['title']); ?>"
                         style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="category" style="
                            background-color: #e9ecef;
                            padding: 4px 12px;
                            border-radius: 15px;
                            font-size: 0.8rem;
                            color: #495057;
                        "><?php echo htmlspecialchars($article['category']); ?></span>
                        <span class="date" style="font-size: 0.8rem; color: #6c757d;">
                            <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                            <?php echo htmlspecialchars($article['date']); ?>
                        </span>
                    </div>
                    <h3 style="margin: 10px 0; font-size: 1.25rem; line-height: 1.4;">
                        <a href="<?php echo htmlspecialchars($article['link']); ?>"
                           style="text-decoration: none; color: #333; transition: color 0.2s ease;">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h3>
                    <p style="color: #555; font-size: 0.9rem; line-height: 1.6; margin-bottom: 15px;">
                        <?php echo htmlspecialchars(substr($article['excerpt'], 0, 120)) . '...'; ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($article['link']); ?>"
                       style="display: inline-block; color: var(--primary-green); text-decoration: none; font-weight: 600;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="latest-articles">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="color: #333; font-size: 1.5rem; margin: 0;">
                <i class="fas fa-newspaper" style="color: var(--primary-green); margin-right: 8px;"></i>
                <?php echo ($current_page == 1) ? 'Artikel Terbaru' : 'Semua Artikel'; ?>
            </h2>
            <div class="article-count" style="color: #6c757d; font-size: 0.9rem;">
                Menampilkan <?php echo $offset + 1; ?>-<?php echo min($offset + $articles_per_page, $total_articles); ?> 
                dari <?php echo $total_articles; ?> artikel
            </div>
        </div>
        
        <div class="articles-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">
            <?php foreach ($current_articles as $article): ?>
            <article class="article-card" style="
                border: 1px solid #ddd;
                border-radius: 12px;
                overflow: hidden;
                background-color: white;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            ">
                <a href="<?php echo htmlspecialchars($article['link']); ?>" style="display: block;">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>"
                         alt="<?php echo htmlspecialchars($article['title']); ?>"
                         style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="category" style="
                            background-color: #e9ecef;
                            padding: 4px 12px;
                            border-radius: 15px;
                            font-size: 0.8rem;
                            color: #495057;
                        "><?php echo htmlspecialchars($article['category']); ?></span>
                        <span class="date" style="font-size: 0.8rem; color: #6c757d;">
                            <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                            <?php echo htmlspecialchars($article['date']); ?>
                        </span>
                    </div>
                    <h3 style="margin: 10px 0; font-size: 1.25rem; line-height: 1.4;">
                        <a href="<?php echo htmlspecialchars($article['link']); ?>"
                           style="text-decoration: none; color: #333; transition: color 0.2s ease;">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h3>
                    <p style="color: #555; font-size: 0.9rem; line-height: 1.6; margin-bottom: 15px;">
                        <?php echo htmlspecialchars(substr($article['excerpt'], 0, 120)) . '...'; ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($article['link']); ?>"
                       style="display: inline-block; color: var(--primary-green); text-decoration: none; font-weight: 600;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 40px;">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn" style="
                    display: flex;
                    align-items: center;
                    padding: 10px 15px;
                    background-color: var(--primary-green);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-size: 0.9rem;
                    transition: background-color 0.3s ease;
                ">
                    <i class="fas fa-chevron-left" style="margin-right: 5px;"></i>
                    Sebelumnya
                </a>
            <?php endif; ?>

            <div class="pagination-numbers" style="display: flex; gap: 5px;">
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if ($start_page > 1) {
                    echo '<a href="?page=1" class="pagination-number">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="pagination-dots">...</span>';
                    }
                }

                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="pagination-number <?php echo ($i == $current_page) ? 'active' : ''; ?>"
                       style="
                           display: flex;
                           align-items: center;
                           justify-content: center;
                           width: 40px;
                           height: 40px;
                           text-decoration: none;
                           border-radius: 8px;
                           font-weight: 600;
                           transition: all 0.3s ease;
                           <?php echo ($i == $current_page) ? 
                               'background-color: var(--primary-green); color: white;' : 
                               'background-color: #f8f9fa; color: #333; border: 1px solid #dee2e6;'; ?>
                       ">
                        <?php echo $i; ?>
                    </a>
                <?php endfor;
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="pagination-dots">...</span>';
                    }
                    echo '<a href="?page=' . $total_pages . '" class="pagination-number">' . $total_pages . '</a>';
                }
                ?>
            </div>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn" style="
                    display: flex;
                    align-items: center;
                    padding: 10px 15px;
                    background-color: var(--primary-green);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-size: 0.9rem;
                    transition: background-color 0.3s ease;
                ">
                    Selanjutnya
                    <i class="fas fa-chevron-right" style="margin-left: 5px;"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="pagination-info" style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 0.9rem;">
            Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.category-tag:hover {
    background-color: var(--primary-green) !important;
    color: white !important;
    border-color: var(--primary-green) !important;
    transform: translateY(-2px);
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.featured-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(255, 193, 7, 0.3);
}

.article-card h3 a:hover {
    color: var(--primary-green) !important;
}

.pagination-btn:hover {
    background-color: #218838 !important;
    transform: translateY(-2px);
}

.pagination-number:hover:not(.active) {
    background-color: var(--primary-green) !important;
    color: white !important;
    transform: translateY(-2px);
}

.pagination-dots {
    display: flex;
    align-items: center;
    padding: 0 10px;
    color: #6c757d;
    font-weight: bold;
}

@media (max-width: 768px) {
    .article-page-container {
        padding: 0 15px;
        margin: 20px auto;
    }
    
    .article-header h1 {
        font-size: 2rem;
    }
    
    .article-header p {
        font-size: 1rem;
    }
    
    .featured-grid,
    .articles-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-tags {
        justify-content: center;
    }
    
    .pagination-container {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .pagination-btn {
        padding: 8px 12px !important;
        font-size: 0.8rem !important;
    }
    
    .pagination-number {
        width: 35px !important;
        height: 35px !important;
        font-size: 0.8rem;
    }
    
    .article-count {
        font-size: 0.8rem;
        text-align: right;
    }
}

@media (max-width: 480px) {
    .article-header {
        margin-bottom: 30px;
    }
    
    .article-header h1 {
        font-size: 1.5rem;
    }
    
    .pagination-numbers {
        gap: 3px;
    }
    
    .pagination-number {
        width: 30px !important;
        height: 30px !important;
        font-size: 0.7rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>