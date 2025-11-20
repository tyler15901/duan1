// Sort tours
function sortTours() {
    const sortSelect = document.getElementById('sortSelect');
    const sortValue = sortSelect.value;
    
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortValue);
    urlParams.set('page', '1'); // Reset to first page
    
    // Redirect with new sort parameter
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set current sort value from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort') || 'recommended';
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.value = currentSort;
    }
});

