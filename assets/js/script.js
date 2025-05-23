// assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const mainNavigationFrontend = document.querySelector(
    ".main-navigation-frontend"
  );

  if (mobileMenuToggle && mainNavigationFrontend) {
    mobileMenuToggle.addEventListener("click", function () {
      mainNavigationFrontend.classList.toggle("mobile-menu-active");
      if (mainNavigationFrontend.classList.contains("mobile-menu-active")) {
        this.innerHTML = "×";
      } else {
        this.innerHTML = "☰";
      }
    });
  }
});
