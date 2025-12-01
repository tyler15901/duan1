<div class="pagetitle">
    <h1>Lịch làm việc cá nhân</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/staff/index">Nhân sự</a></li>
            <li class="breadcrumb-item active"><?php echo $guide['HoTen']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card mb-4 profile-overview">
        <div class="card-body pt-3">
            <div class="d-flex align-items-center">
                <?php $imgSrc = !empty($guide['AnhDaiDien']) ? BASE_URL.'/assets/uploads/'.$guide['AnhDaiDien'] : 'https://ui-avatars.com/api/?name='.$guide['HoTen']; ?>
                <img src="<?php echo $imgSrc; ?>" class="rounded-circle me-3 border border-3 border-light shadow-sm" width="70" height="70" style="object-fit: cover;">
                
                <div class="flex-grow-1">
                    <h4 class="card-title p-0 m-0 text-primary"><?php echo $guide['HoTen']; ?></h4>
                    <span class="badge bg-light text-dark border mt-1"><?php echo $guide['PhanLoai']; ?></span>
                    
                    <div class="d-flex gap-4 mt-2 text-muted small">
                        <span><i class="bi bi-telephone-fill me-1"></i> <?php echo $guide['SoDienThoai']; ?></span>
                        <span><i class="bi bi-envelope-fill me-1"></i> <?php echo $guide['Email']; ?></span>
                    </div>
                </div>

                <div class="text-end d-none d-md-block">
                    <div class="display-6 fw-bold text-success"><?php echo count($schedules); ?></div>
                    <div class="small text-muted text-uppercase">Tour đã nhận</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <h5 class="card-title p-0 m-0">Danh sách Tour được phân công</h5>
                <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Mã Lịch</th>
                            <th scope="col">Tên Tour</th>
                            <th scope="col">Thời gian</th>
                            <th scope="col">Địa điểm & Ghi chú</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col" class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($schedules)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted opacity-25"></i>
                                    <p class="text-muted mt-2 mb-0">Nhân viên này chưa có lịch trình nào.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($schedules as $s): ?>
                            <tr>
                                <td><span class="badge bg-light text-primary border border-primary"><?php echo $s['LichCode']; ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $s['TenTour']; ?></div>
                                    <small class="text-muted"><?php echo $s['SoNgay']; ?> ngày</small>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="text-success fw-bold"><i class="bi bi-box-arrow-right me-1"></i> <?php echo date('d/m/Y', strtotime($s['NgayKhoiHanh'])); ?></div>
                                        <div class="text-danger"><i class="bi bi-box-arrow-in-left me-1"></i> <?php echo date('d/m/Y', strtotime($s['NgayKetThuc'])); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 200px;">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo $s['DiaDiemTapTrung']; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $badge = 'bg-secondary';
                                        if($s['TrangThai'] == 'Nhận khách') $badge = 'bg-success';
                                        elseif($s['TrangThai'] == 'Đang chạy') $badge = 'bg-warning text-dark';
                                        elseif($s['TrangThai'] == 'Hoàn tất') $badge = 'bg-primary';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $s['TrangThai']; ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Xem <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>