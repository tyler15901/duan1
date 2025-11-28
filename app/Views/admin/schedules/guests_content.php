<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Danh sách đoàn khách</h2>
        <h5 class="text-muted">
            Tour: <span class="text-primary"><?php echo $schedule['TenTour']; ?></span> 
            (<?php echo $schedule['LichCode']; ?>)
        </h5>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại lịch
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body p-3 text-center">
                <h3><?php echo count($bookings); ?></h3>
                <small>Đơn hàng (Booking)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body p-3 text-center">
                <h3><?php echo $total_guests; ?> / <?php echo $schedule['SoChoToiDa']; ?></h3>
                <small>Tổng số khách / Chỗ tối đa</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-end align-self-end">
        <button class="btn btn-outline-success" onclick="alert('Tính năng đang phát triển')">
            <i class="bi bi-file-earmark-excel"></i> Xuất danh sách đoàn
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Mã Đơn</th>
                    <th>Người đại diện</th>
                    <th>Liên hệ</th>
                    <th class="text-center">Số người</th>
                    <th>Trạng thái TT</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($bookings)): ?>
                    <tr><td colspan="7" class="text-center py-4">Chưa có khách nào đặt lịch này.</td></tr>
                <?php else: ?>
                    <?php foreach($bookings as $key => $b): ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td class="fw-bold text-primary"><?php echo $b['MaBookingCode']; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $b['HoTen']; ?></div>
                            <small class="text-muted">Ngày đặt: <?php echo date('d/m/Y', strtotime($b['NgayDat'])); ?></small>
                        </td>
                        <td>
                            <i class="bi bi-telephone"></i> <?php echo $b['SoDienThoai']; ?> <br>
                            <small class="text-muted"><?php echo $b['Email']; ?></small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary fs-6 rounded-pill">
                                <?php echo $b['SoLuongKhach']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                $statusClass = 'secondary';
                                if($b['TrangThai'] == 'Đã cọc') $statusClass = 'warning';
                                if($b['TrangThai'] == 'Đã thanh toán') $statusClass = 'success';
                                if($b['TrangThai'] == 'Đã hủy') $statusClass = 'danger';
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?>">
                                <?php echo $b['TrangThai']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Xem đơn
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>