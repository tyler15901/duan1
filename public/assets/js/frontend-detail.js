// Set main image
function setMainImage(index) {
    if (!galleryImages || galleryImages.length === 0) return;
    
    currentImageIndex = index;
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    if (mainImage && galleryImages[index]) {
        mainImage.src = galleryImages[index];
    }
    
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

// Change image
function changeImage(direction) {
    if (!galleryImages || galleryImages.length === 0) return;
    
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = galleryImages.length - 1;
    } else if (currentImageIndex >= galleryImages.length) {
        currentImageIndex = 0;
    }
    
    setMainImage(currentImageIndex);
}

// Select date
function selectDate(date) {
    const dateButtons = document.querySelectorAll('.date-btn');
    dateButtons.forEach(btn => {
        if (btn.textContent.trim() === date || (date === 'all' && btn.textContent.trim() === 'Tất cả')) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Change quantity
function changeQuantity(type, change) {
    const qtyElement = document.getElementById(type + 'Qty');
    if (!qtyElement) return;
    
    let currentQty = parseInt(qtyElement.textContent) || 0;
    currentQty += change;
    
    if (currentQty < 0) {
        currentQty = 0;
    }
    
    qtyElement.textContent = currentQty;
    calculateTotal();
}

// Calculate total price
function calculateTotal() {
    const adultQty = parseInt(document.getElementById('adultQty').textContent) || 0;
    const childQty = parseInt(document.getElementById('childQty').textContent) || 0;
    const infantQty = parseInt(document.getElementById('infantQty').textContent) || 0;
    
    if (!prices) return;
    
    const total = (adultQty * prices.adult) + (childQty * prices.child) + (infantQty * prices.infant);
    
    const totalPriceElement = document.getElementById('totalPrice');
    if (totalPriceElement) {
        totalPriceElement.textContent = formatPrice(total) + ' ₫';
    }
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Request booking
function requestBooking() {
    const adultQty = parseInt(document.getElementById('adultQty').textContent) || 0;
    const childQty = parseInt(document.getElementById('childQty').textContent) || 0;
    const infantQty = parseInt(document.getElementById('infantQty').textContent) || 0;
    
    if (adultQty === 0 && childQty === 0) {
        alert('Vui lòng chọn ít nhất 1 người lớn hoặc trẻ em');
        return;
    }
    
    const total = (adultQty * prices.adult) + (childQty * prices.child) + (infantQty * prices.infant);
    
    alert(`Đang xử lý yêu cầu đặt tour:\n\nNgười lớn: ${adultQty}\nTrẻ em: ${childQty}\nTrẻ nhỏ: ${infantQty}\n\nTổng tiền: ${formatPrice(total)} ₫`);
    // Here you would typically send the booking request to the server
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial image if gallery exists
    if (galleryImages && galleryImages.length > 0) {
        setMainImage(0);
    }
    
    // Calculate initial total
    calculateTotal();
});

