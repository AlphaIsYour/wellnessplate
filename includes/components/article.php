<?php
?>
<section class="latest-articles-section" style="padding: 0; background-color: #fff;">
    <div class="container" style="max-width: 1250px; margin: auto; padding: 0px;"> <?php ?>
        <h2 style="text-align: center; margin-bottom: 30px; font-size: 2rem; color: #333;">Artikel Terbaru</h2>
        
        <?php
        $articles = [
            [
                'title' => 'Manfaat Sarapan Pagi untuk Produktivitas Optimal', 
                'excerpt' => 'Sarapan sering disebut sebagai waktu makan terpenting, dan bukan tanpa alasan. Memulai hari dengan nutrisi yang tepat...', 
                'image' => BASE_URL . '/assets/images/articles/1.png',
                'link' => BASE_URL . '/artikel/manfaat-sarapan'
            ],
            [
                'title' => '5 Olahraga Ringan yang Efektif Dilakukan di Rumah', 
                'excerpt' => 'Tetap aktif secara fisik tidak harus selalu pergi ke gym. Ada banyak olahraga ringan namun efektif yang bisa Anda lakukan...', 
                'image' => BASE_URL . '/assets/images/articles/2.png',
                'link' => BASE_URL . '/artikel/olahraga-rumah'
            ],
            [
                'title' => 'Panduan Praktis Memilih Buah dan Sayur Segar Berkualitas', 
                'excerpt' => 'Mengonsumsi buah dan sayur segar adalah kunci diet seimbang. Namun, bagaimana cara memilih yang terbaik di pasar atau supermarket?...', 
                'image' => BASE_URL . '/assets/images/articles/1.png', 
                'link' => BASE_URL . '/artikel/tips-buah-sayur'
            ],
        ];

        $display_articles = array_slice($articles, 0, 3); 

        if (count($display_articles) === 3):
        ?>
        <div class="articles-list" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; /* Tambah wrap untuk layar kecil */">
            <?php foreach ($display_articles as $article): ?>
            <div class="article-card" 
                 style="width: 410px; /* Lebar tetap untuk setiap kartu */
                        border: 1px solid #ddd; 
                        border-radius: 12px; /* Sedikit lebih rounded */
                        overflow: hidden; 
                        background-color: white; 
                        box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Shadow lebih lembut */
                        display: flex; /* Untuk layout internal card */
                        flex-direction: column; /* Konten card dari atas ke bawah */
                        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Efek hover */
                        margin-bottom: 20px; /* Tambah margin bawah untuk wrapping di layar kecil */
                        ">
                <a href="<?php echo htmlspecialchars($article['link']); ?>" style="display: block;">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                         style="width: 100%; 
                                height: 200px; 
                                object-fit: cover;
                                border-bottom: 1px solid #eee; /* Garis tipis pemisah */
                                ">
                </a>
                <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;"> <?php  ?>
                    <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 1.25rem; line-height: 1.4;">
                        <a href="<?php echo htmlspecialchars($article['link']); ?>" 
                           style="text-decoration: none; color: #333; transition: color 0.2s ease;">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h3>
                    <p style="color: #555; /* Warna teks lebih lembut */ 
                              font-size: 0.9em; 
                              line-height: 1.6; 
                              margin-bottom: 15px; 
                              flex-grow: 1; /* Dorong link 'Baca Selengkapnya' ke bawah */
                              ">
                        <?php echo htmlspecialchars(substr($article['excerpt'], 0, 120)) . (strlen($article['excerpt']) > 120 ? '...' : ''); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($article['link']); ?>" 
                       style="display: inline-block; 
                              margin-top: auto; /* Dorong ke bawah jika ada ruang */
                              color: #007bff; /* Ganti dengan warna primer Anda */
                              text-decoration: none; 
                              font-weight: 600;
                              transition: color 0.2s ease;">
                        Baca Selengkapnya →
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p style="text-align:center;">Membutuhkan tepat 3 artikel untuk ditampilkan dalam tata letak ini.</p>
        <?php endif; ?>
    </div>
</section>

<style>
.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.article-card h3 a:hover {
    color: #0056b3;
}
.article-card > a:last-of-type:hover {
    color: #0056b3;
}

@media (max-width: 1220px) {
    .articles-list {
        justify-content: center !important;
    }
}
@media (max-width: 800px) { 
    .article-card {
        width: calc(50% - 10px) !important;
    }
}
@media (max-width: 480px) {
    .article-card {
        width: 100% !important;
    }
}
</style>