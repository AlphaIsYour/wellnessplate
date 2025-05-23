// assets/js/search.js
document.addEventListener("DOMContentLoaded", function () {
  const filterForm = document.getElementById("filterForm");

  // HAPUS: Logika untuk "Show More" / "Show Less"
  // document.querySelectorAll('.show-more-btn').forEach(button => { ... });

  // HAPUS: Logika auto-submit form ketika filter (checkbox) diubah
  /*
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input[type="checkbox"]'); // Hanya checkbox
        inputs.forEach(input => {
            input.addEventListener('change', function () {
                // filterForm.submit(); // TIDAK LAGI AUTO SUBMIT
            });
        });
    }
    */

  // Handle remove filter tag (tetap submit form)
  if (filterForm) {
    document.querySelectorAll(".remove-filter-btn").forEach((button) => {
      button.addEventListener("click", function () {
        const filterType = this.dataset.filterType;
        const filterValue = this.dataset.filterValue;

        // Logika untuk manual filter sudah dihapus dari sini karena inputnya tidak ada di UI
        const checkbox = filterForm.querySelector(
          `input[type="checkbox"][name="${filterType}[]"][value="${filterValue}"]`
        );
        if (checkbox) {
          checkbox.checked = false;
        }
        filterForm.submit(); // Submit setelah mengubah state
      });
    });

    // Handle clear all filters (tetap submit form)
    const clearAllBtn = document.getElementById("clearAllFiltersBtn");
    if (clearAllBtn) {
      clearAllBtn.addEventListener("click", function () {
        filterForm
          .querySelectorAll('input[type="checkbox"]')
          .forEach((cb) => (cb.checked = false));
        // Logika untuk clear manual input sudah dihapus
        filterForm.submit(); // Submit setelah membersihkan
      });
    }
  }

  // HAPUS: Inisialisasi untuk show more / manual input
  // document.querySelectorAll('.filter-group').forEach(group => { ... });
});
