// Tour data
const tourData = {
    sale: [
        { id: 1, title: "Tour Hà Nội - Sapa 3N2Đ", description: "Khám phá vùng núi Tây Bắc", price: "3.990.000", oldPrice: "5.990.000", image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 2, title: "Tour Đà Nẵng - Hội An 4N3Đ", description: "Thành phố biển xinh đẹp", price: "4.990.000", oldPrice: "6.990.000", image: "https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=400" },
        { id: 3, title: "Tour Phú Quốc 3N2Đ", description: "Thiên đường biển đảo", price: "5.990.000", oldPrice: "7.990.000", image: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400" },
        { id: 4, title: "Tour Nha Trang 4N3Đ", description: "Biển xanh cát trắng", price: "4.490.000", oldPrice: "6.490.000", image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400" },
        { id: 5, title: "Tour Đà Lạt 3N2Đ", description: "Thành phố ngàn hoa", price: "3.490.000", oldPrice: "5.490.000", image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 6, title: "Tour Hạ Long 2N1Đ", description: "Vịnh di sản thế giới", price: "2.990.000", oldPrice: "4.990.000", image: "https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=400" }
    ],
    domestic: [
        { id: 7, title: "Tour Hà Giang 4N3Đ", description: "Cao nguyên đá độc đáo", price: "4.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 8, title: "Tour Huế - Quảng Bình 5N4Đ", description: "Di sản văn hóa thế giới", price: "5.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=400" },
        { id: 9, title: "Tour Cần Thơ - Miền Tây 3N2Đ", description: "Sông nước miền Tây", price: "3.490.000", oldPrice: null, image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400" },
        { id: 10, title: "Tour Mù Cang Chải 3N2Đ", description: "Ruộng bậc thang tuyệt đẹp", price: "3.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400" },
        { id: 11, title: "Tour Quy Nhơn 4N3Đ", description: "Biển đẹp bình yên", price: "4.490.000", oldPrice: null, image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 12, title: "Tour Côn Đảo 3N2Đ", description: "Đảo thiên đường", price: "6.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=400" }
    ],
    international: [
        { id: 13, title: "Tour Thái Lan 5N4Đ", description: "Bangkok - Pattaya", price: "12.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 14, title: "Tour Singapore 4N3Đ", description: "Đảo quốc sư tử", price: "15.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=400" },
        { id: 15, title: "Tour Hàn Quốc 6N5Đ", description: "Seoul - Busan", price: "18.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400" },
        { id: 16, title: "Tour Nhật Bản 7N6Đ", description: "Tokyo - Osaka - Kyoto", price: "35.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400" },
        { id: 17, title: "Tour Úc 8N7Đ", description: "Sydney - Melbourne", price: "45.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400" },
        { id: 18, title: "Tour Châu Âu 10N9Đ", description: "Pháp - Ý - Thụy Sĩ", price: "65.990.000", oldPrice: null, image: "https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=400" }
    ]
};

// Current scroll positions for each tour section
const scrollPositions = {
    sale: 0,
    domestic: 0,
    international: 0
};

// Initialize tours on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTours('sale', tourData.sale);
    renderTours('domestic', tourData.domestic);
    renderTours('international', tourData.international);
});

// Render tours to the page
function renderTours(type, tours) {
    const container = document.getElementById(`${type}-tours`);
    if (!container) return;
    
    container.innerHTML = tours.map(tour => `
        <div class="tour-card">
            <img src="${tour.image}" alt="${tour.title}">
            <div class="tour-card-content">
                <h3>${tour.title}</h3>
                <p>${tour.description}</p>
                <div class="tour-price-info">
                    <span class="price">${tour.price}đ</span>
                    ${tour.oldPrice ? `<span class="old-price">${tour.oldPrice}đ</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

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

// Auto-slide functionality (optional)
let autoSlideIntervals = {};

function startAutoSlide(type) {
    if (autoSlideIntervals[type]) {
        clearInterval(autoSlideIntervals[type]);
    }
    
    autoSlideIntervals[type] = setInterval(() => {
        const container = document.getElementById(`${type}-tours`);
        if (!container) return;
        
        const maxScroll = container.scrollWidth - container.clientWidth;
        if (scrollPositions[type] >= maxScroll) {
            scrollPositions[type] = 0;
        } else {
            scrollPositions[type] += 370;
        }
        
        container.scrollTo({
            left: scrollPositions[type],
            behavior: 'smooth'
        });
    }, 5000); // Auto-slide every 5 seconds
}

function stopAutoSlide(type) {
    if (autoSlideIntervals[type]) {
        clearInterval(autoSlideIntervals[type]);
        autoSlideIntervals[type] = null;
    }
}

// Optional: Add hover to pause auto-slide
document.addEventListener('DOMContentLoaded', function() {
    const tourSections = ['sale', 'domestic', 'international'];
    
    tourSections.forEach(type => {
        const container = document.getElementById(`${type}-tours`);
        if (container) {
            container.addEventListener('mouseenter', () => stopAutoSlide(type));
            container.addEventListener('mouseleave', () => startAutoSlide(type));
            // Uncomment to enable auto-slide:
            // startAutoSlide(type);
        }
    });
});

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
    const searchForm = document.querySelector('.search-form');
    const searchButton = document.querySelector('.btn-search');
    
    if (searchButton) {
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            const destination = document.querySelector('.form-input').value;
            alert(`Đang tìm kiếm tour đến: ${destination || 'Tất cả điểm đến'}`);
            // Add your search logic here
        });
    }
});

// Mobile menu toggle (for responsive design)
function toggleMobileMenu() {
    const menu = document.querySelector('.menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

