<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-map-signs"></i> HDV PORTAL</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3"><i class="fas fa-user-circle"></i> Xin chào, <?= $data['guideName'] ?></span>
                <a href="<?= BASE_URL ?>auth/logout" class="btn btn-sm btn-light text-primary fw-bold">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <h3 class="text-secondary">📅 Lịch Dẫn Tour Của Tôi</h3>
                <p class="text-muted">Danh sách các tour bạn được phân công trong thời gian tới.</p>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($data['schedules'])): ?>
                <?php foreach ($data['schedules'] as $sch): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="position-relative">
                                <img src="<?= BASE_URL . 'uploads/' . $sch['HinhAnh'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <?= $sch['SoNgay'] ?> Ngày
                                </span>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title text-primary fw-bold"><?= $sch['TenTour'] ?></h5>
                                <hr>
                                <div class="mb-2">
                                    <i class="far fa-calendar-alt text-danger w-25"></i> 
                                    <strong><?= date('d/m/Y', strtotime($sch['NgayKhoiHanh'])) ?></strong>
                                </div>
                                <div class="mb-2">
                                    <i class="far fa-clock text-success w-25"></i> 
                                    <?= date('H:i', strtotime($sch['GioTapTrung'])) ?>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt text-info w-25"></i> 
                                    <?= $sch['DiaDiemTapTrung'] ?>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-users text-secondary w-25"></i> 
                                    Khách hiện tại: <strong><?= $sch['SoKhachHienTai'] ?></strong>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-outline-primary">
                                        <i class="fas fa-list-ol"></i> Xem danh sách khách
                                    </button>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-muted small">
                                Mã lịch: <?= $sch['LichCode'] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="150" class="mb-3 opacity-50">
                    <h4 class="text-muted">Hiện tại bạn chưa có lịch dẫn tour nào.</h4>
                    <p>Vui lòng liên hệ điều hành để biết thêm chi tiết.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>