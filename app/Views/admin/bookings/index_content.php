<div class="pagetitle">
    <h1>Quản lý Đơn hàng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Đơn hàng</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h5 class="card-title p-0 m-0">Danh sách Booking</h5>
                        <a href="<?php echo BASE_URL; ?>/booking/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Tạo đơn mới
                        </a>
                    </div>

                    <form class="row g-3 mb-4 bg-light p-3 rounded mx-1 align-items-end" method="GET">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Từ khóa tìm kiếm</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control" 
                                    placeholder="Mã đơn, tên khách, SĐT..." 
                                    value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Trạng thái xử lý</label>
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="Chờ xác nhận">⏳ Chờ xác nhận</option>
                                <option value="Đã xác nhận">✅ Đã xác nhận</option>
                                <option value="Đã hủy">❌ Đã hủy</option>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label fw-bold small">Thanh toán</label>
                            <select name="payment_status" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="Chưa thanh toán">⚪ Chưa thanh toán</option>
                                <option value="Đã cọc">🟡 Đã cọc</option>
                                <option value="Đã thanh toán">🟢 Đã xong</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Lọc</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">Mã Đơn</th>
                                    <th scope="col">Khách hàng</th>
                                    <th scope="col">Tour & Lịch trình</th>
                                    <th scope="col" class="text-center">Số khách</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thanh toán</th>
                                    <th scope="col" class="text-end">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                    <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $b): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-light text-primary border border-primary fw-bold">#<?php echo $b['MaBookingCode']; ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $b['TenKhach']; ?></div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?php echo $b['SoDienThoai']; ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-truncate" style="max-width: 250px;" title="<?php echo $b['TenTour']; ?>">
                                                <?php echo $b['TenTour']; ?>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-calendar-event text-info"></i> 
                                                <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $b['MaLichKhoiHanh']; ?>" class="text-secondary fw-bold">
                                                    <?php echo $b['LichCode']; ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-info text-dark px-3"><?php echo $b['SoLuongKhach']; ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = match ($b['TrangThai']) {
                                                'Đã xác nhận' => 'success',
                                                'Đã hủy' => 'danger',
                                                default => 'warning text-dark'
                                            };
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass; ?>">
                                                <?php echo $b['TrangThai']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($b['TrangThaiThanhToan'] == 'Đã thanh toán'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-lg"></i> Xong</span>
                                            <?php elseif ($b['TrangThaiThanhToan'] == 'Đã cọc'): ?>
                                                <span class="badge bg-warning text-dark">Đã cọc</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa TT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="btn btn-sm btn-light text-primary border" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (isset($pagination)): ?>
                        <div class="mt-4 d-flex justify-content-center">
                            <?php include '../app/Views/layouts/pagination.php'; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>