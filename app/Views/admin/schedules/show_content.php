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

<section class="section profile">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo $schedule['LichCode']; ?></h5>
                    <span class="text-muted text-center small"><?php echo $schedule['TenTour']; ?></span>
                    
                    <div class="w-100 mt-4">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Khởi hành:</span>
                            <span class="fw-bold text-dark"><?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Kết thúc:</span>
                            <span class="fw-bold text-dark"><?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Tập trung:</span>
                            <span class="fw-bold text-primary"><?php echo $schedule['GioTapTrung']; ?></span>
                        </div>
                        <div class="py-2 border-bottom">
                            <span class="text-muted d-block mb-1">Điểm đón:</span>
                            <span class="fw-bold small"><?php echo $schedule['DiaDiemTapTrung']; ?></span>
                        </div>

                        <div class="py-2 border-bottom bg-light px-3 rounded my-2">
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
                            <span class="text-muted">Trạng thái:</span>
                            <span class="badge bg-success"><?php echo $schedule['TrangThai']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quản lý nhanh</h5>
                    <div class="d-grid gap-2">
                         <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i> Danh sách khách
                        </a>
                        <a href="<?php echo BASE_URL; ?>/schedule/expenses/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-success">
                            <i class="bi bi-currency-dollar me-2"></i> Chi phí & Lãi
                        </a>
                        <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-outline-warning text-dark">
                            <i class="bi bi-pencil me-2"></i> Sửa thông tin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
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
                            <?php if(empty($staffs)): ?>
                                <div class="alert alert-warning">Chưa phân công HDV.</div>
                            <?php else: ?>
                                <div class="list-group">
                                <?php foreach($staffs as $s): ?>
                                    <div class="list-group-item border-0 d-flex align-items-center px-0">
                                        <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                            <i class="bi bi-person-badge fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?php echo $s['HoTen']; ?></h6>
                                            <small class="text-muted"><i class="bi bi-telephone"></i> <?php echo $s['SoDienThoai']; ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="resources">
                            <?php if(empty($resources)): ?>
                                <div class="alert alert-light border">Chưa có dịch vụ đi kèm.</div>
                            <?php else: ?>
                                <div class="list-group">
                                <?php foreach($resources as $r): ?>
                                    <div class="list-group-item border-0 d-flex align-items-start px-0 mb-2">
                                        <div class="bg-light rounded p-2 me-3 text-info">
                                            <i class="bi bi-box-seam fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?php echo $r['TenTaiNguyen']; ?></h6>
                                            <span class="badge bg-light text-dark border me-2"><?php echo $r['LoaiCungCap']; ?></span>
                                            <small class="text-muted">NCC: <?php echo $r['TenNhaCungCap']; ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tình trạng chỗ</h5>
                    <?php 
                        $percent = ($schedule['SoKhachHienTai'] / $schedule['SoChoToiDa']) * 100;
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Đã đặt: <?php echo $schedule['SoKhachHienTai']; ?> khách</span>
                        <span class="text-muted">Tổng: <?php echo $schedule['SoChoToiDa']; ?> chỗ</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $percent; ?>%"><?php echo round($percent); ?>%</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>