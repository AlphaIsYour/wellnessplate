// Inisialisasi Feather Icons
feather.replace();

// Fungsi untuk membuka pop-up
function openPopup(popupId) {
  const popup = document.getElementById(popupId);
  if (popup) {
    popup.style.display = "flex";
    setTimeout(() => popup.classList.add("active"), 10);
  }
}

// Fungsi untuk menutup pop-up
function closePopup(popupId) {
  const popup = document.getElementById(popupId);
  if (popup) {
    popup.classList.remove("active");
    setTimeout(() => (popup.style.display = "none"), 300);
  }
}

// Toggle Sidebar
const toggleSidebarBtn = document.getElementById("toggle-sidebar");
if (toggleSidebarBtn) {
  toggleSidebarBtn.addEventListener("click", () => {
    const sidebar = document.getElementById("sidebar");
    const main = document.querySelector(".main");
    if (sidebar && main) {
      sidebar.classList.toggle("collapsed");
      main.classList.toggle("collapsed");
    }
  });
}

// Handle form submission untuk tambah kondisi
const addKondisiForm = document.getElementById("add-kondisi-form");
if (addKondisiForm) {
  addKondisiForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_kondisi.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Kondisi berhasil ditambahkan!");
          closePopup("add-kondisi-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan kondisi: " + data.message);
        }
      })
      .catch((error) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();
        alert("Terjadi kesalahan: " + error);
      });
  });
}

// Indikator menu aktif
const sidebarLinks = document.querySelectorAll(".sidebar ul li a");
sidebarLinks.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});
