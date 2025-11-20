<?php
// View trang chủ frontend
require_once __DIR__ . '/../../app/models/TourImageModel.php';
require_once __DIR__ . '/../../app/models/TourPriceModel.php';
$tourImageModel = new TourImageModel();
$tourPriceModel = new TourPriceModel();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AT Travel - Đặt Tour Du Lịch Trong Nước & Quốc Tế</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/frontend-home.css">
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
                        <li><a href="<?= BASE_URL ?>tours?type=2">Tour nước ngoài</a></li>
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

    <!-- Banner -->
    <section class="banner">
        <div class="banner-overlay"></div>
        <div class="container">
            <div class="banner-content">
                <div class="banner-left">
                    <h1 class="banner-title">Hơn 1000+ Tour, Khám Phá Ngay</h1>
                    <p class="banner-subtitle">Giá tốt – hỗ trợ 24/7 – khắp nơi</p>
                    <div class="popularity-badge">
                        <i class="fas fa-bolt"></i>
                        <span>469 khách đặt trong 24h</span>
                    </div>
                    <div class="search-form">
                        <form action="<?= BASE_URL ?>tours" method="GET">
                            <div class="form-group">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" name="search" placeholder="Bạn muốn đi đâu?" class="form-input">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <i class="fas fa-calendar"></i>
                                    <input type="text" placeholder="Ngày khởi hành Linh hoạt" class="form-input">
                                </div>
                                <div class="form-group">
                                    <i class="fas fa-paper-plane"></i>
                                    <input type="text" value="Khởi hành từ Hồ Chí Minh" class="form-input">
                                </div>
                            </div>
                            <button type="submit" class="btn-search">
                                <i class="fas fa-search"></i> Tìm
                            </button>
                        </form>
                    </div>
                </div>
                <div class="banner-right">
                    <?php if (!empty($saleTours)): 
                        $featuredTour = $saleTours[0];
                        $mainImage = $tourImageModel->getMainImage($featuredTour['MaTour']);
                        $imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                        $price = $tourPriceModel->getCurrentPrice($featuredTour['MaTour']);
                    ?>
                    <div class="featured-tour">
                        <h3><?= htmlspecialchars($featuredTour['TenTour']) ?></h3>
                        <p class="tour-itinerary"><?= htmlspecialchars($featuredTour['MoTa'] ?? '') ?></p>
                        <div class="tour-price">
                            <span class="price-label">Giá chỉ từ</span>
                            <span class="price-value"><?= number_format($price['GiaNguoiLon'] ?? 0, 0, ',', '.') ?>đ/khách</span>
                        </div>
                        <a href="<?= BASE_URL ?>tour?id=<?= $featuredTour['MaTour'] ?>" class="btn-next-tour"><i class="fas fa-chevron-right"></i></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Policies/Features -->
    <section class="policies">
        <div class="container">
            <div class="policies-grid">
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Giá tour ưu đãi</h3>
                    <p>Với nhiều khuyến mại, ưu đãi hấp dẫn, khách hàng sẽ đặt được dịch vụ có giá tốt nhất.</p>
                </div>
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Uy tín & Chuẩn mực</h3>
                    <p>Liên kết chặt chẽ với các đối tác và khảo sát định kỳ để đảm bảo chất lượng tốt nhất.</p>
                </div>
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Nhiệt tình & Tận tâm</h3>
                    <p>Đặt lợi ích khách hàng lên trên hết, chúng tôi hỗ trợ khách hàng nhanh và chính xác nhất.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tour ưu đãi -->
    <?php if (!empty($saleTours)): ?>
    <section class="tour-section">
        <div class="container">
            <h2 class="section-title">Tour Ưu Đãi</h2>
            <div class="tour-slider">
                <button class="slider-btn prev" onclick="slideTour('sale', -1)"><i class="fas fa-chevron-left"></i></button>
                <div class="tour-list" id="sale-tours">
                    <?php foreach ($saleTours as $tour): 
                        $mainImage = $tourImageModel->getMainImage($tour['MaTour']);
                        $imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                        $price = $tourPriceModel->getCurrentPrice($tour['MaTour']);
                    ?>
                    <div class="tour-card">
                        <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($tour['TenTour']) ?>">
                        </a>
                        <div class="tour-card-content">
                            <h3><a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>"><?= htmlspecialchars($tour['TenTour']) ?></a></h3>
                            <p><?= htmlspecialchars(mb_substr($tour['MoTa'] ?? '', 0, 100)) ?>...</p>
                            <div class="tour-price-info">
                                <span class="price"><?= number_format($price['GiaNguoiLon'] ?? 0, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-btn next" onclick="slideTour('sale', 1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="view-more">
                <a href="<?= BASE_URL ?>tours" class="btn-view-more">Xem thêm <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Tour trong nước -->
    <?php if (!empty($domesticTours)): ?>
    <section class="tour-section">
        <div class="container">
            <h2 class="section-title">Tour Trong Nước</h2>
            <div class="tour-slider">
                <button class="slider-btn prev" onclick="slideTour('domestic', -1)"><i class="fas fa-chevron-left"></i></button>
                <div class="tour-list" id="domestic-tours">
                    <?php foreach ($domesticTours as $tour): 
                        $mainImage = $tourImageModel->getMainImage($tour['MaTour']);
                        $imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                        $price = $tourPriceModel->getCurrentPrice($tour['MaTour']);
                    ?>
                    <div class="tour-card">
                        <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($tour['TenTour']) ?>">
                        </a>
                        <div class="tour-card-content">
                            <h3><a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>"><?= htmlspecialchars($tour['TenTour']) ?></a></h3>
                            <p><?= htmlspecialchars(mb_substr($tour['MoTa'] ?? '', 0, 100)) ?>...</p>
                            <div class="tour-price-info">
                                <span class="price"><?= number_format($price['GiaNguoiLon'] ?? 0, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-btn next" onclick="slideTour('domestic', 1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="view-more">
                <a href="<?= BASE_URL ?>tours?type=1" class="btn-view-more">Xem thêm <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Tour nước ngoài -->
    <?php if (!empty($internationalTours)): ?>
    <section class="tour-section">
        <div class="container">
            <h2 class="section-title">Tour Nước Ngoài</h2>
            <div class="tour-slider">
                <button class="slider-btn prev" onclick="slideTour('international', -1)"><i class="fas fa-chevron-left"></i></button>
                <div class="tour-list" id="international-tours">
                    <?php foreach ($internationalTours as $tour): 
                        $mainImage = $tourImageModel->getMainImage($tour['MaTour']);
                        $imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                        $price = $tourPriceModel->getCurrentPrice($tour['MaTour']);
                    ?>
                    <div class="tour-card">
                        <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($tour['TenTour']) ?>">
                        </a>
                        <div class="tour-card-content">
                            <h3><a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>"><?= htmlspecialchars($tour['TenTour']) ?></a></h3>
                            <p><?= htmlspecialchars(mb_substr($tour['MoTa'] ?? '', 0, 100)) ?>...</p>
                            <div class="tour-price-info">
                                <span class="price"><?= number_format($price['GiaNguoiLon'] ?? 0, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-btn next" onclick="slideTour('international', 1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="view-more">
                <a href="<?= BASE_URL ?>tours?type=2" class="btn-view-more">Xem thêm <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-column">
                    <h4>Du lịch trong nước</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Hà Nội">Hà Nội</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Huế">Huế</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=1&search=Hạ Long">Hạ Long</a></li>
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
                    <p>190 Pasteur, Phường Xuân Hoà, TP. Hồ Chí Minh, Việt Nam</p>
                    <p>(+84 28) 3822 8898</p>
                    <p>info@attravel.com</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chat widget -->
    <div class="chat-widget">
        <i class="fas fa-comments"></i>
    </div>

    <script src="<?= BASE_URL ?>public/assets/js/frontend-home.js"></script>
</body>
</html>

