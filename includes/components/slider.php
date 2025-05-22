<?php
// File: wellnessplate/includes/components/slider.php

// Pastikan variabel $slider_banners dan $slider_options tersedia
if (!isset($slider_banners) || !is_array($slider_banners)) {
    echo "<p>Error: Data slider tidak ditemukan.</p>";
    return;
}
if (!isset($slider_options) || !is_array($slider_options)) {
    $slider_options = []; // Default jika tidak ada
}

$num_original_slides = count($slider_banners);

// Jika tidak ada banner, jangan tampilkan slider
if ($num_original_slides === 0) {
    return;
}

$show_captions = $slider_options['show_captions'] ?? true;
// $autoplay_speed = (int)($slider_options['autoplay_speed'] ?? 5000); // Bisa diambil oleh JS jika JS diubah
?>

<section style="margin-top: 20px;" class="custom-slider-section" 
         id="<?php echo $slider_id; ?>" 
         aria-labelledby="<?php echo $slider_id; ?>-title"
         <?php /* data-autoplay-speed="<?php echo $autoplay_speed; ?>" */ // Jika JS mau ambil dari sini ?>
         >
    

    <div class="slider-wrapper" tabindex="0"> <?php // tabindex="0" untuk fokus keyboard ?>
        
        <div class="slider-skeleton">
            <div class="skeleton-item"></div>
        </div>

        <div class="slider-outer-container">
            <div class="slider-inner-container">
                <?php
                // --- LOGIKA CLONING SLIDE UNTUK INFINITE LOOP ---
                // JavaScript mengharapkan: [clone_slide_terakhir, slide1, slide2, ..., slideN, clone_slide_pertama]
                
                // A. CLONE SLIDE TERAKHIR (taruh di awal)
                // Hanya buat clone jika ada lebih dari 1 slide asli, karena infinite loop tidak relevan untuk 1 slide.
                // Namun, JS yang diberikan mungkin lebih stabil jika struktur clone tetap ada walau hanya 1 slide,
                // jadi kita buat clone KECUALI jika num_original_slides = 0 (sudah ditangani di atas).
                // Jika hanya 1 slide, clone akan jadi slide itu sendiri. Navigasi akan disembunyikan.
                
                $last_banner_data = end($slider_banners); // Ambil data slide terakhir
                reset($slider_banners); // Reset pointer array
                ?>
                <div class="slide-item" role="group" aria-roledescription="slide" aria-label="Clone slide terakhir">
                    <img src="<?php echo htmlspecialchars($last_banner_data['image']); ?>" 
                         alt="Clone: <?php echo htmlspecialchars($last_banner_data['alt']); ?>"
                         <?php /* Untuk gambar pertama/clone, jangan lazy load agar aspect ratio bisa cepat dihitung */ ?>
                         >
                    <?php /* Caption pada clone biasanya tidak perlu atau bisa disesuaikan */ ?>
                </div>

                <?php
                // B. SLIDE ASLI
                foreach ($slider_banners as $index => $banner) :
                    $slide_label = "Slide " . ($index + 1) . " dari " . $num_original_slides . ": " . htmlspecialchars($banner['alt']);
                ?>
                    <div class="slide-item" 
                         role="group" 
                         aria-roledescription="slide" 
                         aria-label="<?php echo $slide_label; ?>"
                         id="<?php echo $slider_id . '-slide-' . ($index); // ID untuk aria-controls, 0-indexed ?>">
                        <a href="<?php echo htmlspecialchars($banner['link']); ?>">
                            <?php // Untuk lazy load, ganti src dengan data-src dan src awal adalah placeholder ?>
                            <img src="<?php echo htmlspecialchars($banner['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($banner['alt']); ?>">
                        </a>
                        <?php if ($show_captions && !empty($banner['alt'])) : ?>
                            <div class="slide-item-caption">
                                <h3><?php echo htmlspecialchars($banner['alt']); ?></h3>
                                <?php /* <p>Deskripsi tambahan jika ada</p> */ ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach;

                // C. CLONE SLIDE PERTAMA (taruh di akhir)
                $first_banner_data = reset($slider_banners); // Ambil data slide pertama
                ?>
                 <div class="slide-item" role="group" aria-roledescription="slide" aria-label="Clone slide pertama">
                    <img src="<?php echo htmlspecialchars($first_banner_data['image']); ?>" 
                         alt="Clone: <?php echo htmlspecialchars($first_banner_data['alt']); ?>">
                 </div>
            </div>
        </div>

        <?php // Tampilkan navigasi & indikator HANYA jika ada LEBIH DARI 1 slide asli ?>
        <?php if ($num_original_slides > 1) : ?>
            <button type="button" class="slider-nav-btn prev" aria-label="Slide sebelumnya">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <button type="button" class="slider-nav-btn next" aria-label="Slide berikutnya">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>

            <div class="slider-indicators" role="tablist" aria-label="Pilih slide">
                <?php foreach ($slider_banners as $index => $banner) : ?>
                    <button type="button" 
                            class="indicator-dot <?php /* echo ($index === 0) ? 'active' : ''; */ // JS akan handle kelas 'active' ?>" 
                            data-slide-to="<?php echo $index; // 0-indexed untuk slide asli ?>" 
                            role="tab" 
                            aria-selected="false" <?php // JS akan handle aria-selected ?>
                            aria-controls="<?php echo $slider_id . '-slide-' . $index; ?>"
                            aria-label="Pergi ke slide <?php echo $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>