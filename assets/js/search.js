// assets/js/search.js
document.addEventListener('DOMContentLoaded', function() {
    // Handle filter removal buttons
    document.querySelectorAll('.remove-filter-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filterType = this.dataset.filterType;
            const filterValue = this.dataset.filterValue;
            
            // Uncheck the corresponding checkbox
            const checkbox = document.querySelector(`input[type="checkbox"][name="${filterType}[]"][value="${filterValue}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            // Submit the form
            document.getElementById('filterForm').submit();
        });
    });

    // Handle clear all filters button
    const clearAllBtn = document.getElementById('clearAllFiltersBtn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Get current URL
            const url = new URL(window.location.href);
            
            // Keep only the keyword parameter if it exists
            const keyword = url.searchParams.get('keyword');
            url.search = keyword ? `?keyword=${encodeURIComponent(keyword)}` : '';
            
            // Redirect to the filtered URL
            window.location.href = url.toString();
        });
    }

    // Handle checkbox changes
    document.querySelectorAll('.checkbox-list input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Optional: Auto-submit form on checkbox change
            // document.getElementById('filterForm').submit();
        });
    });

    // Preserve search keyword when applying filters
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const urlParams = new URLSearchParams(window.location.search);
            const keyword = urlParams.get('keyword');
            
            if (keyword && !this.querySelector('input[name="keyword"]')) {
                const keywordInput = document.createElement('input');
                keywordInput.type = 'hidden';
                keywordInput.name = 'keyword';
                keywordInput.value = keyword;
                this.appendChild(keywordInput);
            }
        });
    }
});
