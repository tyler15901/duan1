<h2 class="mb-3">Quản lý Đơn Hàng</h2>

<form class="row g-2 mb-4 bg-light p-3 rounded">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Mã đơn, tên khách, SĐT..." value="<?php echo htmlspecialchars($pagination['keyword']); ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
    </div>
</form>

<table class="table table-bordered table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>Mã Đơn</th>
            <th>Khách hàng</th>
            <th>Tour & Lịch trình</th>
            <th>Số khách</th>
            <th>Trạng thái</th>
            <th>Thanh toán</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($bookings as $b): ?>
        <tr>
            <td class="fw-bold text-primary"><?php echo $b['MaBookingCode']; ?></td>
            <td>
                <div class="fw-bold"><?php echo $b['TenKhach']; ?></div>
                <small><?php echo $b['SoDienThoai']; ?></small>
            </td>
            <td>
                <div class="fw-bold text-truncate" style="max-width: 200px;"><?php echo $b['TenTour']; ?></div>
                <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $b['MaLichKhoiHanh']; ?>" class="text-decoration-none small">
                    <i class="bi bi-calendar-week"></i> <?php echo $b['LichCode']; ?>
                </a>
            </td>
            <td class="text-center"><?php echo $b['SoLuongKhach']; ?></td>
            
            <td>
                <?php if($b['TrangThai']=='Đã xác nhận'): ?>
                    <span class="badge bg-success">Đã xác nhận</span>
                <?php elseif($b['TrangThai']=='Đã hủy'): ?>
                    <span class="badge bg-danger">Đã hủy</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Chờ xử lý</span>
                <?php endif; ?>
            </td>

            <td>
                <?php if($b['TrangThaiThanhToan']=='Đã thanh toán'): ?>
                    <span class="badge bg-success">Hoàn tất</span>
                <?php elseif($b['TrangThaiThanhToan']=='Đã cọc'): ?>
                    <span class="badge bg-warning text-dark">Đã cọc</span>
                <?php else: ?>
                    <span class="badge bg-light text-dark border">Chưa TT</span>
                <?php endif; ?>
            </td>

            <td>
                <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="btn btn-sm btn-info text-white">
                    Chi tiết
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>