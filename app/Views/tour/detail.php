<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <style>
        /* --- CSS Global --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f8; margin: 0; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        a { text-decoration: none; color: inherit; transition: 0.3s; }
        
        /* Breadcrumb */
        .breadcrumb { margin-bottom: 20px; font-size: 0.9rem; color: #666; }
        .breadcrumb a { color: #0056b3; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }

        /* Banner Ảnh Chính */
        .banner-wrap { position: relative; width: 100%; height: 450px; border-radius: 12px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .banner-img { width: 100%; height: 100%; object-fit: cover; }
        .tour-badge { position: absolute; top: 20px; right: 20px; background: #e74c3c; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2); text-transform: uppercase; font-size: 0.85rem; }

        /* Layout 2 cột */
        .tour-detail-layout { display: flex; gap: 30px; flex-wrap: wrap; }
        .main-content { flex: 2; min-width: 60%; }
        .sidebar { flex: 1; min-width: 300px; }

        /* Nội dung chính */
        h1 { font-size: 2rem; color: #2c3e50; margin-bottom: 15px; line-height: 1.3; }
        
        .tour-stats { display: flex; gap: 15px; margin-bottom: 25px; font-size: 0.95rem; color: #555; flex-wrap: wrap; }
        .stat-item { background: white; padding: 8px 15px; border-radius: 8px; border: 1px solid #e0e0e0; display: flex; align-items: center; gap: 8px; }
        .stat-item i { color: #0056b3; }

        .section-title { font-size: 1.4rem; color: #0056b3; margin: 35px 0 15px; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; font-weight: 700; }
        .description { line-height: 1.7; color: #444; text-align: justify; font-size: 1.05rem; }

        /* --- GALLERY (THƯ VIỆN ẢNH) --- */
        .gallery-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
            gap: 12px; 
            margin-bottom: 30px; 
        }
        .gallery-item { 
            height: 120px; 
            border-radius: 8px; 
            overflow: hidden; 
            cursor: pointer; 
            border: 2px solid transparent; 
            transition: 0.3s; 
            position: relative;
        }
        .gallery-item:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #0056b3; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }

        /* Lịch trình */
        .itinerary-list { position: relative; border-left: 3px solid #e9ecef; margin-left: 10px; padding-left: 25px; }
        .itinerary-item { margin-bottom: 30px; position: relative; }
        .itinerary-item::before { content: ''; position: absolute; left: -34px; top: 0; width: 15px; height: 15px; background: #0056b3; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 0 1px #ddd; }
        .day-header { font-weight: bold; font-size: 1.1rem; color: #2c3e50; margin-bottom: 8px; }
        .day-content { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); line-height: 1.6; border: 1px solid #eee; }

        /* Sidebar & Booking Box */
        .booking-box { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); position: sticky; top: 20px; border: 1px solid #eee; }
        
        .price-wrapper { text-align: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px dashed #ddd; }
        .price-label { font-size: 0.9rem; color: #777; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .current-price { font-size: 2.2rem; color: #d32f2f; font-weight: bold; }

        .schedule-list { max-height: 450px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 8px; background: #fdfdfd; }
        .schedule-item { padding: 15px; border-bottom: 1px solid #eee; transition: background 0.2s; }
        .schedule-item:last-child { border-bottom: none; }
        .schedule-item:hover { background: #f0f7ff; }
        
        .schedule-date { font-weight: bold; color: #333; font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center; }
        .seat-status { font-size: 0.8rem; padding: 3px 10px; border-radius: 12px; font-weight: 600; }
        .status-green { background: #e8f5e9; color: #2e7d32; }
        .status-red { background: #ffebee; color: #c62828; }
        
        .schedule-info { font-size: 0.9rem; color: #555; margin-top: 8px; line-height: 1.4; }
        .schedule-code { font-size: 0.75rem; color: #999; margin-top: 5px; font-family: monospace; text-align: right; }
        
        .btn-book { display: block; width: 100%; padding: 12px; margin-top: 12px; background: #0056b3; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; text-align: center; font-size: 0.95rem; }
        .btn-book:hover { background: #004494; box-shadow: 0 4px 10px rgba(0,86,179,0.3); transform: translateY(-1px); }
        .btn-book.disabled { background: #e0e0e0; color: #999; cursor: not-allowed; box-shadow: none; transform: none; }
        
        /* Scrollbar đẹp cho danh sách lịch */
        .schedule-list::-webkit-scrollbar { width: 6px; }
        .schedule-list::-webkit-scrollbar-track { background: #f1f1f1; }
        .schedule-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
        .schedule-list::-webkit-scrollbar-thumb:hover { background: #bbb; }

    </style>
</head>
<body>

<div class="container">
    <div class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>">Trang chủ</a> <span style="margin: 0 5px;">/</span> 
        <span><?php echo htmlspecialchars($data['tour']['TenTour']); ?></span>
    </div>

    <?php 
        // URL ảnh Banner (Trỏ vào public/uploads/)
        $bannerUrl = BASE_URL . 'uploads/' . ($data['tour']['HinhAnh'] ?? '');
    ?>
    <div class="banner-wrap">
        <img src="<?php echo $bannerUrl; ?>" 
             class="banner-img" 
             alt="<?php echo htmlspecialchars($data['tour']['TenTour']); ?>"
             onerror="this.src='https://placehold.co/1200x450?text=Dang+cap+nhat+anh'">
        
        <div class="tour-badge"><?php echo $data['tour']['TenLoai'] ?? 'Tour Hot'; ?></div>
    </div>

    <div class="tour-detail-layout">
        <div class="main-content">
            <h1><?php echo htmlspecialchars($data['tour']['TenTour']); ?></h1>
            
            <div class="tour-stats">
                <div class="stat-item">⏳ <?php echo $data['tour']['SoNgay']; ?> ngày</div>
                <div class="stat-item">👤 Tối đa <?php echo $data['tour']['SoChoToiDa']; ?> khách</div>
                <div class="stat-item">👁️ 1.2k lượt xem</div>
            </div>

            <h2 class="section-title">Mô tả chương trình</h2>
            <div class="description">
                <?php echo nl2br(htmlspecialchars($data['tour']['MoTa'])); ?>
            </div>

            <?php if (!empty($data['tour']['gallery'])): ?>
                <h2 class="section-title">📸 Thư viện ảnh thực tế</h2>
                <div class="gallery-grid">
                    <?php foreach ($data['tour']['gallery'] as $img): ?>
                        <div class="gallery-item">
                            <img src="<?php echo BASE_URL . 'uploads/' . $img; ?>" 
                                 alt="Ảnh chi tiết tour"
                                 onerror="this.src='https://placehold.co/200x150?text=No+Image'">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 class="section-title">🗺️ Lịch trình chi tiết</h2>
            <div class="itinerary-list">
                <?php if (!empty($data['itinerary'])): ?>
                    <?php foreach ($data['itinerary'] as $day): ?>
                        <div class="itinerary-item">
                            <div class="day-header">Ngày <?php echo $day['NgayThu']; ?>: <?php echo htmlspecialchars($day['TieuDe']); ?></div>
                            <div class="day-content">
                                <?php echo nl2br(htmlspecialchars($day['NoiDung'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #777; font-style: italic;">Đang cập nhật lịch trình chi tiết...</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sidebar">
            <div class="booking-box">
                <div class="price-wrapper">
                    <div class="price-label">Giá trọn gói / khách</div>
                    <div class="current-price">
                        <?php 
                            if (!empty($data['tour']['GiaHienTai']) && $data['tour']['GiaHienTai'] > 0) {
                                echo number_format($data['tour']['GiaHienTai'], 0, ',', '.') . ' đ';
                            } else {
                                echo "Liên hệ";
                            }
                        ?>
                    </div>
                </div>

                <h3 style="margin-bottom: 15px; color: #333;">📅 Chọn ngày khởi hành</h3>
                
                <div class="schedule-list">
                    <?php if (!empty($data['schedules'])): ?>
                        <?php foreach ($data['schedules'] as $lich): ?>
                            <?php 
                                $choDaDat = $lich['SoKhachHienTai'];
                                $choToiDa = $data['tour']['SoChoToiDa'];
                                $conLai = $choToiDa - $choDaDat;
                                $hetCho = $conLai <= 0;
                            ?>
                            <div class="schedule-item">
                                <div class="schedule-date">
                                    <?php echo date('d/m/Y', strtotime($lich['NgayKhoiHanh'])); ?>
                                    <?php if ($hetCho): ?>
                                        <span class="seat-status status-red">Hết chỗ</span>
                                    <?php else: ?>
                                        <span class="seat-status status-green">Còn <?php echo $conLai; ?> chỗ</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="schedule-info">
                                    <div>🕒 Tập trung: <strong><?php echo date('H:i', strtotime($lich['GioTapTrung'])); ?></strong></div>
                                    <div>📍 Tại: <?php echo $lich['DiaDiemTapTrung']; ?></div>
                                </div>
                                <div class="schedule-code">CODE: <?php echo $lich['LichCode']; ?></div>

                                <?php if (!$hetCho): ?>
                                    <a href="<?php echo BASE_URL . 'booking/create?lich_id=' . $lich['MaLichKhoiHanh']; ?>" class="btn-book">
                                        Đặt chỗ ngay
                                    </a>
                                <?php else: ?>
                                    <button class="btn-book disabled" disabled>Đã kín chỗ</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding: 30px; text-align: center; color: #777;">
                            <p>Hiện tại chưa có lịch khởi hành mới.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p style="font-size: 0.9rem; color: #555;">Cần hỗ trợ gấp?</p>
                    <a href="tel:19001080" style="font-weight: bold; color: #0056b3; font-size: 1.1rem;">📞 1900 1080</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>