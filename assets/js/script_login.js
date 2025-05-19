// assets/js/script_login.js
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("authContainer");
  if (!container) {
    // console.error("Auth container not found!");
    return;
  }

  const signUpOverlayButton = document.getElementById("signUpOverlayBtn");
  const signInOverlayButton = document.getElementById("signInOverlayBtn");
  const signUpLinkBottom = document.getElementById("signUpLinkBottom");
  const signInLinkBottom = document.getElementById("signInLinkBottom");

  const transitionSpeedCSS = getComputedStyle(
    document.documentElement
  ).getPropertyValue("--auth-transition-speed");
  const transitionDuration = transitionSpeedCSS
    ? parseFloat(transitionSpeedCSS) * 1000
    : 600;

  function initializeInputAnimations(formElement) {
    if (formElement) {
      const formInputs = formElement.querySelectorAll("input");
      formInputs.forEach((input) => {
        input.classList.remove("animated-input");
        void input.offsetWidth; // Trigger reflow
        input.classList.add("animated-input");
      });
    }
  }

  function activateSignUpPanel() {
    if (container.classList.contains("right-panel-active")) return;
    container.classList.add("right-panel-active");
    updateURL("register");
    setTimeout(() => {
      clearAllFormErrors();
      initializeInputAnimations(document.querySelector(".sign-up-container"));
    }, transitionDuration * 0.5); // Delay for panel transition
  }

  function activateSignInPanel() {
    if (!container.classList.contains("right-panel-active")) return;
    container.classList.remove("right-panel-active");
    updateURL(null);
    setTimeout(() => {
      clearAllFormErrors();
      initializeInputAnimations(document.querySelector(".sign-in-container"));
    }, transitionDuration * 0.5); // Delay for panel transition
  }

  function updateURL(formType) {
    const url = new URL(window.location);
    if (formType) {
      url.searchParams.set("form", formType);
    } else {
      url.searchParams.delete("form");
    }
    window.history.pushState({}, "", url);
  }

  if (signUpOverlayButton)
    signUpOverlayButton.addEventListener("click", activateSignUpPanel);
  if (signInOverlayButton)
    signInOverlayButton.addEventListener("click", activateSignInPanel);
  if (signUpLinkBottom)
    signUpLinkBottom.addEventListener("click", (e) => {
      e.preventDefault();
      activateSignUpPanel();
    });
  if (signInLinkBottom)
    signInLinkBottom.addEventListener("click", (e) => {
      e.preventDefault();
      activateSignInPanel();
    });

  // Initial state based on URL
  const urlParams = new URLSearchParams(window.location.search);
  const initialForm = urlParams.get("form");

  // Ensure correct panel is active without triggering full animation sequence on load for non-default
  if (
    initialForm === "register" &&
    !container.classList.contains("right-panel-active")
  ) {
    container.classList.add("right-panel-active"); // Set class directly
  } else if (
    initialForm !== "register" &&
    container.classList.contains("right-panel-active")
  ) {
    container.classList.remove("right-panel-active"); // Set class directly
  }

  // Initialize animations for the initially active form
  const activeFormOnInit = container.classList.contains("right-panel-active")
    ? document.querySelector(".sign-up-container")
    : document.querySelector(".sign-in-container");

  // Wait for container animation to finish before animating inputs
  if (
    getComputedStyle(container).animationName &&
    getComputedStyle(container).animationName !== "none"
  ) {
    container.addEventListener(
      "animationend",
      function handler(event) {
        if (event.animationName === "container-appear") {
          // Ensure it's the correct animation
          if (activeFormOnInit) {
            initializeInputAnimations(activeFormOnInit);
          }
          container.removeEventListener("animationend", handler);
        }
      },
      { once: false }
    ); // Use false if other animations might run on container
  } else {
    if (activeFormOnInit) {
      initializeInputAnimations(activeFormOnInit);
    }
  }

  // Ripple effect for buttons
  const buttons = document.querySelectorAll(
    ".form-container button[type='submit'], .overlay button.ghost"
  );
  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      // Ensure ripple doesn't get created if button is disabled or similar
      if (this.classList.contains("no-ripple")) return;

      const rect = e.target.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const ripple = document.createElement("span");
      ripple.className = "ripple-effect";
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;

      // Remove existing ripples before adding a new one to prevent multiple
      const existingRipple = this.querySelector(".ripple-effect");
      if (existingRipple) existingRipple.remove();

      this.appendChild(ripple);

      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });

  // Form validation
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  function validateEmail(email) {
    const re =
      /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }

  function clearError(inputElement) {
    const wrapper =
      inputElement.closest(".input-error-wrapper") ||
      inputElement.parentElement;
    const errorElement = wrapper.querySelector(".input-error-message");
    if (errorElement) {
      errorElement.remove();
    }
    inputElement.classList.remove("has-error");
  }

  function clearAllFormErrors() {
    document
      .querySelectorAll(".input-error-message")
      .forEach((el) => el.remove());
    document
      .querySelectorAll("input.has-error")
      .forEach((el) => el.classList.remove("has-error"));
  }

  function showInputError(inputElement, message) {
    clearError(inputElement); // Clear previous error first

    inputElement.classList.add("has-error");

    // Wrap input if not already wrapped for error message positioning
    let wrapper = inputElement.closest(".input-error-wrapper");
    if (!wrapper) {
      wrapper = document.createElement("div");
      wrapper.className = "input-error-wrapper";
      inputElement.parentNode.insertBefore(wrapper, inputElement);
      wrapper.appendChild(inputElement);
    }

    const errorElement = document.createElement("div");
    errorElement.className = "input-error-message";
    errorElement.textContent = message;

    // Insert error message after the input within its wrapper
    wrapper.appendChild(errorElement);

    inputElement.addEventListener("input", () => clearError(inputElement), {
      once: true,
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      let isValid = true;
      const emailInput = this.querySelector('input[name="email"]');
      const passwordInput = this.querySelector('input[name="password"]');

      clearError(emailInput);
      clearError(passwordInput);

      if (!validateEmail(emailInput.value)) {
        isValid = false;
        showInputError(emailInput, "Format email tidak valid.");
      }
      if (passwordInput.value.length === 0) {
        isValid = false;
        showInputError(passwordInput, "Password tidak boleh kosong.");
      } else if (passwordInput.value.length < 6) {
        // isValid = false; // Biarkan server yang validasi panjang minimal jika perlu
        // showInputError(passwordInput, "Password minimal 6 karakter.");
      }
      if (!isValid) e.preventDefault();
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      let isValid = true;
      const nameInput = this.querySelector('input[name="nama_lengkap"]');
      const emailInput = this.querySelector('input[name="email"]');
      const passwordInput = this.querySelector('input[name="password"]');
      const confirmPasswordInput = this.querySelector(
        'input[name="konfirmasi_password"]'
      );

      clearError(nameInput);
      clearError(emailInput);
      clearError(passwordInput);
      clearError(confirmPasswordInput);

      if (nameInput.value.trim().length === 0) {
        isValid = false;
        showInputError(nameInput, "Nama lengkap tidak boleh kosong.");
      }
      if (!validateEmail(emailInput.value)) {
        isValid = false;
        showInputError(emailInput, "Format email tidak valid.");
      }
      if (passwordInput.value.length === 0) {
        isValid = false;
        showInputError(passwordInput, "Password tidak boleh kosong.");
      } else if (passwordInput.value.length < 6) {
        isValid = false;
        showInputError(passwordInput, "Password minimal 6 karakter.");
      }
      if (confirmPasswordInput.value !== passwordInput.value) {
        isValid = false;
        showInputError(
          confirmPasswordInput,
          "Konfirmasi password tidak cocok."
        );
      }
      if (!isValid) e.preventDefault();
    });
  }

  // Clear errors on panel switch via timeout (to ensure elements are visible)
  // (Sudah ditambahkan di activateSignUpPanel dan activateSignInPanel)
});
