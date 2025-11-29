<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Tour Du lịch</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        /* --- Sidebar Styles --- */
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -16rem;
            transition: margin .25s ease-out;
            background: #1e293b;
            color: #fff;
        }

        /* Chỉnh sửa: Heading thành thẻ A để click được */
        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none; /* Bỏ gạch chân */
        }
        
        #sidebar-wrapper .sidebar-heading:hover {
            color: #fbbf24; /* Màu vàng khi hover */
        }

        #sidebar-wrapper .list-group {
            width: 16rem;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        .list-group-item {
            border: none;
            padding: 12px 20px;
            background-color: transparent;
            color: #cbd5e1;
            font-weight: 500;
            transition: all 0.3s;
        }

        .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding-left: 25px;
        }

        .list-group-item.active {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            border-left: 4px solid #fff;
        }

        .list-group-item i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar-divider {
            padding: 15px 20px 5px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* --- Page Content Styles --- */
        #page-content-wrapper {
            width: 100%;
            transition: margin .25s ease-out;
        }
        
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -16rem;
            }
        }
        
        .navbar-custom {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
            padding: 10px 20px;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <a href="<?php echo BASE_URL; ?>/dashboard/index" class="sidebar-heading">
                <i class="bi bi-airplane-engines-fill text-warning"></i> ADMIN PANEL
            </a>
            
            <div class="list-group list-group-flush mt-3">
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/dashboard/index">
                    <i class="bi bi-speedometer2"></i> Bảng điều khiển
                </a>

                <div class="sidebar-divider">Nghiệp vụ</div>
                
                <a class="list-group-item list-group-item-action fw-bold text-warning" href="<?php echo BASE_URL; ?>/booking/create">
                    <i class="bi bi-plus-circle-fill"></i> Tạo Đơn Nhanh
                </a>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/booking/index">
                    <i class="bi bi-receipt"></i> Quản lý Đơn Hàng
                </a>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/schedule/index">
                    <i class="bi bi-calendar-check"></i> Lịch Khởi Hành
                </a>

                <div class="sidebar-divider mt-3">Thống kê</div>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/report/index">
                    <i class="bi bi-graph-up-arrow"></i> Báo cáo Doanh thu
                </a>

                <div class="sidebar-divider mt-3">Quản trị</div>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/tour/index">
                    <i class="bi bi-map"></i> Danh sách Tour
                </a>
                
                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/staff/index">
                    <i class="bi bi-person-badge"></i> Hướng Dẫn Viên
                </a>

                <a class="list-group-item list-group-item-action" href="<?php echo BASE_URL; ?>/supplier/index">
                    <i class="bi bi-truck"></i> Nhà Cung Cấp
                </a>
                
                <a class="list-group-item list-group-item-action text-danger mt-5" href="<?php echo BASE_URL; ?>/auth/logout">
                    <i class="bi bi-box-arrow-left"></i> Đăng xuất
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <button class="btn btn-outline-secondary btn-sm" id="sidebarToggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-dark fw-bold d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo !empty($_SESSION['avatar']) ? BASE_URL . '/assets/uploads/' . $_SESSION['avatar'] : 'https://ui-avatars.com/api/?name=' . ($_SESSION['fullname'] ?? 'Admin') . '&background=0D8ABC&color=fff'; ?>" class="rounded-circle me-2" width="32" height="32">
                                <span><?php echo $_SESSION['fullname'] ?? 'Quản trị viên'; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Cài đặt</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <?php require_once '../app/Views/' . $content_view . '.php'; ?>
            </div>
            
            <footer class="text-center text-muted small py-3 mt-auto"> POLY TRAVEL</footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const sidebarToggle = document.body.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                document.getElementById('wrapper').classList.toggle('toggled');
            });
        }

        // Script tự động Active Menu dựa trên URL
        document.addEventListener("DOMContentLoaded", function() {
            const currentLocation = window.location.href;
            const menuItems = document.querySelectorAll('.list-group-item');
            
            menuItems.forEach(item => {
                if(currentLocation.includes(item.href)) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>