// assets/js/main_script.js

document.addEventListener("DOMContentLoaded", function () {
  // console.log("DOM Loaded. Initializing sliders...");
  initializeSliders();

  function initializeSliders() {
    const sliders = document.querySelectorAll(".custom-slider-section");
    // console.log(`Found ${sliders.length} slider sections.`);
    sliders.forEach((sliderSection, i) => {
      const sliderWrapper = sliderSection.querySelector(".slider-wrapper");
      if (sliderWrapper) {
        // console.log(`Initializing slider ${i + 1} (ID: ${sliderSection.id})`);
        initializeSlider(sliderWrapper, sliderSection.id);
      } else {
        // console.warn(`Slider wrapper not found for section ${sliderSection.id}`);
      }
    });
  }

  function initializeSlider(sliderWrapper, sliderId = "unknown-slider") {
    const sliderInnerContainer = sliderWrapper.querySelector(
      ".slider-inner-container"
    );
    // Slide items sekarang termasuk clone di awal dan akhir
    const slideItems = sliderWrapper.querySelectorAll(".slide-item");
    const prevButton = sliderWrapper.querySelector(".slider-nav-btn.prev");
    const nextButton = sliderWrapper.querySelector(".slider-nav-btn.next");
    const indicatorsContainer =
      sliderWrapper.querySelector(".slider-indicators");
    // Indicator dots adalah untuk slide ASLI
    const indicatorDots = indicatorsContainer
      ? indicatorsContainer.querySelectorAll(".indicator-dot")
      : [];
    const skeletonLoader = sliderWrapper.querySelector(".slider-skeleton");

    const numOriginalSlides = indicatorDots.length;
    // console.log(`[${sliderId}] Original slides (based on indicators): ${numOriginalSlides}`);
    // console.log(`[${sliderId}] Total slide items (incl. clones): ${slideItems.length}`);

    // Guard clause: Jika tidak ada slide asli ATAU jika struktur slide item tidak benar (minimal 1 asli + 2 clone jika ada indikator)
    if (numOriginalSlides === 0 && slideItems.length <= 2) {
      // Jika hanya 1 slide asli, numOriginalSlides akan 0 jika indikator disembunyikan
      // Jika tidak ada indikator (kasus 1 slide), kita bisa pakai jumlah slide item - 2 (clone)
      const actualOriginalSlides =
        slideItems.length > 2 ? slideItems.length - 2 : slideItems.length;
      if (actualOriginalSlides <= 0) {
        // console.warn(`[${sliderId}] No original slides found. Aborting initialization.`);
        if (skeletonLoader) skeletonLoader.style.display = "none";
        sliderWrapper.style.display = "none"; // Sembunyikan slider jika tidak ada konten
        return;
      }
      // Jika hanya 1 slide asli (slideItems.length akan 3: clone, asli, clone)
      // atau jika hanya 1 slide dan PHP tidak membuat clone (slideItems.length 1)
      if (actualOriginalSlides === 1) {
        // console.log(`[${sliderId}] Single slide detected. Basic setup.`);
        if (skeletonLoader) skeletonLoader.style.display = "none";
        sliderInnerContainer.style.transform = `translateX(-100%)`; // Tampilkan slide asli (indeks 1)
        slideItems.forEach((item, i) =>
          item.setAttribute("aria-hidden", i !== 1)
        );
        if (slideItems[1] && slideItems[1].querySelector("a"))
          slideItems[1].querySelector("a").setAttribute("tabindex", "0");
        // Sembunyikan navigasi jika ada (seharusnya sudah dihandle PHP)
        if (prevButton) prevButton.style.display = "none";
        if (nextButton) nextButton.style.display = "none";
        if (indicatorsContainer) indicatorsContainer.style.display = "none";
        // Tidak perlu autoplay, infinite loop, dll.
        setSliderHeight(); // Tetap atur tinggi
        return;
      }
    }

    // Jika numOriginalSlides dari indicatorDots.length adalah 0, tapi slideItems.length > 0,
    // ini bisa berarti hanya ada satu slide (jadi tidak ada indikator).
    // PHP sekarang membuat clone bahkan untuk 1 slide (jadi total 3 slide item).
    // Navigasi dan indikator disembunyikan oleh PHP.
    if (numOriginalSlides === 0 && slideItems.length === 3) {
      // 1 slide asli + 2 clone
      // console.log(`[${sliderId}] Detected 1 original slide with clones. Setting up for single display.`);
      if (skeletonLoader) skeletonLoader.style.display = "none"; // Sembunyikan loader
      // Tampilkan slide asli (yang ada di index 1 setelah clone pertama)
      sliderInnerContainer.style.transform = `translateX(-100%)`;
      slideItems.forEach((item, i) =>
        item.setAttribute("aria-hidden", i !== 1)
      );
      if (slideItems.length > 1 && slideItems[1].querySelector("a")) {
        slideItems[1].querySelector("a").setAttribute("tabindex", "0");
      }
      // Fungsi setSliderHeight mungkin masih relevan
      calculateAspectRatio()
        .then(() => {
          setSliderHeight();
          if (skeletonLoader) skeletonLoader.style.display = "none";
        })
        .catch((err) => {
          // console.error(`[${sliderId}] Error calculating aspect ratio for single slide:`, err);
          if (skeletonLoader) skeletonLoader.style.display = "none";
        });
      return; // Tidak perlu fungsionalitas slider penuh
    }

    if (slideItems.length < 3 && numOriginalSlides > 0) {
      // Seharusnya minimal ada (numOriginalSlides + 2) atau 3 jika numOriginalSlides = 1
      // console.error(`[${sliderId}] Mismatch: ${numOriginalSlides} original slides but only ${slideItems.length} total items. HTML structure might be incorrect.`);
      if (skeletonLoader) skeletonLoader.style.display = "none";
      return;
    }

    let currentIndex = 1; // Mulai dari slide asli pertama (setelah clone pertama)
    let isTransitioning = false;
    let autoPlayInterval = null;
    const autoPlayTime =
      parseInt(
        sliderWrapper.closest(".custom-slider-section")?.dataset.autoplaySpeed
      ) || 5000;
    let isHovering = false;
    let isLoading = true;
    let startX,
      currentTranslateX = 0,
      previousTranslateX = 0; // Modifikasi untuk drag
    let isTouching = false;
    const minSwipeDistance = 50;
    let aspectRatio = null;

    function goToSlide(index, withTransition = true) {
      // console.log(`[${sliderId}] goToSlide called. Index: ${index}, Transition: ${withTransition}, isTransitioning: ${isTransitioning}`);
      if (isTransitioning && withTransition) return;

      isTransitioning = withTransition;
      sliderInnerContainer.style.transition = withTransition
        ? "transform 0.5s cubic-bezier(0.25, 0.1, 0.25, 1)"
        : "none";

      currentTranslateX = -index * 100; // Persen
      sliderInnerContainer.style.transform = `translateX(${currentTranslateX}%)`;

      // Update active indicator: index 0 (clone) -> asli terakhir, index N+1 (clone) -> asli pertama
      let activeIndicatorIndex;
      if (index === 0) {
        // Di clone pertama (sebelum melompat ke slide asli terakhir)
        activeIndicatorIndex = numOriginalSlides - 1;
      } else if (index === numOriginalSlides + 1) {
        // Di clone terakhir (sebelum melompat ke slide asli pertama)
        activeIndicatorIndex = 0;
      } else {
        // Slide asli (index 1 sampai numOriginalSlides)
        activeIndicatorIndex = index - 1;
      }

      if (numOriginalSlides > 0) {
        // Hanya update indikator jika ada
        updateIndicators(activeIndicatorIndex);
      }

      slideItems.forEach((item, i) => {
        const isActive = i === index;
        item.setAttribute("aria-hidden", !isActive);
        const link = item.querySelector("a");
        if (link) {
          link.setAttribute("tabindex", isActive ? "0" : "-1");
        }
      });
      currentIndex = index;
    }

    function handleTransitionEnd() {
      // console.log(`[${sliderId}] TransitionEnd. CurrentIndex: ${currentIndex}`);
      isTransitioning = false;
      // Infinite loop: jika di clone pertama, lompat ke slide asli terakhir
      if (currentIndex === 0) {
        // console.log(`[${sliderId}] At first clone, jumping to index ${numOriginalSlides}`);
        goToSlide(numOriginalSlides, false); // numOriginalSlides adalah index dari slide asli terakhir (jika ada clone di awal)
      }
      // Jika di clone terakhir, lompat ke slide asli pertama
      else if (
        currentIndex === slideItems.length - 1 &&
        slideItems.length > 1
      ) {
        // slideItems.length-1 adalah index clone terakhir
        // console.log(`[${sliderId}] At last clone, jumping to index 1`);
        goToSlide(1, false); // 1 adalah index dari slide asli pertama (jika ada clone di awal)
      }
    }

    function updateIndicators(activeIndex) {
      if (!indicatorDots || indicatorDots.length === 0) return;
      indicatorDots.forEach((dot, i) => {
        const isActive = i === activeIndex;
        dot.classList.toggle("active", isActive);
        dot.setAttribute("aria-selected", isActive);
        dot.setAttribute("aria-current", isActive ? "true" : "false");
      });
    }

    // PERBAIKAN: Fungsi next() dan prev() telah diperbaiki untuk arah navigasi yang benar
    function next() {
      if (isTransitioning && numOriginalSlides > 1) return; // Jangan lakukan jika hanya 1 slide atau sedang transisi
      // console.log(`[${sliderId}] Next button pressed. Current index: ${currentIndex}`);
      goToSlide(currentIndex + 1, true); // Move to the next slide (right)
    }

    function prev() {
      if (isTransitioning && numOriginalSlides > 1) return;
      // console.log(`[${sliderId}] Prev button pressed. Current index: ${currentIndex}`);
      goToSlide(currentIndex - 1, true); // Move to the previous slide (left)
    }

    function startAutoPlay() {
      if (isLoading || autoPlayInterval || numOriginalSlides <= 1) return; // Jangan autoplay jika 1 slide
      // console.log(`[${sliderId}] Starting autoplay.`);
      autoPlayInterval = setInterval(() => {
        if (
          !isHovering &&
          !isTouching &&
          document.visibilityState === "visible"
        ) {
          next(); // AutoPlay menggunakan next() untuk slide ke kanan
        }
      }, autoPlayTime);
    }

    function stopAutoPlay() {
      if (autoPlayInterval) {
        // console.log(`[${sliderId}] Stopping autoplay.`);
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
      }
    }

    function setSliderHeight() {
      if (!aspectRatio) {
        // console.warn(`[${sliderId}] Aspect ratio not calculated, cannot set slider height.`);
        // Fallback height jika aspect ratio tidak ada
        sliderWrapper.style.height = "400px"; // Default height
        return;
      }
      const wrapperWidth = sliderWrapper.offsetWidth;
      if (wrapperWidth === 0) {
        // Jika wrapper belum visible/rendered
        // console.log(`[${sliderId}] Wrapper width is 0, delaying height set.`);
        requestAnimationFrame(setSliderHeight); // Coba lagi di frame berikutnya
        return;
      }
      const calculatedHeight = wrapperWidth * aspectRatio;
      const minHeight = 200;
      const maxHeight = Math.min(600, window.innerHeight * 0.8); // Max 600px atau 80% viewport height
      const finalHeight = Math.min(
        Math.max(calculatedHeight, minHeight),
        maxHeight
      );
      sliderWrapper.style.height = `${finalHeight}px`;
      // console.log(`[${sliderId}] Slider height set to ${finalHeight}px (width: ${wrapperWidth}, AR: ${aspectRatio})`);
    }

    function calculateAspectRatio() {
      return new Promise((resolve, reject) => {
        // Ambil gambar pertama YANG ASLI (bukan clone) untuk kalkulasi aspek rasio jika memungkinkan,
        // atau gambar pertama dari semua slideItems (yang merupakan clone)
        const imageForAspectRatio =
          slideItems.length > 1 ? slideItems[1].querySelector("img") : null; // slide asli pertama

        if (!imageForAspectRatio) {
          // console.warn(`[${sliderId}] No image found for aspect ratio calculation. Using default.`);
          aspectRatio = 0.5625; // Default 16:9
          resolve(aspectRatio);
          return;
        }

        if (
          imageForAspectRatio.complete &&
          imageForAspectRatio.naturalWidth > 0
        ) {
          aspectRatio =
            imageForAspectRatio.naturalHeight /
            imageForAspectRatio.naturalWidth;
          // console.log(`[${sliderId}] Aspect ratio calculated from loaded image: ${aspectRatio}`);
          resolve(aspectRatio);
        } else {
          imageForAspectRatio.onload = () => {
            if (imageForAspectRatio.naturalWidth === 0) {
              // Handle broken image
              // console.warn(`[${sliderId}] Image loaded but naturalWidth is 0. Using default AR.`);
              aspectRatio = 0.5625;
            } else {
              aspectRatio =
                imageForAspectRatio.naturalHeight /
                imageForAspectRatio.naturalWidth;
            }
            // console.log(`[${sliderId}] Aspect ratio calculated on image load: ${aspectRatio}`);
            resolve(aspectRatio);
          };
          imageForAspectRatio.onerror = () => {
            // console.error(`[${sliderId}] Error loading image for aspect ratio. Using default.`);
            aspectRatio = 0.5625; // Fallback
            resolve(aspectRatio); // Resolve agar tidak menggantung promise
          };
          // Timeout jika gambar tidak load
          setTimeout(() => {
            if (!aspectRatio) {
              // console.warn(`[${sliderId}] Image load timeout for aspect ratio. Using default.`);
              aspectRatio = 0.5625;
              resolve(aspectRatio);
            }
          }, 3000);
        }
      });
    }

    async function handleLoading() {
      // console.log(`[${sliderId}] handleLoading called.`);
      if (skeletonLoader) skeletonLoader.style.display = "flex"; // 'flex' jika ada item di dalamnya

      try {
        await calculateAspectRatio();
        setSliderHeight(); // Panggil setelah AR dihitung

        // Small delay agar UI tidak janky saat skeleton hilang dan konten muncul
        setTimeout(() => {
          if (skeletonLoader) skeletonLoader.style.display = "none";
          isLoading = false;
          // Inisialisasi slide pertama. currentIndex sudah 1.
          // Jika hanya 1 slide, numOriginalSlides akan 0 (karena indikator tidak ada)
          // atau jika PHP membuat 1 indikator, maka numOriginalSlides = 1.
          // Logika di atas sudah menangani kasus 1 slide.
          if (
            numOriginalSlides > 1 ||
            (numOriginalSlides === 0 && slideItems.length === 3)
          ) {
            // Jika > 1 slide asli, atau 1 slide asli dengan clone
            goToSlide(currentIndex, false); // Tampilkan slide asli pertama (index 1)
          }
          startAutoPlay(); // Autoplay hanya jika > 1 slide
          //   console.log(`[${sliderId}] Loading complete. Slider ready.`);
        }, 100); // Sedikit delay
      } catch (err) {
        // console.error(`[${sliderId}] Error during slider loading:`, err);
        if (skeletonLoader) skeletonLoader.style.display = "none";
        isLoading = false;
        // Mungkin tampilkan pesan error ke pengguna atau fallback UI
      }
    }

    // --- Touch/Swipe Handlers ---
    function handleTouchStart(e) {
      if (
        numOriginalSlides <= 1 &&
        !(numOriginalSlides === 0 && slideItems.length === 3)
      )
        return; // Jangan swipe jika hanya 1 slide (dan bukan 1 slide dengan clone)
      stopAutoPlay();
      isTouching = true;
      startX = (e.touches ? e.touches[0] : e).clientX;
      previousTranslateX = -currentIndex * sliderWrapper.offsetWidth; // Posisi translateX saat ini dalam px
      sliderInnerContainer.style.transition = "none"; // Hapus transisi saat drag
      // console.log(`[${sliderId}] TouchStart. StartX: ${startX}, PrevTranslateX: ${previousTranslateX}`);
    }

    function handleTouchMove(e) {
      if (
        !isTouching ||
        (numOriginalSlides <= 1 &&
          !(numOriginalSlides === 0 && slideItems.length === 3))
      )
        return;

      const currentX = (e.touches ? e.touches[0] : e).clientX;
      const diffX = currentX - startX;

      // TranslateX dalam pixel
      currentTranslateX = previousTranslateX + diffX;

      // Batasi drag agar tidak terlalu jauh (misal setengah lebar slide)
      const maxDrag = sliderWrapper.offsetWidth / 2;
      if (currentIndex === 0 && diffX > maxDrag) {
        // Drag ke kanan di clone pertama
        currentTranslateX = previousTranslateX + maxDrag;
      } else if (currentIndex === slideItems.length - 1 && diffX < -maxDrag) {
        // Drag ke kiri di clone terakhir
        currentTranslateX = previousTranslateX - maxDrag;
      }

      sliderInnerContainer.style.transform = `translateX(${currentTranslateX}px)`;
      // console.log(`[${sliderId}] TouchMove. DiffX: ${diffX}, CurrentTranslateX (px): ${currentTranslateX}`);
    }

    function handleTouchEnd() {
      if (
        !isTouching ||
        (numOriginalSlides <= 1 &&
          !(numOriginalSlides === 0 && slideItems.length === 3))
      )
        return;
      isTouching = false;
      sliderInnerContainer.style.transition =
        "transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1)";

      const movedBy =
        -currentIndex * sliderWrapper.offsetWidth - currentTranslateX; // Perbedaan dari posisi slide saat ini

      // console.log(`[${sliderId}] TouchEnd. MovedBy (px): ${-movedBy}`);

      // PERBAIKAN: Logika swipe diperbaiki untuk konsistensi dengan tombol navigasi
      if (Math.abs(movedBy) > minSwipeDistance) {
        if (movedBy > 0) {
          // Geser ke kiri (finger drag dari kanan ke kiri) -> next slide (ke kanan)
          next();
        } else {
          // Geser ke kanan (finger drag dari kiri ke kanan) -> prev slide (ke kiri)
          prev();
        }
      } else {
        // Kembali ke slide saat ini jika swipe tidak cukup jauh
        goToSlide(currentIndex, true);
      }
      startAutoPlay();
    }

    // Fungsi debounce untuk handling resize
    function debounce(func, wait) {
      let timeout;
      return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
      };
    }

    function setupAccessibility() {
      // Implementasi atribut ARIA untuk aksesibilitas
      sliderWrapper.setAttribute("role", "region");
      sliderWrapper.setAttribute("aria-roledescription", "carousel");
      sliderWrapper.setAttribute("aria-label", `Slider ${sliderId}`);

      slideItems.forEach((item, i) => {
        item.setAttribute("role", "group");
        item.setAttribute("aria-roledescription", "slide");
        item.setAttribute("aria-label", `Slide ${i}`);
        item.setAttribute("aria-hidden", i !== currentIndex);
      });

      if (prevButton) {
        prevButton.setAttribute("aria-label", "Previous slide");
        prevButton.setAttribute("role", "button");
      }

      if (nextButton) {
        nextButton.setAttribute("aria-label", "Next slide");
        nextButton.setAttribute("role", "button");
      }
    }

    function handleKeyboardNav(e) {
      if (
        (numOriginalSlides <= 1 &&
          !(numOriginalSlides === 0 && slideItems.length === 3)) ||
        !sliderWrapper.contains(document.activeElement)
      ) {
        return;
      }

      if (e.key === "ArrowLeft") {
        e.preventDefault();
        stopAutoPlay();
        prev();
        startAutoPlay();
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        stopAutoPlay();
        next();
        startAutoPlay();
      }
    }

    // --- Event Listeners ---
    if (
      numOriginalSlides > 0 ||
      (numOriginalSlides === 0 && slideItems.length === 3)
    ) {
      // Hanya attach event jika ada slide yang berfungsi
      sliderInnerContainer.addEventListener(
        "transitionend",
        handleTransitionEnd
      );

      // PERBAIKAN: Event listener untuk tombol navigasi
      if (prevButton && numOriginalSlides > 1) {
        // Hanya jika ada tombol dan >1 slide
        prevButton.addEventListener("click", (e) => {
          e.preventDefault();
          stopAutoPlay();
          prev(); // Panggil prev() untuk navigasi ke kiri
          startAutoPlay();
        });
      }
      if (nextButton && numOriginalSlides > 1) {
        nextButton.addEventListener("click", (e) => {
          e.preventDefault();
          stopAutoPlay();
          next(); // Panggil next() untuk navigasi ke kanan
          startAutoPlay();
        });
      }

      if (indicatorDots.length > 0 && numOriginalSlides > 1) {
        indicatorDots.forEach((dot) => {
          dot.addEventListener("click", (e) => {
            e.preventDefault();
            stopAutoPlay();
            const slideToGo = parseInt(e.target.dataset.slideTo); // 0-indexed
            goToSlide(slideToGo + 1, true); // +1 karena currentIndex 1 adalah slide asli pertama
            startAutoPlay();
          });
        });
      }

      if (
        numOriginalSlides > 1 ||
        (numOriginalSlides === 0 && slideItems.length === 3)
      ) {
        // Autoplay & hover/touch events hanya jika ada interaksi slider
        sliderWrapper.addEventListener("mouseenter", () => {
          isHovering = true;
          stopAutoPlay();
        });
        sliderWrapper.addEventListener("mouseleave", () => {
          isHovering = false;
          startAutoPlay();
        });

        // Touch Events
        sliderWrapper.addEventListener("touchstart", handleTouchStart, {
          passive: true,
        });
        sliderWrapper.addEventListener("touchmove", handleTouchMove, {
          passive: true,
        });
        sliderWrapper.addEventListener("touchend", handleTouchEnd);
        sliderWrapper.addEventListener("touchcancel", handleTouchEnd); // Handle cancel

        // Mouse Drag Events (simulasi touch)
        let isMouseDown = false;
        sliderWrapper.addEventListener("mousedown", (e) => {
          // Hanya drag dengan tombol kiri mouse
          if (e.button !== 0) return;
          e.preventDefault(); // Penting untuk mencegah seleksi teks saat drag
          isMouseDown = true;
          handleTouchStart(e); // Gunakan handler yang sama
        });
        document.addEventListener("mousemove", (e) => {
          if (!isMouseDown) return;
          // e.preventDefault(); // Bisa menyebabkan masalah scroll di halaman lain
          handleTouchMove(e);
        });
        document.addEventListener("mouseup", (e) => {
          if (!isMouseDown) return;
          isMouseDown = false;
          handleTouchEnd(e);
        });
        // Jika mouse keluar dari slider wrapper saat mousedown, anggap touchend
        sliderWrapper.addEventListener("mouseleave", (e) => {
          if (isMouseDown) {
            isMouseDown = false;
            handleTouchEnd(e); // Anggap sebagai touchend
          }
        });

        // Tambahkan keyboard navigation
        sliderWrapper.setAttribute("tabindex", "0");
        sliderWrapper.addEventListener("keydown", handleKeyboardNav);
      }
    }

    window.addEventListener(
      "resize",
      debounce(() => {
        if (!isLoading) {
          setSliderHeight();
          // goToSlide tanpa transisi agar tidak aneh saat resize
          goToSlide(currentIndex, false);
        }
      }, 200)
    );

    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "hidden") stopAutoPlay();
      else startAutoPlay();
    });

    // --- Inisialisasi ---
    setupAccessibility(); // Panggil ini bahkan jika 1 slide
    handleLoading(); // Mulai proses loading dan setup
  }
});
