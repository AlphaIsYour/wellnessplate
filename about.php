<?php
$page_title = "Tentang Kami - WellnessPlate";
require_once('includes/header.php');
?>

<style>
/* Reset dan Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

/* Header Styles - Same as FAQ page */
/* ... existing header styles ... */

/* About Page Specific Styles */
.about-hero {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.2) 100%);
    padding: 80px 0;
    text-align: center;
    margin-bottom: 60px;
}

.about-hero h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 20px;
    font-weight: 600;
}

.about-hero p {
    font-size: 1.1rem;
    color: #666;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

.about-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.mission-vision {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 40px;
    margin-bottom: 60px;
}

.mission-card, .vision-card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.mission-card:hover, .vision-card:hover {
    transform: translateY(-5px);
}

.card-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-green, #4caf50);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.card-icon i {
    font-size: 30px;
    color: #fff;
}

.mission-card h2, .vision-card h2 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.mission-card p, .vision-card p {
    color: #666;
    line-height: 1.6;
}

.features-section {
    margin-bottom: 60px;
}

.section-title {
    text-align: center;
    margin-bottom: 40px;
}

.section-title h2 {
    color: #2c3e50;
    font-size: 2rem;
    margin-bottom: 15px;
}

.section-title p {
    color: #666;
    max-width: 700px;
    margin: 0 auto;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-bottom: 40px;
}

.feature-card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    color: var(--primary-green, #4caf50);
    font-size: 2rem;
    margin-bottom: 15px;
}

.feature-card h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.feature-card p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}

.team-section {
    margin-bottom: 60px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.team-member {
    text-align: center;
}

.team-member img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin-bottom: 15px;
    object-fit: cover;
    border: 3px solid var(--primary-green, #4caf50);
}

.team-member h3 {
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.team-member p {
    color: #666;
    font-size: 0.9rem;
}

.cta-section {
    background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
    padding: 60px 0;
    text-align: center;
    color: #fff;
    margin-bottom: 60px;
}

.cta-content {
    max-width: 700px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 20px;
}

.cta-content p {
    margin-bottom: 30px;
    font-size: 1.1rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.cta-btn {
    padding: 12px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.cta-btn.primary {
    background: #fff;
    color: var(--primary-green, #4caf50);
}

.cta-btn.primary:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
}

.cta-btn.secondary {
    background: transparent;
    border: 2px solid #fff;
    color: #fff;
}

.cta-btn.secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

@media (max-width: 992px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .about-hero {
        padding: 60px 0;
    }

    .about-hero h1 {
        font-size: 2rem;
    }

    .mission-vision {
        grid-template-columns: 1fr;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .team-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin: 0 auto;
    }

    .cta-buttons {
        flex-direction: column;
        max-width: 300px;
        margin: 0 auto;
    }
}
</style>

<main class="about-page">
    <section class="about-hero">
        <div class="about-content">
            <h1>Selamat Datang di WellnessPlate</h1>
            <p>Platform inovatif yang menghubungkan Anda dengan resep makanan sehat yang disesuaikan dengan kondisi kesehatan Anda. Kami berkomitmen untuk membantu Anda menjalani gaya hidup sehat melalui pilihan makanan yang tepat.</p>
        </div>
    </section>

    <div class="about-content">
        <section class="mission-vision">
            <div class="mission-card">
                <div class="card-icon">
                    <i class='bx bx-target-lock'></i>
                </div>
                <h2>Misi Kami</h2>
                <p>Menyediakan akses mudah ke resep makanan sehat yang disesuaikan dengan kebutuhan kesehatan setiap individu. Kami percaya bahwa makanan yang tepat adalah kunci menuju kesehatan yang optimal.</p>
            </div>
            <div class="vision-card">
                <div class="card-icon">
                    <i class='bx bx-bulb'></i>
                </div>
                <h2>Visi Kami</h2>
                <p>Menjadi platform terdepan dalam memberikan solusi makanan sehat yang personal, membantu jutaan orang mencapai kesehatan optimal melalui pilihan makanan yang tepat dan berkelanjutan.</p>
            </div>
        </section>

        <section class="features-section">
            <div class="section-title">
                <h2>Fitur Unggulan Kami</h2>
                <p>Temukan berbagai fitur yang dirancang khusus untuk membantu Anda menjalani pola makan sehat sesuai dengan kebutuhan Anda.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class='bx bx-filter-alt'></i>
                    </div>
                    <h3>Filter Resep Kesehatan</h3>
                    <p>Temukan resep yang sesuai dengan kondisi kesehatan Anda melalui sistem filter canggih kami.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class='bx bx-book-content'></i>
                    </div>
                    <h3>Panduan Nutrisi</h3>
                    <p>Informasi nutrisi lengkap untuk setiap resep, membantu Anda membuat keputusan makan yang tepat.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class='bx bx-customize'></i>
                    </div>
                    <h3>Personalisasi Menu</h3>
                    <p>Sesuaikan menu makanan Anda berdasarkan preferensi dan kebutuhan diet khusus.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class='bx bx-user-voice'></i>
                    </div>
                    <h3>Komunitas Sehat</h3>
                    <p>Bergabung dengan komunitas yang mendukung dan berbagi pengalaman dalam perjalanan sehat Anda.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class='bx bx-mobile-alt'></i>
                    </div>
                    <h3>Akses Mudah</h3>
                    <p>Akses resep dan informasi kesehatan kapan saja dan di mana saja melalui platform yang responsif.</p>
                </div>
            </div>
        </section>

        <section class="team-section">
            <div class="section-title">
                <h2>Tim Kami</h2>
                <p>Dipimpin oleh para profesional yang berdedikasi untuk membantu Anda mencapai gaya hidup sehat.</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <img src="assets/images/team/alpha.jpg" alt="Dr. Sarah">
                    <h3>Alphareno YS.</h3>
                    <p>Ahli Gizi Senior</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team/habib.png" alt="Chef Michael">
                    <h3>M. Habib Masyhur</h3>
                    <p>Kepala Chef</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team/derby.png" alt="Anna">
                    <h3>M. Derby Junio</h3>
                    <p>Health Coach</p>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>Mulai Perjalanan Sehat Anda</h2>
                <p>Bergabunglah dengan WellnessPlate dan temukan resep sehat yang sesuai dengan kebutuhan Anda.</p>
                <div class="cta-buttons">
                    <a href="/pages/auth/index.php?form=register" class="cta-btn primary">Daftar Sekarang</a>
                    <a href="/pages/search.php" class="cta-btn secondary">Jelajahi Resep</a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php require_once('includes/footer.php'); ?> 