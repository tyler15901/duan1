<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- 1. CSS GỐC CỦA BẠN (Đã nhúng vào đây) --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; transition: 0.3s; }
        ul { list-style: none; }

        /* Header Styles (Giống trang chủ) */
        .header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; }
        .logo { font-size: 24px; font-weight: bold; color: #0066CC; }
        .menu ul { display: flex; gap: 20px; }
        .menu a:hover { color: #FF6600; }
        .auth-buttons a { padding: 8px 15px; border-radius: 5px; margin-left: 10px; font-weight: 600; }
        .btn-login { border: 1px solid #0066CC; color: #0066CC; }
        .btn-register { background: #FF6600; color: #fff; }

        /* Search Bar Horizontal */
        .search-bar { background: #e3f2fd; padding: 20px 0; }
        .search-form-horizontal { display: grid; grid-template-columns: 1fr auto; gap: 15px; max-width: 800px; margin: 0 auto; }
        .search-field { background: #fff; border-radius: 5px; padding: 12px 15px; display: flex; align-items: center; gap: 10px; flex: 1; }
        .search-field input { border: none; outline: none; width: 100%; font-size: 16px; }
        .btn-search-main { padding: 0 30px; background: #FF6600; color: #fff; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; }

        /* Breadcrumbs */
        .breadcrumbs { background: #fff; padding: 15px 0; border-bottom: 1px solid #eee; margin-bottom: 30px; font-size: 14px; }
        .breadcrumbs a { color: #0066CC; }
        
        /* Layout */
        .content-wrapper { display: grid; grid-template-columns: 280px 1fr; gap: 30px; padding-bottom: 50px; }

        /* Sidebar */
        .sidebar { background: #fff; border-radius: 10px; padding: 20px; height: fit-content; position: sticky; top: 100px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .sidebar-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .sidebar-title { font-size: 16px; margin-bottom: 15px; font-weight: 700; }
        .sidebar-list a { display: block; padding: 5px 0; color: #666; }
        .sidebar-list a:hover { color: #0066CC; padding-left: 5px; }

        /* List Section */
        .tour-list-section { background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .page-title { font-size: 24px; color: #333; }
        
        /* Sort Dropdown */
        .sort-dropdown select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; cursor: pointer; }

        /* Tour Card Horizontal */
        .tours-container { display: flex; flex-direction: column; gap: 20px; }
        .tour-card { display: grid; grid-template-columns: 300px 1fr; gap: 20px; border: 1px solid #eee; border-radius: 10px; overflow: hidden; transition: 0.3s; }
        .tour-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-color: #0066CC; }
        
        .tour-image-wrapper { height: 220px; position: relative; }
        .tour-image { width: 100%; height: 100%; object-fit: cover; }
        .tour-badge { position: absolute; top: 10px; left: 10px; background: #ff4444; color: #fff; padding: 4px 8px; font-size: 12px; border-radius: 3px; font-weight: bold; }
        
        .tour-info { padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .tour-title { font-size: 18px; margin-bottom: 10px; line-height: 1.4; }
        .tour-title a:hover { color: #0066CC; }
        
        .tour-details { display: flex; gap: 20px; font-size: 14px; color: #666; margin-bottom: 15px; }
        .tour-details i { color: #0066CC; margin-right: 5px; }
        
        .tour-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f5f5f5; }
        .tour-price-new { font-size: 22px; font-weight: 700; color: #FF6600; }
        .btn-view-tour { padding: 10px 25px; background: #FF6600; color: #fff; border-radius: 5px; font-weight: bold; }
        .btn-view-tour:hover { background: #e55a00; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 40px; }
        .page-number { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; }
        .page-number.active { background: #0066CC; color: #fff; border-color: #0066CC; }
        .page-number:hover:not(.active) { background: #f0f0f0; }

        /* Footer */
        .footer { background: #333; color: #ccc; padding: 40px 0; margin-top: 50px; text-align: center; }

        /* Responsive */
        @media(max-width: 768px) { 
            .content-wrapper { grid-template-columns: 1fr; }
            .sidebar { display: none; } /* Ẩn sidebar trên mobile cho gọn */
            .tour-card { grid-template-columns: 1fr; }
            .tour-image-wrapper { height: 200px; }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo"><a href="<?php echo BASE_URL; ?>">AT Travel</a></div>
                <nav class="menu">
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
                        <li><a href="<?php echo BASE_URL . 'tour/list'; ?>" class="active">Danh sách Tour</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="search-bar">
        <div class="container">
            <form action="<?php echo BASE_URL . 'tour/list'; ?>" method="GET" class="search-form-horizontal">
                <div class="search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($data['search']); ?>" placeholder="Tìm kiếm tour...">
                </div>
                <button type="submit" class="btn-search-main">Tìm kiếm</button>
            </form>
        </div>
    </section>

    <section class="breadcrumbs">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>">Trang chủ</a> / <span>Danh sách Tour</span>
        </div>
    </section>

    <main class="container">
        <div class="content-wrapper">
            
            <aside class="sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Sắp xếp</h3>
                    <ul class="sidebar-list">
                        <li><a href="?sort=newest&q=<?= $data['search'] ?>">✨ Mới nhất</a></li>
                        <li><a href="?sort=price-asc&q=<?= $data['search'] ?>">💰 Giá tăng dần</a></li>
                        <li><a href="?sort=price-desc&q=<?= $data['search'] ?>">💎 Giá giảm dần</a></li>
                        <li><a href="?sort=duration&q=<?= $data['search'] ?>">⏳ Thời gian tour</a></li>
                    </ul>
                </div>
                
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Loại Tour</h3>
                    <ul class="sidebar-list">
                        <li><a href="#">Tour Trong Nước</a></li>
                        <li><a href="#">Tour Quốc Tế</a></li>
                        <li><a href="#">Tour Theo Yêu Cầu</a></li>
                    </ul>
                </div>
            </aside>

            <div class="tour-list-section">
                <div class="section-header">
                    <div>
                        <h1 class="page-title">Kết quả tìm kiếm</h1>
                        <p style="color: #666;">Tìm thấy <strong><?php echo $data['totalTours']; ?></strong> tour phù hợp</p>
                    </div>
                    
                    <form method="GET" class="sort-dropdown">
                        <input type="hidden" name="q" value="<?= $data['search'] ?>">
                        <select name="sort" onchange="this.form.submit()">
                            <option value="newest" <?= $data['sort']=='newest'?'selected':'' ?>>Mới nhất</option>
                            <option value="price-asc" <?= $data['sort']=='price-asc'?'selected':'' ?>>Giá thấp -> cao</option>
                            <option value="price-desc" <?= $data['sort']=='price-desc'?'selected':'' ?>>Giá cao -> thấp</option>
                        </select>
                    </form>
                </div>

                <div class="tours-container">
                    <?php if(!empty($data['tours'])): ?>
                        <?php foreach($data['tours'] as $t): ?>
                            <div class="tour-card">
                                <div class="tour-image-wrapper">
                                    <a href="<?php echo $t['detail_link']; ?>">
                                        <img src="<?php echo $t['img_url']; ?>" alt="<?php echo htmlspecialchars($t['TenTour']); ?>" class="tour-image" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                    </a>
                                    <div class="tour-badge"><?php echo $t['TenLoai'] ?? 'Tour Hot'; ?></div>
                                </div>
                                <div class="tour-info">
                                    <div class="tour-header">
                                        <h3 class="tour-title">
                                            <a href="<?php echo $t['detail_link']; ?>"><?php echo htmlspecialchars($t['TenTour']); ?></a>
                                        </h3>
                                        <div class="tour-details">
                                            <div class="tour-detail-item"><i class="far fa-clock"></i> <?php echo $t['SoNgay']; ?> Ngày</div>
                                            <div class="tour-detail-item"><i class="fas fa-user-friends"></i> <?php echo $t['SoChoToiDa']; ?> chỗ</div>
                                        </div>
                                    </div>
                                    <div class="tour-footer">
                                        <div class="tour-price-section">
                                            <span class="tour-price-new"><?php echo $t['final_price']; ?></span>
                                        </div>
                                        <a href="<?php echo $t['detail_link']; ?>" class="btn-view-tour">Xem Tour <i class="fas fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px;">
                            <i class="fas fa-search" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
                            <p>Không tìm thấy tour nào phù hợp.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($data['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <a href="?page=<?= $i ?>&sort=<?= $data['sort'] ?>&q=<?= $data['search'] ?>" 
                           class="page-number <?= ($i == $data['currentPage']) ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2025 AT Travel. All rights reserved.</p>
    </footer>

</body>
</html>