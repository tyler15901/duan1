<?php
// View danh sách tour
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
    <title>Danh Sách Tour - AT Travel</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/frontend-list.css">
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
                        <li><a href="<?= BASE_URL ?>tours?type=1" class="<?= ($type ?? null) == 1 ? 'active' : '' ?>">Tour trong nước</a></li>
                        <li><a href="<?= BASE_URL ?>tours?type=2" class="<?= ($type ?? null) == 2 ? 'active' : '' ?>">Tour nước ngoài</a></li>
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

    <!-- Search Bar -->
    <section class="search-bar">
        <div class="container">
            <form action="<?= BASE_URL ?>tours" method="GET" class="search-form-horizontal">
                <div class="search-field">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="field-content">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Tìm kiếm tour...">
                        <span class="field-label"><?= count($tours ?? []) ?> Tours</span>
                    </div>
                </div>
                <div class="search-field">
                    <i class="fas fa-calendar"></i>
                    <div class="field-content">
                        <input type="text" placeholder="Ngày khởi hành" readonly>
                        <span class="field-label">Linh hoạt</span>
                    </div>
                </div>
                <div class="search-field">
                    <i class="fas fa-paper-plane"></i>
                    <div class="field-content">
                        <input type="text" placeholder="Khởi hành từ" readonly>
                        <span class="field-label">Tất cả</span>
                    </div>
                </div>
                <button type="submit" class="btn-search-main">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </form>
        </div>
    </section>

    <!-- Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container">
            <a href="<?= BASE_URL ?>home">Trang chủ</a>
            <span>/</span>
            <span>Danh Sách Tour</span>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="sidebar-section">
                        <h3 class="sidebar-title">Tour HOT Nước Ngoài</h3>
                        <ul class="sidebar-list">
                            <li><a href="<?= BASE_URL ?>tours?type=2&search=Trung Quốc">Trung Quốc</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=2&search=Thái Lan">Thái Lan</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=2&search=Nhật Bản">Nhật Bản</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=2&search=Hàn Quốc">Hàn Quốc</a></li>
                        </ul>
                    </div>

                    <div class="sidebar-section">
                        <h3 class="sidebar-title">Tour HOT Trong Nước</h3>
                        <ul class="sidebar-list">
                            <li><a href="<?= BASE_URL ?>tours?type=1&search=Sapa">Sapa</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=1&search=Đà Nẵng">Đà Nẵng</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=1&search=Phú Quốc">Phú Quốc</a></li>
                            <li><a href="<?= BASE_URL ?>tours?type=1&search=Hạ Long">Hạ Long</a></li>
                        </ul>
                    </div>
                </aside>

                <!-- Tour List -->
                <div class="tour-list-section">
                    <div class="section-header">
                        <div>
                            <h1 class="page-title">Danh Sách Tour</h1>
                            <p class="tour-count">Tổng cộng <?= count($tours ?? []) ?> Tour</p>
                        </div>
                        <div class="sort-dropdown">
                            <label>Sắp xếp theo:</label>
                            <select id="sortSelect" onchange="sortTours()">
                                <option value="recommended">Đề Xuất</option>
                                <option value="price-asc">Giá tăng dần</option>
                                <option value="price-desc">Giá giảm dần</option>
                                <option value="duration">Thời gian</option>
                            </select>
                        </div>
                    </div>

                    <div class="tours-container" id="toursContainer">
                        <?php if (!empty($tours)): ?>
                            <?php foreach ($tours as $tour): 
                                $mainImage = $tourImageModel->getMainImage($tour['MaTour']);
                                $imageUrl = $mainImage ? $tourImageModel->getImageUrl($mainImage['DuongDan']) : BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
                                $price = $tourPriceModel->getCurrentPrice($tour['MaTour']);
                            ?>
                            <div class="tour-card">
                                <div class="tour-image-wrapper">
                                    <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>">
                                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($tour['TenTour']) ?>" class="tour-image">
                                    </a>
                                </div>
                                <div class="tour-info">
                                    <div class="tour-header">
                                        <h3 class="tour-title">
                                            <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>"><?= htmlspecialchars($tour['TenTour']) ?></a>
                                        </h3>
                                        <div class="tour-details">
                                            <div class="tour-detail-item">
                                                <i class="far fa-clock"></i>
                                                <span><?= $tour['SoNgay'] ?> Ngày <?= $tour['SoNgay'] - 1 ?> Đêm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tour-footer">
                                        <div class="tour-price-section">
                                            <span class="tour-price-new"><?= number_format($price['GiaNguoiLon'] ?? 0, 0, ',', '.') ?> ₫</span>
                                        </div>
                                        <a href="<?= BASE_URL ?>tour?id=<?= $tour['MaTour'] ?>" class="btn-view-tour">Xem Tour <i class="fas fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Không tìm thấy tour nào.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                        <a href="<?= BASE_URL ?>tours?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="page-btn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                        <?php endif; ?>
                        <div class="page-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="<?= BASE_URL ?>tours?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                   class="page-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                        <?php if ($page < $totalPages): ?>
                        <a href="<?= BASE_URL ?>tours?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="page-btn">
                            Sau <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
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

    <script src="<?= BASE_URL ?>public/assets/js/frontend-list.js"></script>
</body>
</html>

