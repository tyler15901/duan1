<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Chi tiết Đơn hàng: #<?php echo $booking['MaBookingCode']; ?></h2>
    <a href="<?php echo BASE_URL; ?>/booking/index" class="btn btn-secondary">Quay lại</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Người đặt tour</div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> <?php echo $booking['TenKhach']; ?></p>
                <p><strong>SĐT:</strong> <?php echo $booking['SoDienThoai']; ?></p>
                <p><strong>Email:</strong> <?php echo $booking['Email']; ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo $booking['DiaChi']; ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-info text-white">Thông tin Tour</div>
            <div class="card-body">
                <p><strong>Tour:</strong> <?php echo $booking['TenTour']; ?></p>
                <p><strong>Ngày đi:</strong> <?php echo date('d/m/Y', strtotime($booking['NgayKhoiHanh'])); ?></p>
                <p><strong>Thời gian:</strong> <?php echo $booking['SoNgay']; ?> ngày</p>
                <hr>
                <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $booking['MaLichKhoiHanh']; ?>" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-calendar-check"></i> Quản lý Lịch này
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-dark">Xử lý đơn hàng</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/booking/update/<?php echo $booking['MaBooking']; ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="fw-bold">Tình trạng đơn</label>
                        <select name="trang_thai" class="form-select">
                            <option value="Chờ xác nhận" <?php echo ($booking['TrangThai']=='Chờ xác nhận')?'selected':''; ?>>Chờ xác nhận</option>
                            <option value="Đã xác nhận" <?php echo ($booking['TrangThai']=='Đã xác nhận')?'selected':''; ?>>Đã xác nhận</option>
                            <option value="Đã hủy" <?php echo ($booking['TrangThai']=='Đã hủy')?'selected':''; ?>>Hủy đơn</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Thanh toán</label>
                        <select name="thanh_toan" class="form-select">
                            <option value="Chưa thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Chưa thanh toán')?'selected':''; ?>>Chưa thanh toán</option>
                            <option value="Đã cọc" <?php echo ($booking['TrangThaiThanhToan']=='Đã cọc')?'selected':''; ?>>Đã cọc</option>
                            <option value="Đã thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Đã thanh toán')?'selected':''; ?>>Đã thanh toán (Full)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">File danh sách khách (Excel/PDF)</label>
                        <?php if($booking['FileDanhSachKhach']): ?>
                            <div class="mb-2">
                                <a href="<?php echo BASE_URL; ?>/assets/uploads/files/<?php echo $booking['FileDanhSachKhach']; ?>" target="_blank" class="text-success">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Tải file hiện tại
                                </a>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="guest_file" class="form-control">
                        <small class="text-muted">Upload file để lưu trữ gọn nhẹ DB</small>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Danh sách thành viên (<?php echo $booking['SoLuongKhach']; ?> người)</span>
                </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Họ và tên</th>
                            <th>Loại khách</th>
                            <th>Giấy tờ (CCCD)</th>
                            <th>Ghi chú / Yêu cầu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($guests)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-muted mb-1">Chưa có danh sách chi tiết.</p>
                                    <small>Nếu bạn đã upload file, vui lòng tải file về để xem.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($guests as $k => $g): ?>
                            <tr>
                                <td><?php echo $k + 1; ?></td>
                                <td class="fw-bold"><?php echo $g['HoTen']; ?></td>
                                <td><?php echo $g['LoaiKhach']; ?></td> <td><?php echo $g['SoGiayTo'] ?: '-'; ?></td>
                                <td><?php echo $g['GhiChu'] ?: '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>