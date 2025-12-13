<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thông tin HDV - Travel Pro</title>

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
        }

        body {
            background-color: #f6f9ff;
            /* Xóa dòng overflow-x: hidden; đi để trình duyệt tự xử lý cuộn nếu cần */
        }

        /* Navbar Styles */
        .navbar-custom {
            height: var(--topbar-height);
            background-color: #fff;
            box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1);
            z-index: 1030;
            padding-left: 20px;
        }

        /* Sidebar Styles */
        #sidebar-wrapper {
            width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            background-color: #fff;
            box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1);
            transition: all 0.3s;
            z-index: 1020;
            overflow-y: auto; /* Cuộn dọc */
            overflow-x: hidden; /* [SỬA] Ẩn cuộn ngang của Sidebar */
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        /* Content Styles */
        #page-content-wrapper {
            /* [SỬA QUAN TRỌNG] Chiều rộng phải trừ đi sidebar để không bị tràn */
            width: calc(100% - var(--sidebar-width)); 
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            transition: all 0.3s;
            overflow-x: auto; /* Cho phép cuộn ngang nội dung nếu bảng quá dài */
        }

        /* Khi ẩn sidebar thì nội dung full màn hình */
        #wrapper.toggled #page-content-wrapper {
            margin-left: 0;
            width: 100%;
        }

        /* List Group Item Styles */
        .list-group-item {
            border: none;
            padding: 12px 20px;
            color: #4154f1;
            font-weight: 600;
            transition: 0.3s;
            white-space: nowrap; /* [SỬA] Giữ chữ trên 1 dòng để đẹp hơn */
        }

        .list-group-item:hover {
            color: #4154f1;
            background-color: #f6f9ff;
        }

        .list-group-item.active {
            background-color: #f6f9ff;
            color: #4154f1;
            border-right: 4px solid #4154f1;
        }

        .list-group-item i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        /* Responsive Mobile */
        @media (max-width: 992px) {
            #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width)); /* Mặc định ẩn trên mobile */
            }
            #page-content-wrapper {
                margin-left: 0;
                width: 100%; /* Full màn hình trên mobile */
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0; /* Hiện khi bấm nút toggle */
            }
            /* Khi hiện sidebar trên mobile, nội dung vẫn giữ nguyên (sidebar đè lên hoặc đẩy nhẹ) */
            #wrapper.toggled #page-content-wrapper {
                margin-left: 0; 
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="bi bi-list toggle-sidebar-btn d-lg-none me-3 fs-3 cursor-pointer text-primary" id="menu-toggle" style="cursor: pointer;"></i>
            
            <a href="<?php echo BASE_URL; ?>/guide/index" class="text-decoration-none d-flex align-items-center">
                <i class="bi bi-compass-fill text-primary fs-4 me-2"></i>
                <span class="d-none d-lg-block fw-bold text-primary fs-5">GUIDE PORTAL</span>
            </a>
        </div>

        <div class="dropdown me-4 ms-auto">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <?php 
                    $fullname = $_SESSION['fullname'] ?? 'HDV';
                    $avatar = isset($_SESSION['avatar']) && !empty($_SESSION['avatar']) 
                        ? BASE_URL . '/assets/uploads/' . $_SESSION['avatar'] 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($fullname) . '&background=0D6EFD&color=fff';
                ?>
                <img src="<?php echo $avatar; ?>" alt="user" width="36" height="36" class="rounded-circle me-2 border" style="object-fit: cover;">
                <span class="d-none d-md-inline fw-bold small text-secondary"><?php echo $fullname; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                <li><div class="dropdown-header text-center"><strong><?php echo $fullname; ?></strong><br><span class="small text-muted">Hướng dẫn viên</span></div></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <div id="wrapper">
        
        <div class="bg-white border-end" id="sidebar-wrapper">
            <div class="list-group list-group-flush py-3">
                <div class="small text-uppercase fw-bold text-muted px-4 mb-2" style="font-size: 0.75rem;">Quản lý Tour</div>
                
                <a class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'guide/index') !== false || strpos($_SERVER['REQUEST_URI'], 'guide/detail') !== false) ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>/guide/index">
                    <i class="bi bi-calendar-event"></i> Lịch trình của tôi
                </a>

                <div class="small text-uppercase fw-bold text-muted px-4 mb-2 mt-3" style="font-size: 0.75rem;">Cá nhân</div>

                <a class="list-group-item list-group-item-action" href="#">
                    <i class="bi bi-person-badge"></i> Hồ sơ cá nhân
                </a>
                
                <a class="list-group-item list-group-item-action text-danger mt-3" href="<?php echo BASE_URL; ?>/auth/logout">
                    <i class="bi bi-box-arrow-left"></i> Đăng xuất
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <div class="container-fluid px-4 py-4">
                <?php 
                    // Kiểm tra biến content_view có tồn tại không
                    if (isset($content_view)) {
                        // Đường dẫn tương đối từ file layout này (app/Views/layouts) đi ra app/Views/
                        $viewPath = '../app/Views/' . $content_view . '.php';
                        
                        if (file_exists($viewPath)) {
                            require_once $viewPath; 
                        } else {
                            echo '<div class="alert alert-danger shadow-sm">
                                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Lỗi View!</h4>
                                    <p>Không tìm thấy file giao diện: <strong>' . $content_view . '.php</strong></p>
                                    <hr>
                                    <p class="mb-0 small">Vui lòng kiểm tra lại Controller và tên file View.</p>
                                  </div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Chưa định nghĩa biến <code>$content_view</code> trong Controller.</div>';
                    }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script toggle sidebar
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        if(toggleButton){
            toggleButton.onclick = function () {
                el.classList.toggle("toggled");
            };
        }
    </script>
</body>
</html>