<?php
// View chi tiết tour
require_once __DIR__ . '/../../app/models/TourImageModel.php';
require_once __DIR__ . '/../../app/models/TourPriceModel.php';
$tourImageModel = new TourImageModel();
$tourPriceModel = new TourPriceModel();
$mainImage = !empty($tour['images']) ? $tourImageModel->getMainImage($tour['MaTour']) : null;
$imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tour['TenTour']) ?> - AT Travel</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/frontend-detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?= BASE_URL ?>home"><img src="https://via.placeholder.com/150x50/0066CC/FFFFFF?text=AT+Travel" alt="AT Travel Logo"></a>
                </div>
                <nav class="menu">
                    <ul>
                        <li><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1">Tour trong nước</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=2" class="<?= $tour['MaLoaiTour'] == 2 ? 'active' : '' ?>">Tour nước ngoài</a></li>
                        <li><a href="#">Điểm đến</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <a href="<?= BASE_URL ?>login" class="btn-login">Đăng nhập</a>
                    <a href="#" class="btn-register">Đăng ký</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container">
            <a href="<?= BASE_URL ?>home">Trang chủ</a>
            <span>/</span>
            <a href="<?= BASE_URL ?>tours?type=<?= $tour['MaLoaiTour'] ?>"><?= $tour['TenLoaiTour'] ?></a>
            <span>/</span>
            <span><?= htmlspecialchars($tour['TenTour']) ?></span>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Tour Title -->
            <h1 class="tour-title-main"><?= htmlspecialchars($tour['TenTour']) ?></h1>

            <div class="content-wrapper">
                <!-- Left: Gallery -->
                <div class="gallery-section">
                    <div class="main-gallery">
                        <div class="main-image-wrapper">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($tour['TenTour']) ?>" id="mainImage" class="main-image">
                            <?php if ($tour['price']): ?>
                            <div class="gallery-badge">
                                <span><?= number_format($tour['price']['GiaNguoiLon'] ?? 0, 0, ',', '.') ?>k/khách</span>
                            </div>
                            <?php endif; ?>
                            <?php if (count($tour['images'] ?? []) > 1): ?>
                            <button class="gallery-nav prev" onclick="changeImage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="gallery-nav next" onclick="changeImage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($tour['images']) && count($tour['images']) > 1): ?>
                        <div class="thumbnail-gallery">
                            <?php foreach (array_slice($tour['images'], 0, 5) as $index => $image): ?>
                            <img src="<?= $tourImageModel->getImageUrl($image['DuongDan']) ?>" 
                                 alt="Thumbnail <?= $index + 1 ?>" 
                                 class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                                 onclick="setMainImage(<?= $index ?>)">
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="booking-notice">
                            <i class="fas fa-info-circle"></i>
                            <span>Đã có khách đặt tour này</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Sidebar -->
                <aside class="booking-sidebar">
                    <div class="booking-card">
                        <h3 class="booking-title">Lịch Trình và Giá Tour</h3>
                        
                        <?php if (!empty($tour['schedules'])): ?>
                        <div class="date-selection">
                            <p class="section-label">Chọn Lịch Trình và Xem Giá:</p>
                            <div class="date-buttons">
                                <?php foreach (array_slice($tour['schedules'], 0, 3) as $schedule): ?>
                                <button class="date-btn" onclick="selectDate('<?= date('d/m', strtotime($schedule['NgayKhoiHanh'])) ?>')">
                                    <?= date('d/m', strtotime($schedule['NgayKhoiHanh'])) ?>
                                </button>
                                <?php endforeach; ?>
                                <button class="date-btn" onclick="selectDate('all')">Tất cả</button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($tour['price']): ?>
                        <div class="participant-selection">
                            <div class="participant-item">
                                <div class="participant-info">
                                    <span class="participant-label">Người lớn > 10 tuổi</span>
                                    <span class="participant-price">x <?= number_format($tour['price']['GiaNguoiLon'] ?? 0, 0, ',', '.') ?> ₫</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity('adult', -1)">-</button>
                                    <span class="qty-value" id="adultQty">2</span>
                                    <button class="qty-btn" onclick="changeQuantity('adult', 1)">+</button>
                                </div>
                            </div>
                            <div class="participant-item">
                                <div class="participant-info">
                                    <span class="participant-label">Trẻ em 2 - 10 tuổi</span>
                                    <span class="participant-price">x <?= number_format($tour['price']['GiaTreEm'] ?? 0, 0, ',', '.') ?> ₫</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity('child', -1)">-</button>
                                    <span class="qty-value" id="childQty">0</span>
                                    <button class="qty-btn" onclick="changeQuantity('child', 1)">+</button>
                                </div>
                            </div>
                            <div class="participant-item">
                                <div class="participant-info">
                                    <span class="participant-label">Trẻ nhỏ < 2 tuổi</span>
                                    <span class="participant-price">Miễn phí</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity('infant', -1)">-</button>
                                    <span class="qty-value" id="infantQty">0</span>
                                    <button class="qty-btn" onclick="changeQuantity('infant', 1)">+</button>
                                </div>
                            </div>
                            <div class="info-note">
                                <i class="fas fa-info-circle"></i>
                                <span>Liên hệ để xác nhận chỗ</span>
                            </div>
                        </div>

                        <div class="price-summary">
                            <div class="price-row total">
                                <span>Tổng Giá Tour</span>
                                <span class="total-price" id="totalPrice"><?= number_format(($tour['price']['GiaNguoiLon'] ?? 0) * 2, 0, ',', '.') ?> ₫</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <button class="btn-booking" onclick="requestBooking()">
                            Yêu cầu đặt
                        </button>
                    </div>
                </aside>
            </div>

            <!-- Tour Includes Section -->
            <section class="tour-includes">
                <h2 class="section-title">Tour Trọn Gói bao gồm</h2>
                <div class="includes-content">
                    <div class="departure-info">
                        <span><i class="fas fa-map-marker-alt"></i> Khởi hành từ: Hồ Chí Minh</span>
                        <span><i class="fas fa-plane"></i></span>
                        <span><i class="fas fa-bus"></i></span>
                        <span>Mã Tour: T<?= str_pad($tour['MaTour'], 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <ul class="includes-list">
                        <li><i class="fas fa-check-circle"></i> Vé máy bay</li>
                        <li><i class="fas fa-check-circle"></i> Khách sạn</li>
                        <li><i class="fas fa-check-circle"></i> Xe tham quan</li>
                        <li><i class="fas fa-check-circle"></i> Vé tham quan</li>
                        <li><i class="fas fa-check-circle"></i> Hướng dẫn viên</li>
                        <li><i class="fas fa-check-circle"></i> Bảo hiểm du lịch</li>
                    </ul>
                </div>
            </section>

            <?php if (!empty($tour['itinerary'])): ?>
            <!-- Tour Itinerary -->
            <section class="tour-experiences">
                <h2 class="section-title">Lịch trình tour</h2>
                <div class="experiences-grid">
                    <?php foreach ($tour['itinerary'] as $day): ?>
                    <div class="experience-item">
                        <h3>Ngày <?= $day['NgayThu'] ?>: <?= htmlspecialchars($day['TieuDe'] ?? '') ?></h3>
                        <p><?= htmlspecialchars($day['NoiDung'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Related Tours -->
            <?php if (!empty($relatedTours)): ?>
            <section class="related-tours">
                <h2 class="section-title">Tours liên quan</h2>
                <div class="related-tours-grid" id="relatedTours">
                    <?php foreach ($relatedTours as $relatedTour): 
                        $relatedMainImage = $tourImageModel->getMainImage($relatedTour['MaTour']);
                        $relatedImageUrl = $relatedMainImage ? $tourImageModel->getImageUrl($relatedMainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                        $relatedPrice = $tourPriceModel->getCurrentPrice($relatedTour['MaTour']);
                    ?>
                    <div class="related-tour-card">
                        <div class="related-tour-image">
                            <a href="<?= BASE_URL ?>tour?id=<?= $relatedTour['MaTour'] ?>">
                                <img src="<?= $relatedImageUrl ?>" alt="<?= htmlspecialchars($relatedTour['TenTour']) ?>">
                            </a>
                        </div>
                        <div class="related-tour-content">
                            <h3 class="related-tour-title">
                                <a href="<?= BASE_URL ?>tour?id=<?= $relatedTour['MaTour'] ?>"><?= htmlspecialchars($relatedTour['TenTour']) ?></a>
                            </h3>
                            <div class="related-tour-details">
                                <span><i class="far fa-clock"></i> <?= $relatedTour['SoNgay'] ?> Ngày</span>
                            </div>
                            <div class="related-tour-price">
                                <span class="related-tour-price-new"><?= number_format($relatedPrice['GiaNguoiLon'] ?? 0, 0, ',', '.') ?> ₫</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-column">
                    <h4>Du lịch trong nước</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Hà Nội">Hà Nội</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Huế">Huế</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Đà Nẵng">Đà Nẵng</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Phú Quốc">Phú Quốc</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Du lịch nước ngoài</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>tours?type=2&search=Trung Quốc">Trung Quốc</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=2&search=Thái Lan">Thái Lan</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=2&search=Hàn Quốc">Hàn Quốc</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=2&search=Nhật Bản">Nhật Bản</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Liên hệ</h4>
                    <p>190 Pasteur, Phường Xuân Hoà, TP. Hồ Chí Minh</p>
                    <p>(+84 28) 3822 8898</p>
                    <p>info@attravel.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 AT Travel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Gallery images
        const galleryImages = <?= json_encode(array_map(function($img) use ($tourImageModel) {
            return $tourImageModel->getImageUrl($img['DuongDan']);
        }, array_slice($tour['images'] ?? [], 0, 5))) ?>;
        
        let currentImageIndex = 0;
        const prices = {
            adult: <?= $tour['price']['GiaNguoiLon'] ?? 0 ?>,
            child: <?= $tour['price']['GiaTreEm'] ?? 0 ?>,
            infant: 0
        };
    </script>
    <script src="<?= BASE_URL ?>public/assets/js/frontend-detail.js"></script>
</body>
</html>

