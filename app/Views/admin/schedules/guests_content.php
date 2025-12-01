<div class="pagetitle">
    <h1>Danh sách Đoàn khách</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/schedule/index">Lịch trình</a></li>
            <li class="breadcrumb-item active">Danh sách khách</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="card-title m-0 p-0"><?php echo $schedule['LichCode']; ?> - <?php echo $schedule['TenTour']; ?></h5>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm me-2" onclick="window.print()">
                            <i class="bi bi-printer"></i> In Danh Sách
                        </button>
                        <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Đơn <span>| Booking</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo count($bookings); ?></h6>
                                    <span class="text-muted small pt-2 ps-1">đơn hàng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Khách <span>| Thực tế</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo $total_guests; ?></h6>
                                    <span class="text-danger small pt-1 fw-bold">/ <?php echo $schedule['SoChoToiDa']; ?></span> 
                                    <span class="text-muted small pt-2 ps-1">chỗ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Chi tiết danh sách khách hàng</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">STT</th>
                                    <th scope="col">Mã Đơn</th>
                                    <th scope="col">Người đại diện</th>
                                    <th scope="col">Liên hệ</th>
                                    <th scope="col" class="text-center">Số lượng</th>
                                    <th scope="col">Thanh toán</th>
                                    <th scope="col" class="text-end">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($bookings)): ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có khách nào trong lịch này.</td></tr>
                                <?php else: ?>
                                    <?php foreach($bookings as $key => $b): ?>
                                    <tr>
                                        <td class="ps-3"><?php echo $key + 1; ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $b['MaBookingCode']; ?></span></td>
                                        <td>
                                            <div class="fw-bold text-primary"><?php echo $b['HoTen']; ?></div>
                                            <small class="text-muted">Ngày đặt: <?php echo date('d/m', strtotime($b['NgayDat'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="small"><i class="bi bi-telephone text-muted"></i> <?php echo $b['SoDienThoai']; ?></div>
                                            <div class="small text-muted text-truncate" style="max-width: 150px;"><?php echo $b['Email']; ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-info text-dark px-3"><?php echo $b['SoLuongKhach']; ?></span>
                                        </td>
                                        <td>
                                            <?php if($b['TrangThaiThanhToan'] == 'Đã thanh toán'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Đã xong</span>
                                            <?php elseif($b['TrangThaiThanhToan'] == 'Đã cọc'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-pie-chart-fill me-1"></i> Đã cọc</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i> Chưa TT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="btn btn-sm btn-light text-primary" title="Xem đơn hàng">
                                                <i class="bi bi-arrow-right"></i>
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

        </div>
    </div>
</section>