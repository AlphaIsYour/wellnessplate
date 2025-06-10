<?php
$page_title = "Kebijakan Privasi - WellnessPlate";
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

/* Privacy Policy Page Styles */
.privacy-policy-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.privacy-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 20px;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.2) 100%);
    border-radius: 10px;
}

.privacy-header h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 20px;
}

.privacy-header p {
    color: #666;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

.last-updated {
    font-size: 0.9rem;
    color: #666;
    text-align: center;
    margin-top: 10px;
}

.privacy-content {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.privacy-section {
    margin-bottom: 40px;
}

.privacy-section:last-child {
    margin-bottom: 0;
}

.privacy-section h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-green, #4caf50);
}

.privacy-section h3 {
    color: #34495e;
    font-size: 1.3rem;
    margin: 25px 0 15px;
}

.privacy-section p {
    color: #666;
    line-height: 1.7;
    margin-bottom: 15px;
}

.privacy-section ul {
    list-style-type: disc;
    margin: 15px 0;
    padding-left: 40px;
}

.privacy-section ul li {
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
}

.privacy-section .highlight-box {
    background: #f8f9fa;
    padding: 20px;
    border-left: 4px solid var(--primary-green, #4caf50);
    margin: 20px 0;
    border-radius: 0 5px 5px 0;
}

.privacy-section .contact-info {
    background: #e8f5e9;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
}

.privacy-section .contact-info p {
    margin: 5px 0;
}

@media (max-width: 768px) {
    .privacy-header h1 {
        font-size: 2rem;
    }

    .privacy-content {
        padding: 20px;
    }

    .privacy-section h2 {
        font-size: 1.5rem;
    }

    .privacy-section h3 {
        font-size: 1.2rem;
    }
}
</style>

<main class="privacy-policy-page">
    <div class="privacy-header">
        <h1>Kebijakan Privasi</h1>
        <p>Kami menghargai privasi Anda. Kebijakan ini menjelaskan bagaimana WellnessPlate mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p>
        <div class="last-updated">Terakhir diperbarui: <?php echo date('d F Y'); ?></div>
    </div>

    <div class="privacy-content">
        <section class="privacy-section">
            <h2>1. Pendahuluan</h2>
            <p>WellnessPlate ("kami", "kita", atau "platform kami") berkomitmen untuk melindungi privasi pengguna kami. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi pribadi Anda saat Anda menggunakan layanan kami.</p>
            <div class="highlight-box">
                <p>Dengan menggunakan platform WellnessPlate, Anda menyetujui praktik yang dijelaskan dalam kebijakan privasi ini.</p>
            </div>
        </section>

        <section class="privacy-section">
            <h2>2. Informasi yang Kami Kumpulkan</h2>
            
            <h3>2.1 Informasi yang Anda Berikan</h3>
            <ul>
                <li>Informasi profil (nama, alamat email, nomor telepon)</li>
                <li>Informasi kesehatan (kondisi medis, alergi, preferensi diet)</li>
                <li>Riwayat pencarian dan preferensi resep</li>
                <li>Umpan balik dan ulasan</li>
                <li>Informasi yang Anda bagikan di forum komunitas</li>
            </ul>

            <h3>2.2 Informasi yang Dikumpulkan Secara Otomatis</h3>
            <ul>
                <li>Data penggunaan platform</li>
                <li>Informasi perangkat dan browser</li>
                <li>Alamat IP dan lokasi geografis</li>
                <li>Cookie dan teknologi pelacakan serupa</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>3. Penggunaan Informasi</h2>
            <p>Kami menggunakan informasi yang dikumpulkan untuk:</p>
            <ul>
                <li>Menyediakan dan memersonalisasi layanan kami</li>
                <li>Merekomendasikan resep yang sesuai dengan kondisi kesehatan Anda</li>
                <li>Meningkatkan dan mengembangkan platform</li>
                <li>Berkomunikasi dengan Anda tentang layanan kami</li>
                <li>Mengirim pembaruan dan informasi penting</li>
                <li>Memastikan keamanan platform</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>4. Berbagi Informasi</h2>
            <p>Kami tidak menjual informasi pribadi Anda kepada pihak ketiga. Namun, kami mungkin membagikan informasi dengan:</p>
            <ul>
                <li>Penyedia layanan yang membantu operasional platform</li>
                <li>Ahli gizi dan profesional kesehatan (dengan persetujuan Anda)</li>
                <li>Otoritas hukum jika diwajibkan oleh hukum</li>
            </ul>
            <div class="highlight-box">
                <p>Kami mengambil langkah-langkah untuk memastikan bahwa setiap pihak ketiga yang memiliki akses ke data Anda mematuhi standar privasi dan keamanan yang ketat.</p>
            </div>
        </section>

        <section class="privacy-section">
            <h2>5. Keamanan Data</h2>
            <p>Kami menerapkan langkah-langkah keamanan yang ketat untuk melindungi informasi Anda, termasuk:</p>
            <ul>
                <li>Enkripsi data end-to-end</li>
                <li>Akses terbatas ke data sensitif</li>
                <li>Pemantauan keamanan regular</li>
                <li>Protokol keamanan terkini</li>
                <li>Backup data berkala</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>6. Hak Pengguna</h2>
            <p>Anda memiliki hak untuk:</p>
            <ul>
                <li>Mengakses informasi pribadi Anda</li>
                <li>Memperbarui atau mengoreksi data Anda</li>
                <li>Meminta penghapusan data Anda</li>
                <li>Membatasi penggunaan data Anda</li>
                <li>Menarik persetujuan penggunaan data</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>7. Cookie dan Teknologi Pelacakan</h2>
            <p>Kami menggunakan cookie dan teknologi serupa untuk:</p>
            <ul>
                <li>Mengingat preferensi Anda</li>
                <li>Menganalisis penggunaan platform</li>
                <li>Menyediakan fitur personalisasi</li>
                <li>Meningkatkan performa website</li>
            </ul>
            <p>Anda dapat mengatur browser Anda untuk menolak cookie, namun ini mungkin memengaruhi fungsionalitas platform.</p>
        </section>

        <section class="privacy-section">
            <h2>8. Privasi Anak-anak</h2>
            <p>Platform kami tidak ditujukan untuk anak-anak di bawah 13 tahun. Kami tidak secara sengaja mengumpulkan informasi dari anak-anak. Jika Anda mengetahui bahwa anak di bawah 13 tahun telah memberikan informasi pribadi kepada kami, harap hubungi kami.</p>
        </section>

        <section class="privacy-section">
            <h2>9. Perubahan Kebijakan Privasi</h2>
            <p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan melalui email atau pemberitahuan di platform. Penggunaan berkelanjutan atas layanan kami setelah perubahan tersebut merupakan persetujuan Anda terhadap kebijakan yang diperbarui.</p>
        </section>

        <section class="privacy-section">
            <h2>10. Hubungi Kami</h2>
            <div class="contact-info">
                <p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini atau praktik data kami, silakan hubungi kami di:</p>
                <p>Email: privacy@wellnessplate.com</p>
                <p>Telepon: (021) 1234-5678</p>
                <p>Alamat: Jl. Sehat Sejahtera No. 123, Jakarta 12345</p>
            </div>
        </section>
    </div>
</main>

<?php require_once('includes/footer.php'); ?> 