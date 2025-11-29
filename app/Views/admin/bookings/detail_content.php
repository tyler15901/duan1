<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-secondary">Chi tiết Booking</h4>
        <h2 class="text-primary fw-bold">#<?php echo $booking['MaBookingCode']; ?></h2>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>/booking/index" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="bi bi-printer"></i> In đơn
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-card-list"></i> Thông tin chung</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Người đặt tour</h6>
                        <div class="d-flex mb-2">
                            <div class="me-3 text-secondary"><i class="bi bi-person-circle fs-4"></i></div>
                            <div>
                                <div class="fw-bold fs-5"><?php echo $booking['TenKhach']; ?></div>
                                <div class="text-muted"><?php echo $booking['SoDienThoai']; ?></div>
                                <div class="small text-muted"><?php echo $booking['Email']; ?></div>
                            </div>
                        </div>
                        <p class="small text-muted mb-0"><i class="bi bi-geo-alt"></i> <?php echo $booking['DiaChi']; ?></p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Thông tin Tour</h6>
                        <h5 class="fw-bold text-primary mb-1"><?php echo $booking['TenTour']; ?></h5>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar"></i> Khởi hành: <?php echo date('d/m/Y', strtotime($booking['NgayKhoiHanh'])); ?>
                            </span>
                            <span class="badge bg-light text-dark border ms-1">
                                <i class="bi bi-clock"></i> <?php echo $booking['SoNgay']; ?> ngày
                            </span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $booking['MaLichKhoiHanh']; ?>" class="text-decoration-none small fw-bold">
                            <i class="bi bi-box-arrow-up-right"></i> Xem chi tiết lịch này
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-people"></i> Danh sách thành viên (<?php echo $booking['SoLuongKhach']; ?>)</h5>
                
                <?php if($booking['FileDanhSachKhach']): ?>
                    <a href="<?php echo BASE_URL; ?>/assets/uploads/files/<?php echo $booking['FileDanhSachKhach']; ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-download"></i> Tải file khách
                    </a>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Họ và tên</th>
                            <th>Loại khách</th>
                            <th>CCCD/Passport</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($guests)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <em>Dữ liệu khách hàng chưa được cập nhật chi tiết.</em><br>
                                    <small>Hãy tải file danh sách đính kèm nếu có.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($guests as $k => $g): ?>
                            <tr>
                                <td><?php echo $k + 1; ?></td>
                                <td class="fw-bold"><?php echo $g['HoTen']; ?></td>
                                <td><span class="badge bg-info text-dark bg-opacity-25"><?php echo $g['LoaiKhach']; ?></span></td>
                                <td class="font-monospace"><?php echo $g['SoGiayTo'] ?: '-'; ?></td>
                                <td class="small text-muted"><?php echo $g['GhiChu'] ?: ''; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow border-0 position-sticky" style="top: 20px; z-index: 100;">
            <div class="card-header bg-primary text-white fw-bold py-3">
                <i class="bi bi-gear-fill"></i> Xử lý Đơn hàng
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/booking/update/<?php echo $booking['MaBooking']; ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái xử lý</label>
                        <select name="trang_thai" class="form-select form-select-lg fw-bold <?php echo ($booking['TrangThai']=='Đã xác nhận') ? 'text-success border-success' : ''; ?>">
                            <option value="Chờ xác nhận" <?php echo ($booking['TrangThai']=='Chờ xác nhận')?'selected':''; ?>>⏳ Chờ xác nhận</option>
                            <option value="Đã xác nhận" <?php echo ($booking['TrangThai']=='Đã xác nhận')?'selected':''; ?>>✅ Đã xác nhận</option>
                            <option value="Đã hủy" <?php echo ($booking['TrangThai']=='Đã hủy')?'selected':''; ?>>❌ Hủy đơn</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Thanh toán</label>
                        <select name="thanh_toan" class="form-select">
                            <option value="Chưa thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Chưa thanh toán')?'selected':''; ?>>⚪ Chưa thanh toán</option>
                            <option value="Đã cọc" <?php echo ($booking['TrangThaiThanhToan']=='Đã cọc')?'selected':''; ?>>🟡 Đã cọc</option>
                            <option value="Đã thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Đã thanh toán')?'selected':''; ?>>🟢 Đã thanh toán (Full)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Upload DS Khách</label>
                        <input type="file" name="guest_file" class="form-control form-control-sm">
                        <div class="form-text small">Hỗ trợ file Excel, PDF.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            <i class="bi bi-save"></i> Cập nhật thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>