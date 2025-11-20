// Current scroll positions for each tour section
const scrollPositions = {
    sale: 0,
    domestic: 0,
    international: 0
};

// Slide tour function
function slideTour(type, direction) {
    const container = document.getElementById(`${type}-tours`);
    if (!container) return;
    
    const cardWidth = 370; // 350px card + 20px gap
    const scrollAmount = cardWidth * 2; // Scroll 2 cards at a time
    
    scrollPositions[type] += direction * scrollAmount;
    
    // Limit scrolling
    const maxScroll = container.scrollWidth - container.clientWidth;
    scrollPositions[type] = Math.max(0, Math.min(scrollPositions[type], maxScroll));
    
    container.scrollTo({
        left: scrollPositions[type],
        behavior: 'smooth'
    });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Search form handling
document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.querySelector('.btn-search');
    
    if (searchButton) {
        searchButton.addEventListener('click', function(e) {
            // Form will submit naturally
        });
    }
});

