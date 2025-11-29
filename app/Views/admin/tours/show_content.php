<style>
    /* CSS cho Timeline */
    .timeline {
        position: relative;
        padding-left: 20px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 30px;
        border-left: 2px solid #e9ecef;
        padding-left: 25px;
    }

    .timeline-item:last-child {
        border-left: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -11px;
        top: 0;
        width: 20px;
        height: 20px;
        background: #fff;
        border: 4px solid #0d6efd;
        border-radius: 50%;
    }

    .timeline-content {
        position: relative;
        top: -5px;
    }
</style>

<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>/tour/index" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 overflow-hidden h-100">
            <div class="position-relative" style="height: 300px;">
                <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" class="w-100 h-100"
                    style="object-fit: cover;">
                <div class="position-absolute bottom-0 start-0 w-100 p-4"
                    style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <span class="badge bg-warning text-dark mb-2"><?php echo $tour['TenLoai']; ?></span>
                    <h2 class="text-white fw-bold mb-0"><?php echo $tour['TenTour']; ?></h2>
                </div>
            </div>
            <div class="card-body">
                <h5 class="text-primary fw-bold"><i class="bi bi-stars"></i> Giới thiệu</h5>
                <p class="text-secondary" style="white-space: pre-line;"><?php echo $tour['MoTa']; ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Thông tin Tour</h5>
                    <?php if ($tour['TrangThai'] == 'Hoạt động'): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Tạm dừng</span>
                    <?php endif; ?>
                </div>

                <div class="list-group list-group-flush mb-4">
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-qr-code"></i> Mã Tour</span>
                        <span class="fw-bold font-monospace"><?php echo $tour['MaTour']; ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-clock"></i> Thời lượng</span>
                        <span class="fw-bold"><?php echo $tour['SoNgay']; ?> ngày</span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-people"></i> Chỗ tối đa</span>
                        <span class="fw-bold"><?php echo $tour['SoChoToiDa']; ?> khách</span>
                    </div>
                </div>

                <h6 class="fw-bold text-success mb-2">Giá tham khảo</h6>
                <div class="bg-light p-3 rounded mb-4">
                    <?php if (empty($prices)): ?>
                        <p class="text-muted small mb-0 fst-italic">Chưa cập nhật giá.</p>
                    <?php else: ?>
                        <?php foreach ($prices as $p): ?>
                            <div class="d-flex justify-content-between mb-1">
                                <span><?php echo $p['DoiTuong']; ?></span>
                                <strong class="text-danger"><?php echo number_format($p['Gia']); ?> đ</strong>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil-square"></i> Chỉnh sửa
                    </a>
                    <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>"
                        class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check"></i> Xem Lịch Khởi Hành
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-map"></i> Lịch trình chi tiết</h5>
            </div>
            <div class="card-body pt-4">
                <div class="timeline">
                    <?php foreach ($schedule as $day): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold text-primary mb-1">Ngày <?php echo $day['NgayThu']; ?>:
                                    <?php echo $day['TieuDe']; ?></h6>
                                <div class="text-muted bg-light p-3 rounded mt-2">
                                    <?php echo nl2br($day['NoiDung']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shield-exclamation"></i> Chính sách & Điều khoản</h5>
            </div>
            <div class="card-body">
                <div class="text-secondary small" style="white-space: pre-line;">
                    <?php echo $tour['ChinhSach']; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-images"></i> Thư viện ảnh</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if (!empty($gallery)): ?>
                        <?php foreach ($gallery as $img): ?>
                            <div class="col-6">
                                <img src="<?php echo BASE_URL . '/' . $img['DuongDan']; ?>"
                                    class="img-fluid rounded border w-100" style="height: 100px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center small">Chưa có ảnh thư viện.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>