// assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
  // ... (kode SweetAlert delete kamu yang sudah ada) ...

  // Mobile Menu Toggle untuk Frontend Header
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const mainNavigationFrontend = document.querySelector(
    ".main-navigation-frontend"
  );

  if (mobileMenuToggle && mainNavigationFrontend) {
    mobileMenuToggle.addEventListener("click", function () {
      mainNavigationFrontend.classList.toggle("mobile-menu-active");
      // Ganti ikon toggle jika perlu (misal dari burger ke X)
      if (mainNavigationFrontend.classList.contains("mobile-menu-active")) {
        this.innerHTML = "×"; // Karakter X
      } else {
        this.innerHTML = "☰"; // Karakter Burger
      }
    });
  }

  // ... (kode SweetAlert dari session yang mungkin kamu pindah ke sini) ...
});
