<?php
// File: wellnessplate/includes/components/about-elegant.php

$site_name = "WellnessPlate";
$main_heading = "Tentang Kami: Filosofi Sehat <span class='text-gradient'>WellnessPlate</span>";
$tagline = "Menemukan keseimbangan, satu langkah setiap hari. Kami hadir untuk memandu perjalanan Anda.";
$intro_paragraph = "Di " . htmlspecialchars($site_name) . ", kami percaya bahwa kesehatan sejati adalah perjalanan holistik yang mencakup nutrisi seimbang, aktivitas fisik yang menyenangkan, dan mental yang positif. Misi kami adalah menyediakan platform yang inspiratif dan informatif untuk mendukung Anda mencapai versi terbaik dari diri Anda.";

$key_features = [
    [
        'icon_svg_code' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="36" height="36"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.62c.08.33.13.66.13 1s-.05.67-.13 1H13v-2zm0 3h5.92c-.2.35-.43.69-.68 1H13v-1zm0 3h2.87c-.87.48-1.84.8-2.87.93V16z"></path></svg>', // Contoh ikon Globe (sumber inspirasi global)
        'title' => 'Inspirasi Global, Resep Lokal',
        'description' => 'Kurasi resep sehat dari seluruh dunia yang disesuaikan dengan bahan-bahan lokal yang mudah ditemukan.'
    ],
    [
        'icon_svg_code' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="36" height="36"><path d="M12 6c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm0-12c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z"></path></svg>', // Contoh ikon Target (panduan personal)
        'title' => 'Panduan Personal',
        'description' => 'Tips dan program yang dapat disesuaikan untuk membantu Anda mencapai tujuan kesehatan pribadi Anda.'
    ],
    [
        'icon_svg_code' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="36" height="36"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>', // Contoh ikon Komunitas
        'title' => 'Komunitas Suportif',
        'description' => 'Bergabunglah dengan komunitas yang positif dan saling mendukung dalam perjalanan kesehatan bersama.'
    ]
];

$call_to_action_text = "Pelajari Kisah Kami Lebih Dalam";
$call_to_action_link = "#"; // Ganti dengan URL halaman "Tentang Kami" yang lebih detail

?>

<section id="about-us-elegant" class="about-us-elegant-section">
    <div class="container">
        <div class="about-header">
            <h2 class="section-heading"><?php echo $main_heading; ?></h2>
            <p class="section-tagline"><?php echo htmlspecialchars($tagline); ?></p>
        </div>

        <div class="about-intro">
            <p><?php echo nl2br(htmlspecialchars($intro_paragraph)); ?></p>
        </div>

        <?php if (!empty($key_features)) : ?>
        <div class="key-features-grid">
            <?php foreach ($key_features as $feature) : ?>
            <div class="feature-item">
                <div class="feature-icon">
                    <?php echo $feature['icon_svg_code']; // Output SVG code directly ?>
                </div>
                <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                <p class="feature-description"><?php echo htmlspecialchars($feature['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($call_to_action_link) && $call_to_action_link !== '#' && !empty($call_to_action_text)) : ?>
        <div class="about-cta">
            <a href="" class="btn btn-outline-primary btn-rounded">
                <?php echo htmlspecialchars($call_to_action_text); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>