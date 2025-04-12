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

// Handle form submission untuk tambah resep
const addResepForm = document.getElementById("add-resep-form");
if (addResepForm) {
  addResepForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_resep.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Resep berhasil ditambahkan!");
          closePopup("add-resep-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan resep: " + data.message);
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

// Handle form submission untuk tambah bahan
const addBahanForm = document.getElementById("add-bahan-form");
if (addBahanForm) {
  addBahanForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_bahan.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Bahan berhasil ditambahkan!");
          closePopup("add-bahan-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan bahan: " + data.message);
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

// Handle form submission untuk tambah gizi
const addGiziForm = document.getElementById("add-gizi-form");
if (addGiziForm) {
  addGiziForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_gizi.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Gizi berhasil ditambahkan!");
          closePopup("add-gizi-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan gizi: " + data.message);
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

// Handle form submission untuk tambah resep bahan
const addResepBahanForm = document.getElementById("add-resep-bahan-form");
if (addResepBahanForm) {
  addResepBahanForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_resep_bahan.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Resep bahan berhasil ditambahkan!");
          closePopup("add-resep-bahan-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan resep bahan: " + data.message);
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

// Handle form submission untuk tambah user
const addUserForm = document.getElementById("add-user-form");
if (addUserForm) {
  addUserForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_user.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("User berhasil ditambahkan!");
          closePopup("add-user-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan user: " + data.message);
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

// Handle form submission untuk tambah admin
const addAdminForm = document.getElementById("add-admin-form");
if (addAdminForm) {
  addAdminForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-feather="loader" class="spin"></i> Memproses...';
    feather.replace();

    const formData = new FormData(this);

    fetch("process_add_admin.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-feather="save"></i> Simpan';
        feather.replace();

        if (data.success) {
          alert("Admin berhasil ditambahkan!");
          closePopup("add-admin-popup");
          location.reload();
        } else {
          alert("Gagal menambahkan admin: " + data.message);
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

// Animasi card saat load
const cards = document.querySelectorAll(".card");
if (cards) {
  cards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(30px) scale(0.95)";
    setTimeout(() => {
      card.style.transition = "all 0.5s ease";
      card.style.opacity = "1";
      card.style.transform = "translateY(0) scale(1)";
    }, index * 150);
  });
}
