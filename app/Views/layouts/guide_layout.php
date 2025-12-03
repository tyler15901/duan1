<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thông tin Hướng Dẫn Viên</title>
    
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body>

    <header id="header" class="header fixed-top d-flex align-items-center bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?php echo BASE_URL; ?>/guide/index" class="logo d-flex align-items-center text-decoration-none">
                <i class="bi bi-compass-fill text-primary fs-3 me-2"></i>
                <span class="d-none d-lg-block fw-bold text-dark">GUIDE PORTAL</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn ms-3 fs-3 cursor-pointer text-primary"></i>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center list-unstyled m-0">
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0 text-decoration-none" href="#" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <span class="fw-bold"><?php echo substr($_SESSION['fullname'] ?? 'H', 0, 1); ?></span>
                        </div>
                        <span class="d-none d-md-block dropdown-toggle ps-2 text-dark"><?php echo $_SESSION['fullname'] ?? 'HDV'; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile shadow border-0">
                        <li><div class="dropdown-header"><h6><?php echo $_SESSION['fullname']; ?></h6><span>Hướng dẫn viên</span></div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i class="bi bi-box-arrow-right"></i> <span>Đăng xuất</span></a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <aside id="sidebar" class="sidebar bg-white shadow-sm">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'guide/index') !== false) ? '' : 'collapsed'; ?>" href="<?php echo BASE_URL; ?>/guide/index">
                    <i class="bi bi-calendar-event"></i><span>Lịch trình của tôi</span>
                </a>
            </li>
            <li class="nav-heading">Cá nhân</li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#">
                    <i class="bi bi-person"></i><span>Hồ sơ</span>
                </a>
            </li>
        </ul>
    </aside>

    <main id="main" class="main bg-light" style="min-height: 90vh;">
        <?php 
            if (file_exists('../app/Views/' . $content_view . '.php')) {
                require_once '../app/Views/' . $content_view . '.php'; 
            } else {
                echo '<div class="alert alert-danger">File view not found: '.$content_view.'</div>';
            }
        ?>
    </main>

    <footer id="footer" class="footer bg-white border-top py-3 text-center text-muted small">
        &copy; <?php echo date('Y'); ?> <strong><span>Travel Pro Guide System</span></strong>.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggleBtn = document.querySelector('.toggle-sidebar-btn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    document.body.classList.toggle('toggle-sidebar');
                });
            }
        });
    </script>
</body>
</html>