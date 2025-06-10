<?php
$page_title = "Syarat dan Ketentuan - WellnessPlate";
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

/* Terms Page Styles */
.terms-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.terms-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 20px;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.2) 100%);
    border-radius: 10px;
}

.terms-header h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 20px;
}

.terms-header p {
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

.terms-content {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.terms-section {
    margin-bottom: 40px;
}

.terms-section:last-child {
    margin-bottom: 0;
}

.terms-section h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-green, #4caf50);
}

.terms-section h3 {
    color: #34495e;
    font-size: 1.3rem;
    margin: 25px 0 15px;
}

.terms-section p {
    color: #666;
    line-height: 1.7;
    margin-bottom: 15px;
}

.terms-section ul, .terms-section ol {
    list-style-type: disc;
    margin: 15px 0;
    padding-left: 40px;
}

.terms-section ol {
    list-style-type: decimal;
}

.terms-section ul li, .terms-section ol li {
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
}

.terms-section .highlight-box {
    background: #f8f9fa;
    padding: 20px;
    border-left: 4px solid var(--primary-green, #4caf50);
    margin: 20px 0;
    border-radius: 0 5px 5px 0;
}

.terms-section .warning-box {
    background: #fff3e0;
    padding: 20px;
    border-left: 4px solid #ff9800;
    margin: 20px 0;
    border-radius: 0 5px 5px 0;
}

.terms-section .contact-info {
    background: #e8f5e9;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
}

.terms-section .contact-info p {
    margin: 5px 0;
}

@media (max-width: 768px) {
    .terms-header h1 {
        font-size: 2rem;
    }

    .terms-content {
        padding: 20px;
    }

    .terms-section h2 {
        font-size: 1.5rem;
    }

    .terms-section h3 {
        font-size: 1.2rem;
    }
}
</style>

<main class="terms-page">
    <div class="terms-header">
        <h1>Syarat dan Ketentuan</h1>
        <p>Bacalah dengan seksama syarat dan ketentuan berikut sebelum menggunakan layanan WellnessPlate.</p>
        <div class="last-updated">Terakhir diperbarui: <?php echo date('d F Y'); ?></div>
    </div>

    <div class="terms-content">
        <section class="terms-section">
            <h2>1. Penerimaan Syarat</h2>
            <p>Dengan mengakses dan menggunakan platform WellnessPlate ("Platform"), Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak setuju dengan bagian apapun dari syarat ini, Anda tidak diperkenankan untuk menggunakan Platform kami.</p>
            <div class="highlight-box">
                <p>Syarat dan ketentuan ini merupakan perjanjian yang mengikat secara hukum antara Anda dan WellnessPlate.</p>
            </div>
        </section>

        <section class="terms-section">
            <h2>2. Definisi</h2>
            <ul>
                <li><strong>"Platform"</strong> mengacu pada website WellnessPlate dan seluruh layanannya</li>
                <li><strong>"Pengguna"</strong> adalah setiap individu yang mengakses atau menggunakan Platform</li>
                <li><strong>"Konten"</strong> mencakup semua informasi, teks, gambar, video, dan materi lain yang tersedia di Platform</li>
                <li><strong>"Resep"</strong> adalah panduan memasak dan informasi nutrisi yang disediakan di Platform</li>
            </ul>
        </section>

        <section class="terms-section">
            <h2>3. Penggunaan Platform</h2>
            
            <h3>3.1 Persyaratan Pengguna</h3>
            <ul>
                <li>Anda harus berusia minimal 13 tahun</li>
                <li>Anda harus memberikan informasi yang akurat dan lengkap saat mendaftar</li>
                <li>Anda bertanggung jawab untuk menjaga kerahasiaan akun Anda</li>
                <li>Anda tidak boleh menggunakan Platform untuk tujuan ilegal</li>
            </ul>

            <h3>3.2 Larangan</h3>
            <div class="warning-box">
                <p>Anda dilarang untuk:</p>
                <ul>
                    <li>Menyalahgunakan atau memanipulasi Platform</li>
                    <li>Mengunggah konten yang melanggar hukum atau tidak pantas</li>
                    <li>Melakukan tindakan yang dapat membahayakan Platform atau penggunanya</li>
                    <li>Menggunakan data pengguna lain tanpa izin</li>
                </ul>
            </div>
        </section>

        <section class="terms-section">
            <h2>4. Konten dan Hak Kekayaan Intelektual</h2>
            <p>Semua konten di Platform WellnessPlate dilindungi oleh hak cipta dan hak kekayaan intelektual lainnya.</p>
            
            <h3>4.1 Hak Platform</h3>
            <ul>
                <li>Seluruh konten adalah milik WellnessPlate atau pemberi lisensinya</li>
                <li>Logo, merek dagang, dan desain adalah hak milik WellnessPlate</li>
                <li>Penggunaan konten tanpa izin tertulis dilarang</li>
            </ul>

            <h3>4.2 Konten Pengguna</h3>
            <ul>
                <li>Anda mempertahankan hak atas konten yang Anda unggah</li>
                <li>Anda memberikan lisensi non-eksklusif kepada WellnessPlate untuk menggunakan konten Anda</li>
                <li>Anda bertanggung jawab atas konten yang Anda bagikan</li>
            </ul>
        </section>

        <section class="terms-section">
            <h2>5. Informasi Kesehatan dan Penafian</h2>
            <div class="warning-box">
                <p>Penting untuk diperhatikan:</p>
                <ul>
                    <li>Informasi di Platform bersifat edukatif dan informatif</li>
                    <li>Bukan pengganti konsultasi medis profesional</li>
                    <li>Konsultasikan dengan profesional kesehatan sebelum memulai program diet baru</li>
                </ul>
            </div>
        </section>

        <section class="terms-section">
            <h2>6. Pembayaran dan Langganan</h2>
            <h3>6.1 Layanan Berbayar</h3>
            <ul>
                <li>Beberapa fitur mungkin memerlukan pembayaran</li>
                <li>Harga dan ketentuan pembayaran akan dijelaskan sebelum pembelian</li>
                <li>Pembayaran diproses melalui penyedia layanan pembayaran yang aman</li>
            </ul>

            <h3>6.2 Pembatalan dan Pengembalian Dana</h3>
            <ul>
                <li>Kebijakan pembatalan berlaku sesuai jenis langganan</li>
                <li>Pengembalian dana sesuai dengan ketentuan yang berlaku</li>
            </ul>
        </section>

        <section class="terms-section">
            <h2>7. Batasan Tanggung Jawab</h2>
            <div class="highlight-box">
                <p>WellnessPlate tidak bertanggung jawab atas:</p>
                <ul>
                    <li>Kerugian yang timbul dari penggunaan Platform</li>
                    <li>Keakuratan informasi yang diberikan oleh pengguna</li>
                    <li>Gangguan teknis atau ketidaktersediaan layanan</li>
                    <li>Hasil dari penggunaan resep atau saran diet</li>
                </ul>
            </div>
        </section>

        <section class="terms-section">
            <h2>8. Perubahan dan Penghentian</h2>
            <ul>
                <li>Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu</li>
                <li>Perubahan akan diumumkan melalui Platform</li>
                <li>Kami dapat menghentikan atau membatasi akses ke Platform</li>
            </ul>
        </section>

        <section class="terms-section">
            <h2>9. Hukum yang Berlaku</h2>
            <p>Syarat dan ketentuan ini tunduk pada hukum Republik Indonesia. Setiap perselisihan akan diselesaikan melalui forum yang berwenang di Jakarta, Indonesia.</p>
        </section>

        <section class="terms-section">
            <h2>10. Hubungi Kami</h2>
            <div class="contact-info">
                <p>Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini, silakan hubungi kami di:</p>
                <p>Email: legal@wellnessplate.com</p>
                <p>Telepon: (021) 1234-5678</p>
                <p>Alamat: Jl. Sehat Sejahtera No. 123, Jakarta 12345</p>
            </div>
        </section>
    </div>
</main>

<?php require_once('includes/footer.php'); ?>
