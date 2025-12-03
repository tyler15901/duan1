<div class="pagetitle">
    <h1>Lịch trình phân công</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Guide</a></li>
            <li class="breadcrumb-item active">Danh sách Tour</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <?php if (empty($schedules)): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>Hiện tại bạn chưa có lịch trình nào sắp tới. Hãy nghỉ ngơi và nạp năng lượng nhé!</div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($schedules as $s): ?>
            <div class="col-lg-6">
                <div class="card mb-4 border-0 shadow-sm h-100">
                    <div class="row g-0 h-100">
                        <div class="col-md-4">
                            <?php $img = $s['HinhAnh'] ? BASE_URL.'/assets/uploads/'.$s['HinhAnh'] : 'https://via.placeholder.com/300x400'; ?>
                            <img src="<?php echo $img; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover; min-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title p-0 mb-1 text-primary fw-bold"><?php echo $s['TenTour']; ?></h5>
                                        <span class="badge bg-secondary mb-2"><?php echo $s['LichCode']; ?></span>
                                    </div>
                                    <?php 
                                        $badgeStatus = ($s['TrangThai'] == 'Đang chạy') ? 'bg-success' : 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $badgeStatus; ?>"><?php echo $s['TrangThai']; ?></span>
                                </div>

                                <div class="row mt-2 text-muted small">
                                    <div class="col-6 mb-2">
                                        <i class="bi bi-calendar-check text-success me-1"></i> 
                                        <span class="fw-bold">Đi:</span> <?php echo date('d/m/Y', strtotime($s['NgayKhoiHanh'])); ?>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <i class="bi bi-calendar-x text-danger me-1"></i> 
                                        <span class="fw-bold">Về:</span> <?php echo date('d/m/Y', strtotime($s['NgayKetThuc'])); ?>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        <span class="fw-bold">Tập trung:</span> <?php echo $s['GioTapTrung']; ?> tại <?php echo $s['DiaDiemTapTrung']; ?>
                                    </div>
                                    <div class="col-12">
                                        <i class="bi bi-people-fill text-info me-1"></i>
                                        <span class="fw-bold">Tổng khách:</span> <?php echo $s['SoKhachHienTai']; ?> pax
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <a href="<?php echo BASE_URL; ?>/guide/guests/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-primary flex-fill shadow-sm">
                                        <i class="bi bi-list-check me-1"></i> Danh sách Khách
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/guide/diary/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-outline-success flex-fill shadow-sm">
                                        <i class="bi bi-journal-richtext me-1"></i> Nhật ký Tour
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>