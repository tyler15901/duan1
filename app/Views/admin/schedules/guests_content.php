<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Danh sách Đoàn khách</h4>
        <h3 class="text-primary fw-bold"><?php echo $schedule['LichCode']; ?> - <?php echo $schedule['TenTour']; ?></h3>
    </div>
    <div>
        <button class="btn btn-success me-2" onclick="window.print()"><i class="bi bi-printer"></i> In DS</button>
        <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-primary">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Tổng đơn (Booking)</div>
                <div class="d-flex align-items-center mt-2">
                    <h2 class="mb-0 me-3"><?php echo count($bookings); ?></h2>
                    <i class="bi bi-receipt fs-1 text-primary opacity-25 ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-info">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Tổng số khách</div>
                <div class="d-flex align-items-center mt-2">
                    <h2 class="mb-0 me-3"><?php echo $total_guests; ?> <span class="fs-6 text-muted">/ <?php echo $schedule['SoChoToiDa']; ?></span></h2>
                    <i class="bi bi-people fs-1 text-info opacity-25 ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Chi tiết danh sách</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary">
                <tr>
                    <th class="ps-4">STT</th>
                    <th>Mã Đơn</th>
                    <th>Người đại diện</th>
                    <th>Liên hệ</th>
                    <th class="text-center">Số lượng</th>
                    <th>Thanh toán</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($bookings)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có khách nào trong lịch này.</td></tr>
                <?php else: ?>
                    <?php foreach($bookings as $key => $b): ?>
                    <tr>
                        <td class="ps-4"><?php echo $key + 1; ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo $b['MaBookingCode']; ?></span></td>
                        <td>
                            <div class="fw-bold"><?php echo $b['HoTen']; ?></div>
                            <small class="text-muted">Ngày đặt: <?php echo date('d/m', strtotime($b['NgayDat'])); ?></small>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-telephone"></i> <?php echo $b['SoDienThoai']; ?></div>
                            <div class="small text-muted text-truncate" style="max-width: 150px;"><?php echo $b['Email']; ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info rounded-pill px-3"><?php echo $b['SoLuongKhach']; ?></span>
                        </td>
                        <td>
                            <?php if($b['TrangThaiThanhToan'] == 'Đã thanh toán'): ?>
                                <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Đã xong</span>
                            <?php elseif($b['TrangThaiThanhToan'] == 'Đã cọc'): ?>
                                <span class="text-warning small fw-bold"><i class="bi bi-pie-chart-fill"></i> Đã cọc</span>
                            <?php else: ?>
                                <span class="text-danger small fw-bold"><i class="bi bi-circle"></i> Chưa TT</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="btn btn-sm btn-outline-primary">
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