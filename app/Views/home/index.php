<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <style>
        /* CSS Reset cơ bản */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; color: #333; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        /* Header đơn giản */
        header { text-align: center; margin-bottom: 40px; padding-top: 20px; }
        header h1 { color: #0056b3; font-size: 2.5rem; margin-bottom: 10px; }
        header p { color: #666; font-size: 1.1rem; }

        /* Grid Layout cho Tour */
        .tour-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 25px; 
        }

        /* Card Tour Style */
        .tour-card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            display: flex; 
            flex-direction: column; 
        }
        
        .tour-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
        }

        .tour-img-wrap { position: relative; height: 200px; overflow: hidden; }
        .tour-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .tour-card:hover .tour-img { transform: scale(1.1); }
        
        /* Nhãn Loại Tour (Góc trên ảnh) */
        .tour-category {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0, 86, 179, 0.9); color: white;
            padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;
        }

        .tour-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        
        .tour-title { 
            font-size: 1.1rem; font-weight: bold; margin-bottom: 10px; 
            color: #2c3e50; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        .tour-meta { 
            font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; 
            display: flex; gap: 15px;
        }

        .tour-price-row { 
            margin-top: auto; /* Đẩy xuống đáy */
            display: flex; justify-content: space-between; align-items: center; 
            border-top: 1px solid #eee; padding-top: 15px;
        }

        .price { color: #e74c3c; font-size: 1.2rem; font-weight: bold; }
        .btn-detail { 
            text-decoration: none; background: #eaf2f8; color: #0056b3; 
            padding: 8px 15px; border-radius: 6px; font-weight: 600; transition: 0.2s; 
        }
        .btn-detail:hover { background: #0056b3; color: white; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Khám Phá Thế Giới</h1>
        <p>Hơn 100+ địa điểm du lịch hấp dẫn đang chờ đón bạn</p>
    </header>

    <div class="tour-grid">
        <?php if (!empty($data['tours'])): ?>
            <?php foreach ($data['tours'] as $tour): ?>
                <div class="tour-card">
                    <?php 
                        $dbPath = $tour['HinhAnh'] ?? ''; 
                        // Đường dẫn hiển thị trên web (URL)
                        $webUrl = BASE_URL . 'uploads/' . $dbPath;
                        // Đường dẫn kiểm tra file trên ổ cứng (Physical Path)
                        $checkPath = 'uploads/' . $dbPath; 
                        
                        // Nếu có tên ảnh và file tồn tại thật
                        if (!empty($dbPath) && file_exists($checkPath)) {
                            $imgSrc = $webUrl;
                        } else {
                            // Ảnh placeholder đẹp nếu lỗi
                            $imgSrc = 'https://placehold.co/600x400/e0e0e0/999999?text=No+Image';
                        }
                    ?>

                    <div class="tour-img-wrap">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($tour['TenTour']); ?>" class="tour-img">
                        <div class="tour-category"><?php echo $tour['TenLoai'] ?? 'Tour'; ?></div>
                    </div>
                    
                    <div class="tour-info">
                        <div class="tour-title">
                            <?php echo htmlspecialchars($tour['TenTour']); ?>
                        </div>
                        
                        <div class="tour-meta">
                            <span>🕒 <?php echo $tour['SoNgay']; ?> Ngày</span>
                            <span>👤 Max: <?php echo $tour['SoChoToiDa']; ?></span>
                        </div>

                        <div class="tour-price-row">
                            <div class="price">
                                <?php 
                                    if (!empty($tour['GiaHienTai']) && $tour['GiaHienTai'] > 0) {
                                        echo number_format($tour['GiaHienTai'], 0, ',', '.') . ' đ';
                                    } else {
                                        echo "Liên hệ";
                                    }
                                ?>
                            </div>
                            <a href="<?php echo BASE_URL . 'tour/detail/' . $tour['MaTour']; ?>" class="btn-detail">Đặt ngay</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1; font-size: 1.2rem; color: #888;">
                Hiện tại chưa có tour nào đang mở bán. Vui lòng quay lại sau!
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>