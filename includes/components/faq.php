<?php
// File: wellnessplate/includes/components/faq.php
// Komponen FAQ sederhana dengan dropdown.

$faq_title = "Pertanyaan yang Sering Diajukan (FAQ)";

// Array untuk data FAQ. Anda bisa menambah atau mengubahnya.
$faq_items = [
    [
        'question' => 'Apa itu WellnessPlate?',
        'answer'   => 'WellnessPlate adalah platform online yang didedikasikan untuk membantu Anda mencapai gaya hidup yang lebih sehat dan seimbang. Kami menyediakan resep bernutrisi, tips kebugaran, artikel kesehatan, dan panduan praktis lainnya.'
    ],
    [
        'question' => 'Apakah konten di WellnessPlate gratis?',
        'answer'   => 'Ya, sebagian besar konten informatif seperti artikel, resep dasar, dan tips umum dapat diakses secara gratis. Kami mungkin menawarkan program atau konten premium di masa mendatang.'
    ],
    [
        'question' => 'Bagaimana cara memulai perjalanan sehat saya dengan WellnessPlate?',
        'answer'   => 'Mulailah dengan menjelajahi kategori resep kami untuk inspirasi makanan sehat. Baca juga artikel kami tentang nutrisi dan kebugaran untuk pemula. Jangan ragu untuk menetapkan tujuan kecil dan konsisten!'
    ],
    [
        'question' => 'Siapa saja yang menulis konten di WellnessPlate?',
        'answer'   => 'Konten kami ditulis dan dikurasi oleh tim yang terdiri dari ahli gizi, pelatih kebugaran, dan penulis kesehatan yang bersemangat untuk berbagi pengetahuan dan pengalaman mereka.'
    ],
    [
        'question' => 'Apakah saya bisa berkontribusi atau mengajukan pertanyaan spesifik?',
        'answer'   => 'Saat ini kami belum membuka kontribusi eksternal secara umum, namun Anda selalu bisa mengirimkan pertanyaan atau masukan melalui halaman kontak kami. Kami senang mendengar dari Anda!'
    ]
];

// Generate unique ID untuk section FAQ agar JS bisa lebih spesifik jika ada >1 FAQ section
$faq_section_id = 'faq-section-' . uniqid();
?>

<section id="" class="faq-section">
    <div class="container">
        <h2 class="faq-main-title"><?php echo htmlspecialchars($faq_title); ?></h2>

        <?php if (!empty($faq_items)) : ?>
            <div class="faq-list">
                <?php foreach ($faq_items as $index => $item) : ?>
                    <div class="faq-item">
                        <button class="faq-question" 
                                aria-expanded="false" 
                                aria-controls="faq-answer-<?php echo $index . '-' . substr($faq_section_id, -5); ?>">
                            <span><?php echo htmlspecialchars($item['question']); ?></span>
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer" 
                             id="faq-answer-<?php echo $index . '-' . substr($faq_section_id, -5); ?>" 
                             role="region" 
                             aria-hidden="true">
                            <p><?php echo nl2br(htmlspecialchars($item['answer'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>Belum ada pertanyaan yang sering diajukan saat ini.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// Kita akan tambahkan JavaScript langsung di sini untuk kemudahan.
// Idealnya, ini akan ada di file JS terpisah dan di-enqueue.
// Memastikan skrip hanya dimuat sekali jika komponen dipanggil berkali-kali (meskipun tidak ideal)
if (!defined('FAQ_SCRIPT_INCLUDED')) {
    define('FAQ_SCRIPT_INCLUDED', true);
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cari semua section FAQ yang mungkin ada di halaman
    const faqSections = document.querySelectorAll('.faq-section');

    faqSections.forEach(faqSection => {
        const faqItems = faqSection.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const questionButton = item.querySelector('.faq-question');
            const answerPanel = item.querySelector('.faq-answer');
            const icon = questionButton.querySelector('.faq-icon');

            questionButton.addEventListener('click', () => {
                const isExpanded = questionButton.getAttribute('aria-expanded') === 'true';

                questionButton.setAttribute('aria-expanded', !isExpanded);
                answerPanel.setAttribute('aria-hidden', isExpanded); // Jika tadinya true (expanded), maka jadi hidden (collapsed)

                if (!isExpanded) {
                    // Expand: set max-height agar transisi CSS bekerja
                    answerPanel.style.maxHeight = answerPanel.scrollHeight + 'px';
                    answerPanel.style.opacity = '1';
                    icon.style.transform = 'rotate(180deg)';
                    item.classList.add('active');
                } else {
                    // Collapse
                    answerPanel.style.maxHeight = '0';
                    answerPanel.style.opacity = '0';
                    icon.style.transform = 'rotate(0deg)';
                    item.classList.remove('active');
                }
            });

            // Set initial state for collapsed answers to have 0 opacity and maxHeight
            // This ensures the transition works on first click as well
            if (questionButton.getAttribute('aria-expanded') === 'false') {
                answerPanel.style.maxHeight = '0';
                answerPanel.style.opacity = '0';
            }
        });
    });
});
</script>
<?php
} // End if (!defined('FAQ_SCRIPT_INCLUDED'))
?>