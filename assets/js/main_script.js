// simple_slider_script.js
document.addEventListener("DOMContentLoaded", function () {
  const sliders = document.querySelectorAll(".simple-slider-section");

  sliders.forEach((sliderSection) => {
    const wrapper = sliderSection.querySelector(".simple-slider-wrapper");
    const inner = sliderSection.querySelector(".simple-slider-inner");
    const slides = sliderSection.querySelectorAll(".simple-slide-item");
    const skeleton = sliderSection.querySelector(".simple-slider-skeleton");

    if (!inner || !slides || slides.length === 0) {
      if (skeleton) skeleton.style.display = "none";
      return; // Tidak ada slide, hentikan
    }

    let currentIndex = 0;
    const numSlides = slides.length;
    const autoplayEnabled = sliderSection.dataset.autoplay === "true";
    // Ini akan mengambil nilai 5000 dari data attribute atau PHP
    const autoplaySpeed = parseInt(sliderSection.dataset.autoplaySpeed) || 5000;
    let autoplayInterval;

    function setActiveSlide(index) {
      slides.forEach((slide, i) => {
        slide.setAttribute("data-active", i === index);
        slide.setAttribute("aria-hidden", i !== index);
        const link = slide.querySelector("a");
        if (link) {
          link.tabIndex = i === index ? 0 : -1;
        }
      });
    }

    function goToSlide(index) {
      if (index < 0) {
        index = numSlides - 1;
      } else if (index >= numSlides) {
        index = 0;
      }
      inner.style.transform = `translateX(-${index * 100}%)`;
      currentIndex = index;
      setActiveSlide(currentIndex);
    }

    function nextSlide() {
      goToSlide(currentIndex + 1);
    }

    function startAutoplay() {
      if (!autoplayEnabled || numSlides <= 1) return;
      stopAutoplay();
      autoplayInterval = setInterval(nextSlide, autoplaySpeed);
    }

    function stopAutoplay() {
      clearInterval(autoplayInterval);
    }

    // Inisialisasi
    // Mengandalkan CSS aspect-ratio untuk dimensi. JS hanya menyembunyikan skeleton.
    const firstImage = slides[0] ? slides[0].querySelector("img") : null;
    if (firstImage) {
      const hideSkeleton = () => {
        if (skeleton) {
          skeleton.style.opacity = "0";
          skeleton.addEventListener(
            "transitionend",
            () => (skeleton.style.display = "none"),
            { once: true }
          );
        }
      };

      if (firstImage.complete) {
        // Jika gambar sudah di cache atau non-SVG yang cepat
        hideSkeleton();
      } else {
        // Untuk SVG, 'load' event adalah yang paling reliable
        // Untuk img biasa, 'load' juga bekerja
        firstImage.addEventListener("load", hideSkeleton);
        firstImage.addEventListener("error", hideSkeleton); // Juga sembunyikan jika error
      }
    } else {
      // Tidak ada gambar sama sekali
      if (skeleton) skeleton.style.display = "none";
    }

    goToSlide(0);
    startAutoplay();

    if (autoplayEnabled && numSlides > 1) {
      sliderSection.addEventListener("mouseenter", stopAutoplay);
      sliderSection.addEventListener("mouseleave", startAutoplay);
      sliderSection.addEventListener("focusin", stopAutoplay);
      sliderSection.addEventListener("focusout", startAutoplay);
    }
  });
});
