<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>HDV - Travel Pro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

    <style>
        body { background-color: #f0f2f5; padding-bottom: 70px; /* Chừa chỗ cho menu dưới */ }
        
        /* Mobile Header */
        .guide-header {
            background: #fff;
            padding: 15px;
            position: sticky;
            top: 0; z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
        }
        
        /* Bottom Navigation Bar (Giống App) */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #fff;
            height: 60px;
            display: flex; justify-content: space-around; align-items: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .nav-item-link {
            text-decoration: none; color: #899bbd;
            display: flex; flex-direction: column; align-items: center;
            font-size: 10px; font-weight: 600;
        }
        .nav-item-link i { font-size: 22px; margin-bottom: 2px; }
        .nav-item-link.active { color: #4154f1; }
        
        /* Card styles mobile */
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="small text-muted" style="font-size: 10px; line-height: 1;">XIN CHÀO</div>
                <div class="fw-bold text-dark" style="line-height: 1;"><?php echo $_SESSION['fullname'] ?? 'HDV'; ?></div>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/auth/logout" class="text-danger"><i class="bi bi-box-arrow-right fs-4"></i></a>
    </div>

    <div class="container-fluid px-3 py-3">
        <?php 
            // Load nội dung view con
            if (file_exists('../app/Views/' . $content_view . '.php')) {
                require_once '../app/Views/' . $content_view . '.php'; 
            } else {
                echo '<div class="alert alert-danger">Không tìm thấy view: '.$content_view.'</div>';
            }
        ?>
    </div>

    <div class="bottom-nav">
        <a href="<?php echo BASE_URL; ?>/guide/index" class="nav-item-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'guide/index') !== false) ? 'active' : ''; ?>">
            <i class="bi bi-calendar2-week"></i>
            <span>Lịch trình</span>
        </a>
        
        <a href="#" class="nav-item-link text-primary">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" 
                 style="width: 45px; height: 45px; margin-top: -25px; border: 4px solid #fff;">
                <i class="bi bi-qr-code-scan fs-5"></i>
            </div>
        </a>

        <a href="#" class="nav-item-link">
            <i class="bi bi-person-circle"></i>
            <span>Cá nhân</span>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>