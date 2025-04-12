// scripts.js
feather.replace();

// scripts.js
feather.replace();

// Fungsi untuk membuka pop-up
function openPopup(popupId) {
  document.getElementById(popupId).style.display = "block";
}

// Fungsi untuk menutup pop-up
function closePopup(popupId) {
  document.getElementById(popupId).style.display = "none";
}

// Handle form submission untuk tambah kondisi
document
  .getElementById("add-kondisi-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("process_add_kondisi.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Kondisi berhasil ditambahkan!");
          closePopup("add-kondisi-popup");
          location.reload(); // Refresh halaman untuk update tabel
        } else {
          alert("Gagal menambahkan kondisi: " + data.message);
        }
      })
      .catch((error) => {
        alert("Terjadi kesalahan: " + error);
      });
  });
