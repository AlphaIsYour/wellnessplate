// assets/js/search.js
document.addEventListener("DOMContentLoaded", function () {
  const filterForm = document.getElementById("filterForm");
  if (filterForm) {
    document.querySelectorAll(".remove-filter-btn").forEach((button) => {
      button.addEventListener("click", function () {
        const filterType = this.dataset.filterType;
        const filterValue = this.dataset.filterValue;

        const checkbox = filterForm.querySelector(
          `input[type="checkbox"][name="${filterType}[]"][value="${filterValue}"]`
        );
        if (checkbox) {
          checkbox.checked = false;
        }
        filterForm.submit(); // Submit setelah mengubah state
      });
    });

    const clearAllBtn = document.getElementById("clearAllFiltersBtn");
    if (clearAllBtn) {
      clearAllBtn.addEventListener("click", function () {
        filterForm
          .querySelectorAll('input[type="checkbox"]')
          .forEach((cb) => (cb.checked = false));
        filterForm.submit();
      });
    }
  }
});
