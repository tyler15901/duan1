<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="card border-0 shadow-sm bg-primary text-white mb-4 position-relative overflow-hidden">
    <div class="card-body p-4">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8">
                <h5 class="text-white-50 text-uppercase ls-1">Chi tiết Lịch khởi hành</h5>
                <h1 class="fw-bold mb-0"><?php echo $schedule['LichCode']; ?></h1>
                <p class="fs-5 mb-0 opacity-75"><?php echo $schedule['TenTour']; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-light text-primary fw-bold shadow-sm">
                    <i class="bi bi-pencil-fill"></i> Sửa lịch
                </a>
            </div>
        </div>
    </div>
    <i class="bi bi-airplane-engines position-absolute" style="font-size: 10rem; opacity: 0.1; right: -20px; bottom: -40px;"></i>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold text-muted text-uppercase mb-3">Thông tin vận hành</h6>
                
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <small class="text-muted d-block">Thời gian:</small>
                        <span class="fw-bold fs-5 text-dark">
                            <?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?>
                        </span>
                        <i class="bi bi-arrow-right mx-2 text-muted"></i>
                        <span class="fw-bold fs-5 text-dark">
                            <?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?>
                        </span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Tập trung:</small>
                        <strong><?php echo $schedule['GioTapTrung']; ?></strong> <br>
                        <?php echo $schedule['DiaDiemTapTrung']; ?>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Tình trạng chỗ:</small>
                        <div class="d-flex align-items-center mt-1">
                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                <?php 
                                    $percent = ($schedule['SoKhachHienTai'] / $schedule['SoChoToiDa']) * 100;
                                    $color = ($percent > 90) ? 'danger' : 'success';
                                ?>
                                <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <span class="fw-bold"><?php echo $schedule['SoKhachHienTai']; ?>/<?php echo $schedule['SoChoToiDa']; ?></span>
                        </div>
                    </li>
                    <li>
                        <small class="text-muted d-block">Trạng thái:</small>
                        <span class="badge bg-success mt-1"><?php echo $schedule['TrangThai']; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold py-3 border-bottom-0">
                        <i class="bi bi-person-badge text-warning me-2"></i> Hướng dẫn viên
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if(empty($staffs)): ?>
                            <div class="p-4 text-center text-muted bg-light m-3 rounded border border-dashed">
                                Chưa có HDV nào.
                            </div>
                        <?php else: ?>
                            <?php foreach($staffs as $s): ?>
                                <div class="list-group-item border-0 d-flex align-items-center px-4 py-3">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                        <i class="bi bi-person-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo $s['HoTen']; ?></h6>
                                        <small class="text-muted"><?php echo $s['SoDienThoai']; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold py-3 border-bottom-0">
                        <i class="bi bi-truck text-info me-2"></i> Xe & Khách sạn
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if(empty($resources)): ?>
                            <div class="p-4 text-center text-muted bg-light m-3 rounded border border-dashed">
                                Chưa phân bổ tài nguyên.
                            </div>
                        <?php else: ?>
                            <?php foreach($resources as $r): ?>
                                <div class="list-group-item border-0 d-flex align-items-start px-4 py-3">
                                    <div class="bg-info bg-opacity-10 text-info rounded p-2 me-3">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo $r['TenTaiNguyen']; ?></h6>
                                        <small class="text-muted d-block">NCC: <?php echo $r['TenNhaCungCap']; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body d-flex justify-content-center gap-3">
                        <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-primary bg-white px-4">
                            <i class="bi bi-people"></i> Quản lý Danh sách khách
                        </a>
                        <a href="<?php echo BASE_URL; ?>/schedule/expenses/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-success bg-white px-4">
                            <i class="bi bi-currency-dollar"></i> Quản lý Chi phí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>