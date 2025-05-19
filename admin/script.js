// script.js
// Untuk saat ini kita biarkan kosong,
// akan diisi jika ada interaksi JavaScript yang dibutuhkan.

document.addEventListener("DOMContentLoaded", function () {
  // Contoh: konfirmasi sebelum menghapus
  const deleteButtons = document.querySelectorAll(".delete-link"); // Jika ada link dengan class .delete-link
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        event.preventDefault();
      }
    });
  });
});
