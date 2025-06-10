<?php
$page_title = "FAQ - WellnessPlate";
require_once('includes/header.php');

// Extended FAQ items for the main page
$faq_items = [
    // Tentang WellnessPlate
    [
        'category' => 'Tentang WellnessPlate',
        'items' => [
            [
                'question' => 'Apa itu WellnessPlate?',
                'answer'   => 'WellnessPlate adalah platform online yang didedikasikan untuk membantu Anda mencapai gaya hidup yang lebih sehat melalui rekomendasi makanan sehat. Kami menyediakan resep bernutrisi yang dapat disesuaikan dengan kondisi kesehatan Anda, tips pola makan sehat, dan panduan gizi yang komprehensif.'
            ],
            [
                'question' => 'Mengapa WellnessPlate berbeda dari platform resep lainnya?',
                'answer'   => 'WellnessPlate unik karena kami menyediakan fitur filter resep berdasarkan kondisi kesehatan pengguna. Ini memungkinkan Anda menemukan resep yang aman dan sesuai dengan kebutuhan diet khusus Anda. Setiap resep juga dilengkapi dengan informasi nutrisi lengkap dan rekomendasi penyesuaian untuk berbagai kondisi kesehatan.'
            ]
        ]
    ],
    // Fitur dan Penggunaan
    [
        'category' => 'Fitur dan Penggunaan',
        'items' => [
            [
                'question' => 'Bagaimana cara menggunakan fitur filter resep berdasarkan kondisi kesehatan?',
                'answer'   => 'Cukup pilih kondisi kesehatan Anda dari daftar yang tersedia di halaman resep. Platform kami akan secara otomatis menampilkan resep-resep yang aman dan sesuai dengan kondisi Anda. Anda juga dapat mengkombinasikan beberapa filter untuk hasil yang lebih spesifik.'
            ],
            [
                'question' => 'Apakah informasi kesehatan yang saya masukkan aman?',
                'answer'   => 'Ya, kami sangat memperhatikan privasi pengguna. Informasi kesehatan yang Anda masukkan hanya digunakan untuk memberikan rekomendasi resep yang sesuai dan tidak akan dibagikan dengan pihak ketiga.'
            ],
            [
                'question' => 'Bagaimana cara menyimpan resep favorit?',
                'answer'   => 'Setelah login, Anda dapat menyimpan resep favorit dengan mengklik ikon bookmark di setiap resep. Resep yang disimpan dapat diakses melalui halaman profil Anda.'
            ]
        ]
    ],
    // Resep dan Nutrisi
    [
        'category' => 'Resep dan Nutrisi',
        'items' => [
            [
                'question' => 'Bagaimana resep-resep ini disesuaikan dengan kondisi kesehatan?',
                'answer'   => 'Setiap resep dikembangkan dengan mempertimbangkan berbagai kondisi kesehatan. Kami bekerja sama dengan ahli gizi untuk memastikan bahwa bahan-bahan dan metode memasak aman untuk kondisi kesehatan tertentu. Setiap resep juga menyertakan alternatif bahan dan penyesuaian untuk berbagai kebutuhan diet.'
            ],
            [
                'question' => 'Apakah resep-resep ini sudah diverifikasi oleh ahli kesehatan?',
                'answer'   => 'Ya, semua resep dan rekomendasi makanan di WellnessPlate telah direview oleh tim ahli gizi dan praktisi kesehatan. Kami memastikan bahwa setiap rekomendasi memenuhi standar gizi dan aman untuk kondisi kesehatan yang ditargetkan.'
            ],
            [
                'question' => 'Bagaimana cara mengetahui nilai gizi dari setiap resep?',
                'answer'   => 'Setiap resep dilengkapi dengan informasi nutrisi lengkap, termasuk kalori, protein, karbohidrat, lemak, serat, dan nutrisi penting lainnya. Anda juga dapat melihat penjelasan tentang manfaat kesehatan dari bahan-bahan utama.'
            ]
        ]
    ],
    // Kondisi Kesehatan Spesifik
    [
        'category' => 'Kondisi Kesehatan Spesifik',
        'items' => [
            [
                'question' => 'Apakah ada resep khusus untuk penderita diabetes?',
                'answer'   => 'Ya, kami memiliki koleksi resep khusus untuk penderita diabetes yang memperhatikan indeks glikemik dan kandungan karbohidrat. Setiap resep menyertakan informasi tentang dampaknya terhadap gula darah.'
            ],
            [
                'question' => 'Bagaimana dengan alergi makanan?',
                'answer'   => 'Sistem filter kami memungkinkan Anda menghindari bahan-bahan yang dapat menyebabkan alergi. Kami juga menyediakan alternatif bahan untuk setiap resep yang mengandung alergen umum.'
            ],
            [
                'question' => 'Apakah ada resep untuk penderita hipertensi?',
                'answer'   => 'Ya, kami memiliki banyak resep rendah sodium yang cocok untuk penderita hipertensi. Setiap resep mencantumkan kandungan sodium dan tips untuk mengurangi penggunaan garam tanpa mengorbankan rasa.'
            ]
        ]
    ],
    // Kontribusi dan Dukungan
    [
        'category' => 'Kontribusi dan Dukungan',
        'items' => [
            [
                'question' => 'Bagaimana jika saya memiliki resep sehat untuk dibagikan?',
                'answer'   => 'Kami menyambut kontribusi resep dari komunitas. Anda dapat mengirimkan resep melalui formulir khusus di website. Tim kami akan mereview dan memverifikasi resep tersebut sebelum dipublikasikan.'
            ],
            [
                'question' => 'Bagaimana cara mendapatkan bantuan jika ada pertanyaan?',
                'answer'   => 'Anda dapat menghubungi tim dukungan kami melalui halaman Kontak atau mengirim email ke support@wellnessplate.com. Kami juga memiliki forum komunitas di mana Anda dapat berbagi pengalaman dan mendapatkan tips dari pengguna lain.'
            ]
        ]
    ]
];
?>

<main class="faq-page">
    <div class="container">
        <h1 class="page-title">Pertanyaan yang Sering Diajukan (FAQ)</h1>
        <p class="page-description">
            Temukan jawaban untuk pertanyaan umum tentang WellnessPlate, fitur-fitur kami, dan panduan penggunaan platform untuk mendapatkan rekomendasi makanan sehat yang sesuai dengan kondisi kesehatan Anda.
        </p>

        <div class="faq-categories">
            <?php foreach ($faq_items as $category) : ?>
                <section class="faq-category">
                    <h2 class="category-title"><?php echo htmlspecialchars($category['category']); ?></h2>
                    <div class="faq-list">
                        <?php foreach ($category['items'] as $index => $item) : ?>
                            <div class="faq-item">
                                <button class="faq-question" 
                                        aria-expanded="false" 
                                        aria-controls="faq-answer-<?php echo $index; ?>">
                                    <span><?php echo htmlspecialchars($item['question']); ?></span>
                                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <div class="faq-answer" 
                                     id="faq-answer-<?php echo $index; ?>" 
                                     role="region" 
                                     aria-hidden="true">
                                    <p><?php echo nl2br(htmlspecialchars($item['answer'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="additional-help">
            <h3>Masih Membutuhkan Bantuan?</h3>
            <p>Jika Anda tidak menemukan jawaban yang Anda cari, jangan ragu untuk menghubungi tim dukungan kami atau mengunjungi halaman bantuan untuk informasi lebih lanjut.</p>
            <div class="help-buttons">
                <a href="/contact" class="btn btn-primary">Hubungi Kami</a>
                <a href="/help" class="btn btn-secondary">Pusat Bantuan</a>
            </div>
        </div>
    </div>
</main>

<style>
/* Reset dan Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    text-decoration: none;
    list-style: none;
}

body {
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
    background: #fff;
}

/* Header Styles */
.site-header-frontend {
    background-color: #fff;
    padding: 0 20px;
    border-bottom: 1px solid var(--border-color, #e0e0e0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.container-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    height: 70px;
}

.logo-frontend a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--primary-green, #4caf50);
}

.logo-frontend img {
    height: 40px;
    margin-right: 10px;
}

.logo-frontend span {
    font-size: 1.5em;
    font-weight: 600;
}

.main-navigation-frontend ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.main-navigation-frontend ul li {
    margin-left: 25px;
}

.main-navigation-frontend ul li a {
    text-decoration: none;
    color: var(--text-color, #333);
    font-weight: 500;
    padding: 10px 5px;
    position: relative;
    transition: color 0.3s ease;
}

.main-navigation-frontend ul li a:hover,
.main-navigation-frontend ul li a.active-nav-link {
    color: var(--primary-green, #4caf50);
}

.main-navigation-frontend ul li a::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--primary-green, #4caf50);
    transition: width 0.3s ease;
}

.main-navigation-frontend ul li a:hover::after,
.main-navigation-frontend ul li a.active-nav-link::after {
    width: 100%;
}

.user-actions-frontend {
    display: flex;
    align-items: center;
}

.user-actions-frontend .welcome-user {
    margin-right: 15px;
    font-size: 0.9em;
    color: #555;
}

.user-actions-frontend .btn-nav-action {
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 20px;
    margin-left: 10px;
    font-size: 0.9em;
    font-weight: 500;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.user-actions-frontend .btn-nav-action:first-child {
    margin-left: 0;
}

.user-actions-frontend a[href*="login"] {
    background-color: transparent;
    color: var(--primary-green, #4caf50);
    border: 1px solid var(--primary-green, #4caf50);
}

.user-actions-frontend a[href*="login"]:hover {
    background-color: var(--primary-green, #4caf50);
    color: #fff;
}

.user-actions-frontend a.btn-register {
    background-color: var(--primary-green, #4caf50);
    color: #fff;
    border: 1px solid var(--primary-green, #4caf50);
}

.user-actions-frontend a.btn-register:hover {
    background-color: var(--dark-green, #388e3c);
    border-color: var(--dark-green, #388e3c);
}

.user-actions-frontend a[href*="logout"] {
    background-color: var(--accent-red, #f44336);
    color: #fff;
    border: 1px solid var(--accent-red, #f44336);
}

.user-actions-frontend a[href*="logout"]:hover {
    background-color: #d32f2f;
    border-color: #d32f2f;
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.8em;
    color: var(--text-color, #333);
    cursor: pointer;
}

.main-content-area-frontend {
    padding-top: 20px;
    min-height: calc(100vh - 70px - 150px);
    max-width: 1300px;
    margin: 0 auto;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 40px;
}

/* Footer Styles */
.site-footer-frontend {
    background-color: var(--sidebar-bg, #343a40);
    color: var(--sidebar-text, #f8f9fa);
    padding: 40px 20px 20px;
    font-size: 0.9em;
}

.container-footer {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 30px;
}

.footer-section {
    flex: 1;
    min-width: 200px;
}

.footer-section h4 {
    color: var(--primary-green, #4caf50);
    margin-bottom: 15px;
    font-size: 1.1em;
}

.footer-section p {
    line-height: 1.7;
    margin-bottom: 10px;
    color: #ccc;
}

.quick-links ul,
.footer-section .social-media-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.quick-links ul li {
    margin-bottom: 8px;
}

.quick-links ul li a {
    text-decoration: none;
    color: #ccc;
    transition: color 0.3s ease;
}

.quick-links ul li a:hover {
    color: #fff;
    text-decoration: underline;
}

.social-media-links {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.social-media-links a img {
    width: 24px;
    height: 24px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.social-media-links a:hover img {
    opacity: 1;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .main-navigation-frontend {
        display: none;
    }

    .container-navbar {
        padding: 0 15px;
    }
}

@media (max-width: 768px) {
    .container-navbar {
        height: 60px;
    }

    .logo-frontend img {
        height: 35px;
    }

    .logo-frontend span {
        font-size: 1.2em;
    }

    .main-navigation-frontend {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .main-navigation-frontend.mobile-menu-active {
        display: block;
        z-index: 1000;
    }

    .main-navigation-frontend ul {
        flex-direction: column;
    }

    .main-navigation-frontend ul li {
        margin: 0;
        padding: 8px 0;
    }

    .main-navigation-frontend ul li:last-child {
        border-bottom: none;
    }

    .main-navigation-frontend ul li a {
        display: block;
    }

    .main-navigation-frontend ul li a::after {
        display: none;
    }

    .user-actions-frontend .welcome-user {
        display: none;
    }

    .user-actions-frontend .btn-nav-action {
        padding: 6px 12px;
    }

    .mobile-menu-toggle {
        display: block;
    }

    .container-footer {
        flex-direction: column;
        gap: 20px;
    }

    .footer-section {
        min-width: 100%;
    }

    .social-media-links {
        justify-content: center;
    }
}

/* FAQ Page Styles */
.faq-page {
    padding: 4rem 0;
    background-color: #f8f9fa;
}

.page-title {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 2.5rem;
}

.page-description {
    text-align: center;
    color: #6c757d;
    max-width: 800px;
    margin: 0 auto 3rem;
    line-height: 1.6;
}

.faq-categories {
    max-width: 900px;
    margin: 0 auto;
}

.faq-category {
    margin-bottom: 3rem;
    background: #fff;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

.category-title {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.faq-question {
    width: 100%;
    text-align: left;
    padding: 1rem;
    background: #fff;
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    color: #2c3e50;
    font-weight: 500;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: #f8f9fa;
}

.faq-question span {
    flex: 1;
    padding-right: 1rem;
}

.faq-icon {
    transition: transform 0.3s ease;
}

.faq-answer {
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    opacity: 0;
    background: #fff;
}

.faq-answer p {
    padding: 1rem;
    margin: 0;
    color: #6c757d;
    line-height: 1.6;
}

.additional-help {
    text-align: center;
    margin-top: 4rem;
    padding: 2rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

.additional-help h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.additional-help p {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.help-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #545b62;
}

@media (max-width: 768px) {
    .faq-page {
        padding: 2rem 0;
    }

    .page-title {
        font-size: 2rem;
    }

    .faq-category {
        padding: 1.5rem;
    }

    .help-buttons {
        flex-direction: column;
    }
}
</style>

<?php
require_once('includes/footer.php');
?>
