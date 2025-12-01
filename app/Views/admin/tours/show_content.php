<div class="pagetitle">
    <h1>Chi tiết Tour</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/tour/index">Tour</a></li>
            <li class="breadcrumb-item active"><?php echo $tour['TenTour']; ?></li>
        </ol>
    </nav>
</div>

<section class="section profile">
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="mb-4 position-relative rounded overflow-hidden" style="height: 350px;">
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" class="w-100 h-100" style="object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <span class="badge bg-warning text-dark mb-2"><?php echo $tour['TenLoai']; ?></span>
                            <h2 class="text-white fw-bold mb-0"><?php echo $tour['TenTour']; ?></h2>
                        </div>
                    </div>

                    <div class="tab-content pt-2">
                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Giới thiệu</h5>
                        <p class="text-muted" style="white-space: pre-line;"><?php echo $tour['MoTa']; ?></p>

                        <h5 class="card-title mt-4"><i class="bi bi-map"></i> Lịch trình chi tiết</h5>
                        
                        <div class="activity">
                            <?php foreach ($schedule as $day): ?>
                            <div class="activity-item d-flex">
                                <div class="activity-label fw-bold text-dark" style="min-width: 80px;">Ngày <?php echo $day['NgayThu']; ?></div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                    <strong class="text-primary d-block mb-1"><?php echo $day['TieuDe']; ?></strong>
                                    <span class="text-muted"><?php echo nl2br($day['NoiDung']); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="card-title mt-4"><i class="bi bi-shield-check"></i> Chính sách</h5>
                        <div class="alert alert-light border">
                            <?php echo nl2br($tour['ChinhSach']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h5 class="fw-bold mb-3">Thông tin tóm tắt</h5>
                    
                    <div class="w-100">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Mã Tour:</span>
                            <span class="fw-bold font-monospace">#<?php echo $tour['MaTour']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Thời lượng:</span>
                            <span class="fw-bold"><?php echo $tour['SoNgay']; ?> ngày</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Số chỗ tối đa:</span>
                            <span class="fw-bold"><?php echo $tour['SoChoToiDa']; ?> khách</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 mb-3">
                            <span class="text-muted">Trạng thái:</span>
                            <?php if ($tour['TrangThai'] == 'Hoạt động'): ?>
                                <span class="badge bg-success">Đang hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tạm dừng</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h6 class="text-start w-100 fw-bold mt-2 text-primary">Bảng giá tham khảo</h6>
                    <div class="bg-light p-3 rounded w-100 mb-4">
                        <?php if (!empty($prices)): ?>
                            <?php foreach ($prices as $p): ?>
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?php echo $p['DoiTuong']; ?></span>
                                    <strong class="text-danger"><?php echo number_format($p['Gia']); ?> đ</strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Chưa cập nhật giá.</p>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2 w-100">
                        <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa Tour
                        </a>
                        <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-calendar-check"></i> Xem Lịch Khởi Hành
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body pt-3">
                    <h5 class="card-title">Thư viện ảnh</h5>
                    <div class="row g-2">
                        <?php if (!empty($gallery)): ?>
                            <?php foreach ($gallery as $img): ?>
                                <div class="col-4">
                                    <img src="<?php echo BASE_URL . '/' . $img['DuongDan']; ?>" class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small text-center">Chưa có ảnh thư viện.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>