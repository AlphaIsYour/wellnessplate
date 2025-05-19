// assets/js/script_login.js
document.addEventListener("DOMContentLoaded", function () {
  const signUpButton = document.getElementById("signUp");
  const signInButton = document.getElementById("signIn");
  const container = document.getElementById("authContainer");

  if (signUpButton && signInButton && container) {
    signUpButton.addEventListener("click", () => {
      container.classList.add("right-panel-active");
    });

    signInButton.addEventListener("click", () => {
      container.classList.remove("right-panel-active");
    });
  }

  // Cek parameter URL untuk mengaktifkan panel register saat load
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("form") === "register" && container) {
    container.classList.add("right-panel-active");
  }
});
