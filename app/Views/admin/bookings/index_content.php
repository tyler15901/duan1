<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold"><i class="bi bi-receipt"></i> Quản lý Đơn Hàng</h2>
    <a href="<?php echo BASE_URL; ?>/booking/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tạo đơn mới
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0"
                        placeholder="Tìm theo mã đơn, tên khách, SĐT..."
                        value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Chờ xử lý">Chờ xử lý</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr class="text-uppercase small fw-bold">
                        <th class="ps-4">Mã Đơn</th>
                        <th>Khách hàng</th>
                        <th>Tour & Lịch trình</th>
                        <th class="text-center">Số khách</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không tìm thấy đơn hàng nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#<?php echo $b['MaBookingCode']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $b['TenKhach']; ?></div>
                                    <div class="small text-muted"><i class="bi bi-telephone"></i>
                                        <?php echo $b['SoDienThoai']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-truncate" style="max-width: 250px;"
                                        title="<?php echo $b['TenTour']; ?>">
                                        <?php echo $b['TenTour']; ?>
                                    </div>
                                    <div class="small text-secondary mt-1">
                                        <i class="bi bi-calendar-event text-info"></i>
                                        <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $b['MaLichKhoiHanh']; ?>"
                                            class="text-decoration-none">
                                            <?php echo $b['LichCode']; ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        <?php echo $b['SoLuongKhach']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $statusClass = match ($b['TrangThai']) {
                                        'Đã xác nhận' => 'success',
                                        'Đã hủy' => 'danger',
                                        default => 'warning text-dark'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?> bg-opacity-75">
                                        <?php echo $b['TrangThai']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($b['TrangThaiThanhToan'] == 'Đã thanh toán'): ?>
                                        <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Hoàn
                                            tất</span>
                                    <?php elseif ($b['TrangThaiThanhToan'] == 'Đã cọc'): ?>
                                        <span class="text-warning small fw-bold"><i class="bi bi-pie-chart-fill"></i> Đã cọc</span>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="bi bi-circle"></i> Chưa TT</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end pe-4">
                                    <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>"
                                        class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <?php include '../app/Views/layouts/pagination.php'; ?>
    </div>
</div>