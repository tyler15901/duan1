<div class="pagetitle">
    <h1>Chi tiết Vận hành</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/schedule/index">Lịch trình</a></li>
            <li class="breadcrumb-item active"><?php echo $schedule['LichCode']; ?></li>
        </ol>
    </nav>
</div>

<?php if (!empty($noti_id)): ?>
    <div class="alert alert-warning border-start border-5 border-warning shadow-sm d-flex align-items-center justify-content-between mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3 text-warning-emphasis">
                <i class="bi bi-bell-fill fs-4"></i>
            </div>
            <div>
                <h5 class="alert-heading fw-bold mb-1 text-dark">Nhiệm vụ cần xử lý</h5>
                <p class="mb-0 small text-dark">
                    Bạn đang truy cập từ thông báo hệ thống. <br>
                    Vui lòng kiểm tra và cập nhật <b>Hướng dẫn viên / Nhà cung cấp</b> bên dưới, sau đó bấm nút xác nhận.
                </p>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/dashboard/confirm_done/<?php echo $noti_id; ?>" class="btn btn-warning text-dark fw-bold shadow-sm px-4 py-2">
            <i class="bi bi-check-circle-fill me-2"></i> Xác nhận Đã xong
        </a>
    </div>
<?php endif; ?>

<section class="section profile">
    <div class="row">
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-bus-front fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1 text-center"><?php echo $schedule['LichCode']; ?></h5>
                    <span class="text-muted text-center small px-2"><?php echo $schedule['TenTour']; ?></span>
                    
                    <div class="w-100 mt-4">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small text-uppercase">Khởi hành</span>
                            <span class="fw-bold text-dark"><?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small text-uppercase">Kết thúc</span>
                            <span class="fw-bold text-dark"><?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small text-uppercase">Tập trung</span>
                            <span class="fw-bold text-primary"><?php echo date('H:i', strtotime($schedule['GioTapTrung'])); ?></span>
                        </div>

                        <div class="py-2 border-bottom bg-light px-3 rounded my-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Người lớn:</span>
                                <strong class="text-success"><?php echo number_format($schedule['GiaNguoiLon']); ?> đ</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Trẻ em:</span>
                                <strong class="text-info"><?php echo number_format($schedule['GiaTreEm']); ?> đ</strong>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between py-2 align-items-center">
                            <span class="text-muted small text-uppercase">Trạng thái</span>
                            <?php 
                                $badge = 'bg-secondary';
                                if ($schedule['TrangThai'] == 'Nhận khách') $badge = 'bg-success';
                                elseif ($schedule['TrangThai'] == 'Đang gom khách') $badge = 'bg-warning text-dark';
                                elseif ($schedule['TrangThai'] == 'Đã đóng sổ') $badge = 'bg-danger';
                                elseif ($schedule['TrangThai'] == 'Đang chạy') $badge = 'bg-primary';
                                elseif ($schedule['TrangThai'] == 'Hoàn tất') $badge = 'bg-info text-dark';
                            ?>
                            <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo $schedule['TrangThai']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Quản lý nhanh</h5>
                    <div class="d-grid gap-2">
                         <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i> Danh sách khách
                        </a>
                        <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-2"></i> Sửa thông tin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            
            <div class="card shadow-sm">
                <div class="card-body pt-3">
                    <h5 class="card-title">Tiến độ gom khách</h5>
                    <?php 
                        $current = (int)$schedule['SoKhachHienTai'];
                        $min = (int)$schedule['SoChoMin'];
                        $max = (int)$schedule['SoChoToiDa'];
                        
                        $percent = ($max > 0) ? round(($current / $max) * 100) : 0;
                        
                        // Màu thanh tiến độ
                        $barColor = 'bg-success';
                        if ($current < $min) $barColor = 'bg-warning'; // Chưa đủ khách
                        if ($percent >= 100) $barColor = 'bg-danger';  // Full chỗ
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2 align-items-end">
                        <div>
                            <span class="fw-bold fs-3 text-primary"><?php echo $current; ?></span>
                            <span class="text-muted">/ <?php echo $max; ?> khách</span>
                        </div>
                        
                        <span class="badge <?php echo ($current >= $min) ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            Min: <?php echo $min; ?>
                        </span>
                    </div>

                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar <?php echo $barColor; ?> progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: <?php echo $percent; ?>%">
                            <?php echo $percent; ?>%
                        </div>
                    </div>
                    
                    <?php if ($current < $min): ?>
                        <div class="alert alert-warning mt-3 mb-0 small py-2 border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Chưa đủ số lượng khách tối thiểu để khởi hành.
                        </div>
                    <?php else: ?>
                         <div class="alert alert-success mt-3 mb-0 small py-2 border-0 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill me-2"></i>Đã đủ điều kiện khởi hành. Vui lòng gán HDV.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body pt-3">
                    <ul class="nav nav-tabs nav-tabs-bordered">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#staffs">Hướng dẫn viên</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#resources">Xe & Dịch vụ</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        
                        <div class="tab-pane fade show active" id="staffs">
                            
                            <?php 
                                $hasStaff = !empty($staffs); 
                                $assignedIds = array_column($staffs, 'MaNhanSu');
                            ?>

                          

                            

                            <h6 class="text-muted small text-uppercase fw-bold mb-3 mt-4">Danh sách đang phụ trách</h6>
                            <?php if(empty($staffs)): ?>
                                <div class="text-center py-5 text-muted bg-light rounded border border-dashed">
                                    <i class="bi bi-person-x fs-1 d-block opacity-25 mb-2"></i>
                                    Chưa có HDV nào được phân công.
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                <?php foreach($staffs as $s): ?>
                                    <div class="col-md-6">
                                        <div class="card mb-0 shadow-sm border-0 bg-light">
                                            <div class="card-body p-3 d-flex align-items-center">
                                                <div class="bg-white rounded-circle p-3 me-3 shadow-sm text-primary">
                                                    <i class="bi bi-person-badge fs-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold text-dark"><?php echo $s['HoTen']; ?></h6>
                                                    <div class="text-muted small">
                                                        <i class="bi bi-telephone me-1"></i> <?php echo $s['SoDienThoai']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="resources">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted small text-uppercase fw-bold m-0">Dịch vụ đã gán</h6>
                                <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Cập nhật Dịch vụ
                                </a>
                            </div>

                            <?php if(empty($resources)): ?>
                                <div class="alert alert-light border text-center py-4">
                                    <i class="bi bi-box-seam fs-2 text-muted d-block mb-2"></i>
                                    Chưa có dịch vụ đi kèm.
                                </div>
                            <?php else: ?>
                                <div class="list-group shadow-sm">
                                <?php foreach($resources as $r): ?>
                                    <div class="list-group-item border-0 d-flex align-items-start px-3 py-3 mb-1 bg-light rounded">
                                        <div class="bg-info bg-opacity-10 rounded p-2 me-3 text-info">
                                            <i class="bi bi-box-seam fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark"><?php echo $r['TenTaiNguyen']; ?></h6>
                                            <span class="badge bg-white text-dark border me-2"><?php echo $r['LoaiCungCap']; ?></span>
                                            <small class="text-muted"><i class="bi bi-building me-1"></i> <?php echo $r['TenNhaCungCap']; ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>