<!DOCTYPE html>
<html>

<head>
    <title>Admin Quản Lý Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <div class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh;">
            <h4>Tour Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_URL; ?>/tour/index">
                        <i class="bi bi-map"></i> Quản lý Tour
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_URL; ?>/schedule/index">
                        <i class="bi bi-calendar-check"></i> Lịch Khởi Hành
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_URL; ?>/supplier/index">
                        <i class="bi bi-truck"></i> Nhà Cung Cấp
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_URL; ?>/booking/index">
                        <i class="bi bi-receipt"></i> Quản lý Đơn Hàng
                    </a>
                </li>
                <li class="nav-item mt-2"> <a class="nav-link text-warning fw-bold border border-warning rounded"
                        href="<?php echo BASE_URL; ?>/booking/create">
                        <i class="bi bi-plus-circle-fill"></i> Tạo Đơn Hàng
                    </a>
                </li>
            </ul>
        </div>
        <div class="p-4 w-100">
            <?php require_once '../app/Views/' . $content_view . '.php'; ?>
        </div>
    </div>
</body>

</html>