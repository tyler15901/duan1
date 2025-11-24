<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- NHÚNG CSS TỪ FILE STYLES.CSS CỦA BẠN --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }

        /* Header (Dùng chung) */
        .header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; }
        .logo a { font-size: 24px; font-weight: bold; color: #0066CC; }
        .menu ul { display: flex; gap: 30px; }
        .menu a:hover, .menu a.active { color: #FF6600; border-bottom: 2px solid #FF6600; }
        .auth-buttons a { padding: 8px 20px; border-radius: 5px; margin-left: 10px; font-weight: 500; }
        .btn-login { color: #0066CC; border: 1px solid #0066CC; }
        .btn-register { background: #FF6600; color: #fff; }

        /* Breadcrumbs */
        .breadcrumbs { background: #fff; padding: 15px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .breadcrumbs a { color: #0066CC; }
        .breadcrumbs span { margin: 0 10px; color: #999; }

        /* Main Content */
        .main-content { padding: 30px 0; }
        .tour-title-main { font-size: 28px; color: #333; margin-bottom: 30px; font-weight: 700; line-height: 1.3; }
        .content-wrapper { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }

        /* Gallery */
        .gallery-section { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .main-image-wrapper { position: relative; width: 100%; height: 450px; border-radius: 10px; overflow: hidden; margin-bottom: 15px; }
        .main-image { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .gallery-badge { position: absolute; top: 15px; left: 15px; background: #ff4444; color: #fff; padding: 5px 12px; border-radius: 5px; font-weight: 600; font-size: 13px; }
        
        .gallery-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background: rgba(255,255,255,0.8); border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #333; transition: 0.3s; }
        .gallery-nav:hover { background: #fff; }
        .gallery-nav.prev { left: 15px; }
        .gallery-nav.next { right: 15px; }

        .thumbnail-gallery { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
        .thumbnail { width: 100px; height: 70px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid transparent; opacity: 0.7; transition: 0.3s; flex-shrink: 0; }
        .thumbnail:hover, .thumbnail.active { border-color: #0066CC; opacity: 1; }

        /* Booking Sidebar */
        .booking-sidebar { position: sticky; top: 90px; height: fit-content; }
        .booking-card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .booking-title { font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        
        .date-selection, .participant-selection { margin-bottom: 20px; border-bottom: 1px solid #f5f5f5; padding-bottom: 20px; }
        .section-label { font-size: 14px; font-weight: 600; margin-bottom: 10px; display: block; }
        
        .date-buttons { display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto; }
        .date-btn { padding: 8px 12px; border: 1px solid #ddd; background: #fff; border-radius: 5px; cursor: pointer; font-size: 13px; }
        .date-btn:hover, .date-btn.active { background: #0066CC; color: #fff; border-color: #0066CC; }
        .date-btn.disabled { background: #f9f9f9; color: #ccc; cursor: not-allowed; border-color: #eee; }

        .participant-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .participant-info { display: flex; flex-direction: column; }
        .participant-label { font-size: 14px; font-weight: 500; }
        .participant-price { font-size: 12px; color: #666; }
        
        .quantity-control { display: flex; align-items: center; gap: 10px; }
        .qty-btn { width: 30px; height: 30px; border: 1px solid #ddd; background: #fff; border-radius: 50%; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; }
        .qty-btn:hover { border-color: #0066CC; color: #0066CC; }
        .qty-value { font-weight: 600; width: 20px; text-align: center; }

        .price-summary { margin-top: 20px; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .total { border-top: 2px solid #eee; padding-top: 15px; margin-top: 10px; font-size: 18px; font-weight: 700; }
        .total-price { color: #FF6600; font-size: 24px; }
        
        .btn-booking { width: 100%; padding: 15px; background: #FF6600; color: #fff; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 15px; transition: 0.3s; }
        .btn-booking:hover { background: #e55a00; box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3); }

        /* Content Sections */
        .tour-section-block { background: #fff; border-radius: 10px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .block-title { font-size: 20px; font-weight: 700; margin-bottom: 20px; padding-left: 10px; border-left: 4px solid #0066CC; }
        
        /* Experiences */
        .experiences-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .experience-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; }
        .experience-item h3 { font-size: 16px; margin: 10px 0 5px; }
        .experience-item p { font-size: 13px; color: #666; }

        /* Tabs */
        .tabs-header { display: flex; border-bottom: 1px solid #eee; margin-bottom: 20px; overflow-x: auto; }
        .tab-btn { padding: 12px 20px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 600; color: #666; white-space: nowrap; }
        .tab-btn.active { color: #0066CC; border-bottom-color: #0066CC; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s; }
        .tab-content ul { padding-left: 20px; }
        .tab-content li { list-style-type: disc; margin-bottom: 8px; color: #555; }

        /* Related Tours */
        .related-tours-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .related-card { background: #fff; border: 1px solid #eee; border-radius: 10px; overflow: hidden; transition: 0.3s; }
        .related-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .related-img { width: 100%; height: 180px; object-fit: cover; }
        .related-info { padding: 15px; }
        .related-title { font-size: 15px; font-weight: 700; margin-bottom: 10px; display: block; }
        .related-price { color: #FF6600; font-weight: 700; font-size: 16px; }

        /* Footer */
        .footer { background: #e3f2fd; padding: 40px 0 20px; margin-top: 50px; }
        .footer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .footer h4 { color: #0066CC; margin-bottom: 15px; }
        .copyright { text-align: center; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px; font-size: 13px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 768px) { 
            .content-wrapper { grid-template-columns: 1fr; } 
            .booking-sidebar { position: static; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/../layout/header.php'; ?>

    <section class="breadcrumbs">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>">Trang chủ</a> <span>/</span>
            <a href="#"><?php echo $data['tour']['TenLoai'] ?? 'Chi tiết tour'; ?></a> <span>/</span>
            <span><?php echo htmlspecialchars($data['tour']['TenTour']); ?></span>
        </div>
    </section>

    <main class="main-content">
        <div class="container">
            <h1 class="tour-title-main"><?php echo htmlspecialchars($data['tour']['TenTour']); ?></h1>

            <div class="content-wrapper">
                <div class="left-column">
                    
                    <div class="gallery-section">
                        <div class="main-gallery">
                            <div class="main-image-wrapper">
                                <?php 
                                    $mainImg = !empty($data['tour']['gallery'][0]) ? BASE_URL . 'uploads/' . $data['tour']['gallery'][0] : 'https://placehold.co/800x500'; 
                                ?>
                                <img src="<?php echo $mainImg; ?>" id="mainImage" class="main-image" onerror="this.src='https://placehold.co/800x500'">
                                <div class="gallery-badge">
                                    <?php echo $data['tour']['TenLoai'] ?? 'Tour Hot'; ?> | <?php echo $data['tour']['SoNgay']; ?> Ngày
                                </div>
                                <button class="gallery-nav prev" onclick="changeImage(-1)"><i class="fas fa-chevron-left"></i></button>
                                <button class="gallery-nav next" onclick="changeImage(1)"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            
                            <div class="thumbnail-gallery">
                                <?php if (!empty($data['tour']['gallery'])): ?>
                                    <?php foreach ($data['tour']['gallery'] as $index => $img): ?>
                                        <img src="<?php echo BASE_URL . 'uploads/' . $img; ?>" 
                                             class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                             onclick="setMainImage(<?php echo $index; ?>)"
                                             onerror="this.src='https://placehold.co/100x80'">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tour-section-block" style="margin-top: 30px;">
                        <h2 class="block-title">Lịch trình chi tiết</h2>
                        
                        <?php if (!empty($data['itinerary'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                <?php foreach ($data['itinerary'] as $day): ?>
                                    <div style="border-left: 3px solid #0066CC; padding-left: 20px;">
                                        <h3 style="font-size: 18px; color: #333;">Ngày <?php echo $day['NgayThu']; ?>: <?php echo htmlspecialchars($day['TieuDe']); ?></h3>
                                        <p style="color: #666; margin-top: 5px;"><?php echo nl2br(htmlspecialchars($day['NoiDung'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>Đang cập nhật lịch trình...</p>
                        <?php endif; ?>
                    </div>

                    <div class="tour-section-block">
                        <div class="tabs-header">
                            <button class="tab-btn active" onclick="switchTab('desc')">Mô tả</button>
                            <button class="tab-btn" onclick="switchTab('policy')">Chính sách</button>
                            <button class="tab-btn" onclick="switchTab('note')">Lưu ý</button>
                        </div>
                        <div class="tabs-content">
                            <div id="tab-desc" class="tab-content active">
                                <p><?php echo nl2br(htmlspecialchars($data['tour']['MoTa'])); ?></p>
                            </div>
                            <div id="tab-policy" class="tab-content">
                                <ul>
                                    <li>Vé máy bay khứ hồi (nếu có)</li>
                                    <li>Xe đưa đón theo chương trình</li>
                                    <li>Khách sạn tiêu chuẩn 3-4 sao</li>
                                    <li>Bảo hiểm du lịch</li>
                                </ul>
                            </div>
                            <div id="tab-note" class="tab-content">
                                <ul>
                                    <li>Giá có thể thay đổi tùy thời điểm</li>
                                    <li>Mang theo giấy tờ tùy thân</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <aside class="booking-sidebar">
                    <div class="booking-card">
                        <h3 class="booking-title">Đặt Tour Ngay</h3>
                        
                        <div class="date-selection">
                            <span class="section-label">Chọn ngày khởi hành:</span>
                            <div class="date-buttons">
                                <?php if (!empty($data['schedules'])): ?>
                                    <?php foreach ($data['schedules'] as $index => $lich): ?>
                                        <?php 
                                            $conCho = $data['tour']['SoChoToiDa'] - $lich['SoKhachHienTai'];
                                            $isDisabled = $conCho <= 0;
                                            $class = $isDisabled ? 'disabled' : ($index === 0 ? 'active' : '');
                                            $ngayDi = date('d/m', strtotime($lich['NgayKhoiHanh']));
                                        ?>
                                        <button class="date-btn <?php echo $class; ?>" 
                                                onclick="selectDate(this, '<?php echo $lich['MaLichKhoiHanh']; ?>')"
                                                <?php echo $isDisabled ? 'disabled' : ''; ?>>
                                            <?php echo $ngayDi; ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="font-size: 13px; color: #999;">Chưa có lịch chạy.</p>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="selectedSchedule" value="<?php echo !empty($data['schedules'][0]) ? $data['schedules'][0]['MaLichKhoiHanh'] : ''; ?>">
                        </div>

                        <div class="participant-selection">
                            <div class="participant-item">
                                <div class="participant-info">
                                    <span class="participant-label">Người lớn</span>
                                    <span class="participant-price">x <?php echo number_format($data['tour']['GiaHienTai']); ?> ₫</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity('adult', -1)">-</button>
                                    <span class="qty-value" id="adultQty">1</span>
                                    <button class="qty-btn" onclick="changeQuantity('adult', 1)">+</button>
                                </div>
                            </div>
                            <div class="participant-item">
                                <div class="participant-info">
                                    <span class="participant-label">Trẻ em</span>
                                    <span class="participant-price">x <?php echo number_format($data['tour']['GiaHienTai'] * 0.8); ?> ₫</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity('child', -1)">-</button>
                                    <span class="qty-value" id="childQty">0</span>
                                    <button class="qty-btn" onclick="changeQuantity('child', 1)">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="price-summary">
                            <div class="price-row total">
                                <span>Tổng cộng:</span>
                                <span class="total-price" id="totalPrice">
                                    <?php echo number_format($data['tour']['GiaHienTai']); ?> ₫
                                </span>
                            </div>
                        </div>

                        <button class="btn-booking" onclick="submitBooking()">YÊU CẦU ĐẶT</button>
                    </div>
                </aside>
            </div>

            <section class="tour-section-block">
                <h2 class="block-title">Tour tương tự có thể bạn thích</h2>
                <div class="related-tours-grid">
                    <?php if (!empty($data['relatedTours'])): ?>
                        <?php foreach ($data['relatedTours'] as $relTour): ?>
                            <?php 
                                $relImg = !empty($relTour['HinhAnh']) ? BASE_URL . 'uploads/' . $relTour['HinhAnh'] : 'https://placehold.co/400x250';
                                $relPrice = !empty($relTour['GiaHienTai']) ? number_format($relTour['GiaHienTai']) . ' đ' : 'Liên hệ';
                            ?>
                            <div class="related-card">
                                <div style="position: relative; height: 180px; overflow: hidden;">
                                    <img src="<?php echo $relImg; ?>" class="related-img" onerror="this.src='https://placehold.co/400x250'">
                                </div>
                                <div class="related-info">
                                    <a href="<?php echo BASE_URL . 'tour/detail/' . $relTour['MaTour']; ?>" class="related-title">
                                        <?php echo htmlspecialchars($relTour['TenTour']); ?>
                                    </a>
                                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #666;">
                                        <span><i class="far fa-clock"></i> <?php echo $relTour['SoNgay']; ?> ngày</span>
                                        <span class="related-price"><?php echo $relPrice; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>Liên hệ</h4>
                    <p>Hotline: 1900 1080</p>
                    <p>Email: info@attravel.com</p>
                </div>
                <div class="footer-column">
                    <h4>Chứng nhận</h4>
                    <p>Đã thông báo Bộ Công Thương</p>
                </div>
                <div class="footer-column">
                    <h4>Thanh toán</h4>
                    <p>VISA, Master Card, Momo</p>
                </div>
            </div>
            <div class="copyright">&copy; 2025 AT Travel.</div>
        </div>
    </footer>

    <script>
        // 1. XỬ LÝ GALLERY ẢNH
        // Chuyển mảng PHP sang JS
        const galleryImages = [
            <?php 
                if (!empty($data['tour']['gallery'])) {
                    foreach ($data['tour']['gallery'] as $img) {
                        echo "'" . BASE_URL . 'uploads/' . $img . "',";
                    }
                } else {
                    // Nếu không có ảnh, dùng ảnh bìa hoặc placeholder
                    echo "'" . BASE_URL . 'uploads/' . ($data['tour']['HinhAnh'] ?? '') . "'";
                }
            ?>
        ];
        
        let currentIndex = 0;

        function setMainImage(index) {
            if (galleryImages.length > 0 && galleryImages[index]) {
                document.getElementById('mainImage').src = galleryImages[index];
                currentIndex = index;
                // Active thumbnail class
                document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });
            }
        }

        function changeImage(step) {
            let newIndex = currentIndex + step;
            if (newIndex < 0) newIndex = galleryImages.length - 1;
            if (newIndex >= galleryImages.length) newIndex = 0;
            setMainImage(newIndex);
        }

        // 2. XỬ LÝ TABS
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            document.getElementById('tab-' + tabId).classList.add('active');
            event.target.classList.add('active');
        }

        // 3. XỬ LÝ TÍNH TIỀN & ĐẶT TOUR
        const basePrice = <?php echo $data['tour']['GiaHienTai'] ?? 0; ?>;
        
        function changeQuantity(type, step) {
            const el = document.getElementById(type + 'Qty');
            let qty = parseInt(el.innerText) + step;
            if (qty < 0) qty = 0;
            el.innerText = qty;
            updateTotal();
        }

        function updateTotal() {
            const adult = parseInt(document.getElementById('adultQty').innerText);
            const child = parseInt(document.getElementById('childQty').innerText);
            
            // Logic: Người lớn 100%, Trẻ em 80%
            const total = (adult * basePrice) + (child * basePrice * 0.8);
            
            // Format tiền
            document.getElementById('totalPrice').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        }

        function selectDate(btn, scheduleId) {
            // Bỏ active cũ
            document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
            // Active mới
            btn.classList.add('active');
            // Lưu ID vào input hidden
            document.getElementById('selectedSchedule').value = scheduleId;
        }

        function submitBooking() {
            const scheduleId = document.getElementById('selectedSchedule').value;
            const adult = document.getElementById('adultQty').innerText;
            const child = document.getElementById('childQty').innerText;
            const totalText = document.getElementById('totalPrice').innerText;

            if (!scheduleId) {
                alert("Vui lòng chọn ngày khởi hành!");
                return;
            }

            if (adult == 0 && child == 0) {
                alert("Vui lòng chọn số lượng khách!");
                return;
            }

            // Chuyển hướng sang trang điền thông tin (Sẽ làm ở bước sau)
            // URL ví dụ: /booking/create?lich=1&adult=2&child=0
            const url = `<?php echo BASE_URL; ?>booking/create?lich_id=${scheduleId}&adult=${adult}&child=${child}`;
            window.location.href = url;
        }
    </script>

</body>
</html>