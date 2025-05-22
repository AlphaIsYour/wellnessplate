<?php
// File: wellnessplate/includes/components/slider.php

if (!isset($slider_banners) || !is_array($slider_banners) || empty($slider_banners)) {
    // echo "<p>Error: Data slider tidak ditemukan atau kosong.</p>";
    return;
}
if (!isset($slider_options) || !is_array($slider_options)) {
    $slider_options = [];
}

$num_slides = count($slider_banners);
$slider_id = $slider_id ?? 'simple-slider-' . uniqid();
// Default 5 detik sudah di sini
$autoplay_speed = (int)($slider_options['autoplay_speed'] ?? 5000); 
$enable_autoplay = (bool)($slider_options['enable_autoplay'] ?? true); // Default autoplay aktif
$show_captions = $slider_options['show_captions'] ?? true;
?>

<section class="simple-slider-section"
         id="<?php echo htmlspecialchars($slider_id); ?>"
         aria-roledescription="carousel"
         aria-label="Galeri Banner Otomatis"
         data-autoplay="<?php echo $enable_autoplay ? 'true' : 'false'; ?>"
         data-autoplay-speed="<?php echo $autoplay_speed; ?>">

    <div class="simple-slider-wrapper">
        <div class="simple-slider-skeleton">
            <div class="skeleton-item-simple"></div>
        </div>
        <div class="simple-slider-inner">
            <?php foreach ($slider_banners as $index => $banner) : ?>
                <div class="simple-slide-item"
                     role="group"
                     aria-roledescription="slide"
                     aria-label="Slide <?php echo ($index + 1) . ' dari ' . $num_slides . ($banner['alt'] ? ': ' . htmlspecialchars($banner['alt']) : ''); ?>">
                    <a href="<?php echo htmlspecialchars($banner['link'] ?: '#'); ?>"
                       <?php echo ($banner['link'] && $banner['link'] !== '#') ? '' : 'tabindex="-1" style="pointer-events: none;"'; ?>>
                        <img src="<?php echo htmlspecialchars($banner['image']); ?>"
                             alt="<?php echo htmlspecialchars($banner['alt']); ?>"
                             loading="<?php echo ($index === 0) ? 'eager' : 'lazy'; ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>