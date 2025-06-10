const container = document.querySelector(".container");
const registerBtn = document.querySelector(".register-btn");
const loginBtn = document.querySelector(".login-btn");

// Function to update URL without reloading
function updateURL(form) {
    const url = new URL(window.location.href);
    if (form === 'register') {
        url.searchParams.set('form', 'register');
    } else {
        url.searchParams.delete('form');
    }
    window.history.pushState({}, '', url);
}

// Check URL on page load
function checkURLAndSetForm() {
    const urlParams = new URLSearchParams(window.location.search);
    const formType = urlParams.get('form');
    if (formType === 'register') {
        container.classList.add("active");
    } else {
        container.classList.remove("active");
    }
}

registerBtn.addEventListener("click", () => {
    container.classList.add("active");
    updateURL('register');
});

loginBtn.addEventListener("click", () => {
    container.classList.remove("active");
    updateURL('login');
});

// Handle browser back/forward buttons
window.addEventListener('popstate', () => {
    checkURLAndSetForm();
});

// Check URL on initial page load
document.addEventListener('DOMContentLoaded', () => {
    checkURLAndSetForm();
});
