<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Tour - Travel Pro</title>

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top d-flex align-items-center justify-content-between">
        <a href="<?php echo BASE_URL; ?>/dashboard/index" class="sidebar-heading text-decoration-none border-0">
            <i class="bi bi-grid-fill"></i>
            <span class="d-none d-lg-block">TRAVEL PRO</span>
        </a>

        <i class="bi bi-list toggle-sidebar-btn d-lg-none ms-2 fs-3" id="menu-toggle"></i>

        <div class="search-bar ms-auto me-3 d-none d-md-block">
            <form class="d-flex align-items-center position-relative">
                <input class="form-control" type="text" placeholder="Tìm kiếm..." style="width: 250px; padding-left: 35px;">
                <i class="bi bi-search position-absolute text-muted" style="left: 10px;"></i>
            </form>
        </div>

        <div class="dropdown me-4">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['fullname'] ?? 'Admin'; ?>&background=0d6efd&color=fff" alt="mdo" width="36" height="36" class="rounded-circle me-2">
                <span class="d-none d-md-inline fw-bold small text-secondary"><?php echo $_SESSION['fullname'] ?? 'Admin'; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                <li><div class="dropdown-header"><h6>Xin chào, <?php echo $_SESSION['fullname'] ?? 'Admin'; ?>!</h6></div></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/profile/index"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
            </ul>
        </div>
    </nav>
    <div class="d-flex" id="wrapper" style="margin-top: 60px;">
        
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="list-group list-group-flush py-3">
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/dashboard/index">
                    <i class="bi bi-grid"></i> Dashboard
                </a>

                <div class="sidebar-divider">Điều hành</div>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'booking/create') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/booking/create">
                    <i class="bi bi-plus-circle"></i> Tạo Đơn Mới
                </a>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'booking/index') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/booking/index">
                    <i class="bi bi-receipt"></i> Đơn hàng
                </a>

                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'schedule') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/schedule/index">
                    <i class="bi bi-calendar-check"></i> Lịch khởi hành
                </a>

                <div class="sidebar-divider">Dữ liệu</div>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'tour/') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/tour/index">
                    <i class="bi bi-map"></i> Sản phẩm Tour
                </a>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'staff') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/staff/index">
                    <i class="bi bi-person-badge"></i> Hướng dẫn viên
                </a>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'supplier') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/supplier/index">
                    <i class="bi bi-buildings"></i> Đối tác / NCC
                </a>

                <div class="sidebar-divider">Thống kê</div>

                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'report') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/report/index">
                    <i class="bi bi-bar-chart"></i> Báo cáo
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <div class="container-fluid px-4 py-4">
                <?php 
                    if (file_exists('../app/Views/' . $content_view . '.php')) {
                        require_once '../app/Views/' . $content_view . '.php'; 
                    } else {
                        echo '<div class="alert alert-warning">File view not found: '.$content_view.'</div>';
                    }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
        // Script toggle sidebar đơn giản
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>
</html>