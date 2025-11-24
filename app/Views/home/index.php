<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php require_once __DIR__ . '/../layout/header.php'; ?>

    <section class="banner">
        <div class="banner-overlay"></div>
        <div class="container">
            <div class="banner-content">
                <div class="banner-left">
                    <h1 class="banner-title">Hơn 1000+ Tour,<br>Khám Phá Ngay</h1>
                    <p class="banner-subtitle">Giá tốt – hỗ trợ 24/7 – khắp nơi</p>
                    
                    <form action="index.php" method="GET" class="search-form">
                        <div class="form-group">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="q" placeholder="Bạn muốn đi đâu?" class="form-input">
                        </div>
                        <div class="form-group">
                            <i class="fas fa-calendar"></i>
                            <input type="text" placeholder="Ngày khởi hành" class="form-input" onfocus="(this.type='date')">
                        </div>
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i> TÌM TOUR NGAY
                        </button>
                    </form>
                </div>
                
                <?php if(!empty($data['tourUuDai'][0])): $top = $data['tourUuDai'][0]; ?>
                <div class="banner-right" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 30px; border-radius: 15px; color: #fff; border: 1px solid rgba(255,255,255,0.3); width: 350px; display: none;">
                    <style>@media (min-width: 1024px) { .banner-right { display: block !important; } }</style>
                    
                    <h3 style="color: #FFD700; margin-bottom: 10px; font-size: 1.5rem;"><?php echo htmlspecialchars($top['TenTour']); ?></h3>
                    <p class="tour-itinerary" style="margin-bottom: 15px;"><i class="far fa-clock"></i> <?php echo $top['SoNgay']; ?> Ngày trải nghiệm</p>
                    <div class="tour-price">
                        <span class="price-label">Giá chỉ từ</span>
                        <span class="price-value" style="font-size: 1.8rem; font-weight: bold; color: #FF6600; display: block;">
                            <?php echo $top['final_price']; ?> </span>
                    </div>
                    <a href="<?php echo $top['detail_link']; ?>" style="background: #fff; color: var(--primary); border: none; width: 40px; height: 40px; border-radius: 50%; margin-top: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="policies">
        <div class="container">
            <div class="policies-grid">
                <div class="policy-item">
                    <div class="policy-icon"><i class="fas fa-dollar-sign"></i></div>
                    <h3>Giá tour ưu đãi</h3>
                    <p>Nhiều khuyến mại, ưu đãi hấp dẫn.</p>
                </div>
                <div class="policy-item">
                    <div class="policy-icon"><i class="fas fa-award"></i></div>
                    <h3>Uy tín & Chuẩn mực</h3>
                    <p>Đối tác chọn lọc, đảm bảo chất lượng.</p>
                </div>
                <div class="policy-item">
                    <div class="policy-icon"><i class="fas fa-headset"></i></div>
                    <h3>Nhiệt tình & Tận tâm</h3>
                    <p>Hỗ trợ khách hàng 24/7 nhanh chóng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="tour-section">
        <div class="container">
            <h2 class="section-title">🔥 Tour Ưu Đãi</h2>
            <div class="tour-list">
                <?php if(!empty($data['tourUuDai'])): ?>
                    <?php foreach($data['tourUuDai'] as $t): ?>
                        <div class="tour-card">
                            <div class="tour-img-wrap">
                                <a href="<?php echo $t['detail_link']; ?>">
                                    <img src="<?php echo $t['final_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($t['TenTour']); ?>"
                                         onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                </a>
                                <span class="discount-tag"><?php echo $t['label']; ?></span>
                            </div>
                            <div class="tour-info">
                                <span class="tour-cat"><i class="fas fa-map-marker-alt"></i> Khởi hành từ HN/HCM</span>
                                <h3 class="tour-title">
                                    <a href="<?php echo $t['detail_link']; ?>">
                                        <?php echo htmlspecialchars($t['TenTour']); ?>
                                    </a>
                                </h3>
                                <div class="tour-meta">
                                    <span><i class="far fa-clock"></i> <?php echo $t['SoNgay']; ?> Ngày</span>
                                    <span><i class="fas fa-user-friends"></i> <?php echo $t['SoChoToiDa']; ?> chỗ</span>
                                </div>
                                <div class="tour-price"><?php echo $t['final_price']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="tour-section" id="trong-nuoc">
        <div class="container">
            <h2 class="section-title">🇻🇳 Tour Trong Nước</h2>
            <div class="tour-list">
                <?php if(!empty($data['tourTrongNuoc'])): ?>
                    <?php foreach($data['tourTrongNuoc'] as $t): ?>
                        <div class="tour-card">
                            <div class="tour-img-wrap">
                                <a href="<?php echo $t['detail_link']; ?>">
                                    <img src="<?php echo $t['final_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($t['TenTour']); ?>"
                                         onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                </a>
                            </div>
                            <div class="tour-info">
                                <h3 class="tour-title">
                                    <a href="<?php echo $t['detail_link']; ?>"><?php echo htmlspecialchars($t['TenTour']); ?></a>
                                </h3>
                                <div class="tour-meta">
                                    <span><i class="far fa-clock"></i> <?php echo $t['SoNgay']; ?> Ngày</span>
                                    <span><i class="fas fa-user-friends"></i> <?php echo $t['SoChoToiDa']; ?> chỗ</span>
                                </div>
                                <div class="tour-price"><?php echo $t['final_price']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="view-more">
                <a href="#" class="btn-view-more">Xem tất cả Tour Trong Nước <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <section class="tour-section" id="nuoc-ngoai">
        <div class="container">
            <h2 class="section-title">🌏 Tour Nước Ngoài</h2>
            <div class="tour-list">
                <?php if(!empty($data['tourQuocTe'])): ?>
                    <?php foreach($data['tourQuocTe'] as $t): ?>
                        <div class="tour-card">
                            <div class="tour-img-wrap">
                                <a href="<?php echo $t['detail_link']; ?>">
                                    <img src="<?php echo $t['final_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($t['TenTour']); ?>"
                                         onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                </a>
                            </div>
                            <div class="tour-info">
                                <h3 class="tour-title">
                                    <a href="<?php echo $t['detail_link']; ?>"><?php echo htmlspecialchars($t['TenTour']); ?></a>
                                </h3>
                                <div class="tour-meta">
                                    <span><i class="far fa-clock"></i> <?php echo $t['SoNgay']; ?> Ngày</span>
                                    <span><i class="fas fa-user-friends"></i> <?php echo $t['SoChoToiDa']; ?> chỗ</span>
                                </div>
                                <div class="tour-price"><?php echo $t['final_price']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="view-more">
                <a href="#" class="btn-view-more">Xem tất cả Tour Quốc Tế <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <section class="destinations" id="diem-den">
        <div class="container">
            <h2 class="section-title">Điểm Đến Hàng Đầu</h2>
            <div class="destinations-grid">
                <div class="destination-card"><img src="https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400"><div class="destination-overlay"><h3>Miền Bắc</h3></div></div>
                <div class="destination-card"><img src="https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=400"><div class="destination-overlay"><h3>Miền Nam</h3></div></div>
                <div class="destination-card"><img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400"><div class="destination-overlay"><h3>Miền Tây</h3></div></div>
                <div class="destination-card"><img src="https://images.unsplash.com/photo-1464817739973-48a2ac745a80?w=400"><div class="destination-overlay"><h3>Miền Trung</h3></div></div>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../layout/footer.php'; ?>

</body>
</html>