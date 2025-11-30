<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Admin Pro</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body>
    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <a href="<?php echo BASE_URL; ?>/dashboard/index" class="sidebar-heading text-decoration-none text-white d-flex align-items-center gap-2">
                <div class="bg-primary rounded-3 p-1">
                    <i class="bi bi-send-fill text-white"></i> 
                </div>
                <span>TRAVEL PRO</span>
            </a>
            
            <div class="list-group list-group-flush mt-3">
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/dashboard/index">
                    <i class="bi bi-grid-1x2-fill"></i> Tổng quan
                </a>

                <div class="sidebar-divider">Điều hành</div>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/booking/create">
                    <i class="bi bi-plus-circle-fill text-primary"></i> <span class="text-white">Tạo Đơn Mới</span>
                </a>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/booking/index">
                    <i class="bi bi-receipt"></i> Đơn hàng
                </a>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/schedule/index">
                    <i class="bi bi-calendar2-week-fill"></i> Lịch khởi hành
                </a>

                <div class="sidebar-divider">Dữ liệu</div>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/tour/index">
                    <i class="bi bi-map-fill"></i> Sản phẩm Tour
                </a>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/supplier/index">
                    <i class="bi bi-buildings-fill"></i> Đối tác
                </a>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/staff/index">
                    <i class="bi bi-people-fill"></i> Nhân sự
                </a>

                <div class="sidebar-divider">Tài chính</div>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/report/index">
                    <i class="bi bi-bar-chart-fill"></i> Báo cáo
                </a>
            </div>
            
            <div class="mt-auto p-3 border-top border-secondary">
                <div class="d-flex align-items-center text-white">
                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['fullname'] ?? 'Admin'; ?>&background=random" class="rounded-circle me-2" width="40">
                    <div class="small">
                        <div class="fw-bold"><?php echo $_SESSION['fullname'] ?? 'Admin'; ?></div>
                        <div class="text-muted" style="font-size: 0.8em;">Quản trị viên</div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/auth/logout" class="ms-auto text-secondary hover-danger" title="Đăng xuất">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-custom border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-light" id="sidebarToggle"><i class="bi bi-list"></i></button>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <i class="bi bi-clock"></i> <?php echo date('H:i - d/m/Y'); ?>
                        </div>
                        <a href="<?php echo BASE_URL; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-globe"></i> Xem trang chủ
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <?php require_once '../app/Views/' . $content_view . '.php'; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar Script
        const sidebarToggle = document.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                document.getElementById('wrapper').classList.toggle('toggled');
            });
        }
        
        // Active Link Script
        const currentUrl = window.location.href;
        document.querySelectorAll('.list-group-item').forEach(link => {
            if(currentUrl.includes(link.href)) link.classList.add('active');
        });
    </script>
</body>
</html>