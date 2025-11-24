<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= BASE_URL ?>adminTour/index" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left"></i> Về danh sách Tour
                </a>
                <h3 class="text-primary">Quản lý Lịch Khởi Hành</h3>
                <h5 class="text-muted">Tour: <?= $data['tour']['TenTour'] ?></h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-plus"></i> Thêm ngày khởi hành mới
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>adminSchedule/store" method="POST">
                            <input type="hidden" name="tour_id" value="<?= $data['tour']['MaTour'] ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ngày khởi hành:</label>
                                <input type="date" name="start_date" class="form-control" required
                                    min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Giờ tập trung:</label>
                                <input type="time" name="time" class="form-control" value="06:00" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Điểm đón:</label>
                                <input type="text" name="location" class="form-control"
                                    placeholder="VD: Sân bay Nội Bài" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold">
                                LƯU LỊCH TRÌNH
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-calendar-alt"></i> Danh sách các ngày khởi hành
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ngày đi</th>
                                    <th>Giờ & Địa điểm</th>
                                    <th>Khách</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['schedules'])): ?>
                                    <?php foreach ($data['schedules'] as $sch): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    <?= date('d/m/Y', strtotime($sch['NgayKhoiHanh'])) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted"><?= $sch['LichCode'] ?></small>
                                            </td>
                                            <td>
                                                <i class="far fa-clock"></i>
                                                <?= date('H:i', strtotime($sch['GioTapTrung'])) ?><br>
                                                <i class="fas fa-map-marker-alt"></i> <?= $sch['DiaDiemTapTrung'] ?>
                                            </td>
                                            <td>
                                                <?= $sch['SoKhachHienTai'] ?> / <?= $data['tour']['SoChoToiDa'] ?>
                                            </td>
                                            <td>
                                                <?php if ($sch['SoKhachHienTai'] >= $data['tour']['SoChoToiDa']): ?>
                                                    <span class="badge bg-danger">Full</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Nhận khách</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($sch['SoKhachHienTai'] == 0): ?>
                                                    <a href="<?= BASE_URL ?>adminAllocation/index/<?= $sch['MaLichKhoiHanh'] ?>"
                                                        class="btn btn-sm btn-warning text-dark" title="Phân công HDV">
                                                        <i class="fas fa-user-tag"></i> HDV
                                                    </a>
                                                    <a href="<?= BASE_URL ?>adminSchedule/delete/<?= $sch['MaLichKhoiHanh'] ?>"
                                                        class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Đã có khách đặt"><i
                                                            class="fas fa-lock"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Chưa có lịch khởi hành nào. Hãy thêm ở cột bên trái.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>