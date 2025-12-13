<div class="pagetitle d-print-none">
    <h1>Chi tiết Đơn hàng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/booking/index">Đơn hàng</a></li>
            <li class="breadcrumb-item active">#<?php echo $booking['MaBookingCode']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body pt-4">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Người đặt tour</h6>
                            <div class="d-flex mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 text-dark"><?php echo $booking['TenKhach']; ?></div>
                                    <div class="text-primary fw-bold font-monospace"><?php echo $booking['SoDienThoai']; ?></div>
                                </div>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt me-2"></i> <?php echo $booking['DiaChi'] ?? 'Chưa cập nhật địa chỉ'; ?>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Tour đăng ký</h6>
                            <div class="fw-bold text-dark mb-2 fs-5"><?php echo $booking['TenTour']; ?></div>
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border me-1"><i class="bi bi-qr-code me-1"></i> <?php echo $booking['LichCode']; ?></span>
                                <span class="badge bg-light text-dark border"><i class="bi bi-calendar-check me-1"></i> <?php echo date('d/m/Y', strtotime($booking['NgayKhoiHanh'])); ?></span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $booking['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-outline-primary d-print-none">
                                <i class="bi bi-list-stars me-1"></i> Xem DS toàn đoàn
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($booking['SoLuongKhach'] > count($guests)): ?>
                <div class="alert alert-warning d-flex align-items-center d-print-none shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-4"></i>
                    <div>
                        <strong>Chưa đủ thông tin!</strong> Đơn hàng có <b><?php echo $booking['SoLuongKhach']; ?></b> khách, nhưng mới nhập <b><?php echo count($guests); ?></b> người.
                    </div>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Danh sách thành viên (<?php echo count($guests); ?>/<?php echo $booking['SoLuongKhach']; ?>)</h5>
                    <div class="d-print-none">
                        <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import CSV
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Thêm Khách
                        </button>
                        <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="bi bi-printer me-1"></i> In DS</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3">#</th> <th>Họ tên</th> <th>Loại</th> <th>SĐT</th> <th>Giấy tờ</th> <th>Ghi chú</th> <th class="text-end d-print-none">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($guests)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có thông tin thành viên.</td></tr>
                            <?php else: ?>
                                <?php foreach ($guests as $k => $g): ?>
                                    <tr>
                                        <td class="ps-3"><?php echo $k + 1; ?></td>
                                        <td class="fw-bold text-primary"><?php echo $g['HoTen']; ?></td>
                                        <td><span class="badge bg-secondary bg-opacity-75 text-white"><?php echo $g['LoaiKhach']; ?></span></td>
                                        <td class="font-monospace"><?php echo $g['SoDienThoai'] ?: '-'; ?></td>
                                        <td class="font-monospace text-secondary"><?php echo $g['SoGiayTo'] ?: '-'; ?></td>
                                        <td><?php echo $g['GhiChu']; ?></td>
                                        <td class="text-end d-print-none">
                                            <button class="btn btn-sm btn-light text-primary border-0 me-1" onclick="openEditGuestModal('<?php echo $g['MaChiTiet']; ?>','<?php echo $g['HoTen']; ?>','<?php echo $g['LoaiKhach']; ?>','<?php echo $g['SoDienThoai']; ?>','<?php echo $g['SoGiayTo']; ?>','<?php echo $g['GhiChu']; ?>')"><i class="bi bi-pencil-square"></i></button>
                                            <a href="<?php echo BASE_URL; ?>/booking/delete_guest/<?php echo $g['MaChiTiet']; ?>/<?php echo $booking['MaBooking']; ?>" class="btn btn-sm btn-light text-danger border-0" onclick="return confirm('Xóa?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-print-none">
            <div class="card sticky-top shadow border-0" style="top: 80px; z-index: 10;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title text-white m-0 p-0"><i class="bi bi-gear-fill me-2"></i> Xử lý Đơn hàng</h5>
                </div>
                
                <div class="card-body pt-4">
                    <form action="<?php echo BASE_URL; ?>/booking/update/<?php echo $booking['MaBooking']; ?>" method="POST">

                        <input type="hidden" id="price_adult" value="<?php echo (int)$booking['GiaNguoiLon']; ?>">
                        <input type="hidden" id="price_child" value="<?php echo (int)$booking['GiaTreEm']; ?>">

                        <div class="mb-3 bg-warning bg-opacity-10 p-2 rounded border border-warning">
                            <label class="form-label fw-bold small text-uppercase text-dark">
                                <i class="bi bi-calendar-range me-1"></i> Đổi Lịch Khởi Hành
                            </label>
                            <select name="change_lich_id" class="form-select border-warning fw-bold text-dark">
                                <option value="<?php echo $booking['MaLichKhoiHanh']; ?>" selected>
                                    HT: <?php echo $booking['LichCode']; ?> (<?php echo date('d/m', strtotime($booking['NgayKhoiHanh'])); ?>)
                                </option>
                                
                                <?php if (!empty($other_schedules)): ?>
                                    <option disabled>──────────</option>
                                    <?php foreach ($other_schedules as $sch): ?>
                                        <?php if ($sch['MaLichKhoiHanh'] != $booking['MaLichKhoiHanh']): ?>
                                            <option value="<?php echo $sch['MaLichKhoiHanh']; ?>">
                                                ➡ <?php echo $sch['LichCode']; ?> (<?php echo date('d/m', strtotime($sch['NgayKhoiHanh'])); ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text small text-muted fst-italic">
                                Chọn lịch mới nếu khách muốn đổi ngày.
                            </div>
                        </div>

                        <div class="mb-3 border-bottom pb-3">
                            <label class="form-label fw-bold small text-uppercase">Cập nhật số lượng</label>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="small text-muted">Người lớn</label>
                                    <input type="number" name="sl_nguoi_lon" id="sl_nguoi_lon" class="form-control fw-bold" value="<?php echo $booking['SLNguoiLon']; ?>" min="1" onchange="calculateTotal()">
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted">Trẻ em</label>
                                    <input type="number" name="sl_tre_em" id="sl_tre_em" class="form-control fw-bold" value="<?php echo $booking['SLTreEm']; ?>" min="0" onchange="calculateTotal()">
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <label class="small fw-bold text-danger">Tổng thành tiền (VND)</label>
                                <input type="text" id="total_money_show" class="form-control form-control-lg text-danger fw-bold bg-light" value="<?php echo number_format($booking['TongTien']); ?>" readonly>
                                <input type="hidden" name="tong_tien" id="total_money" value="<?php echo (int)$booking['TongTien']; ?>">
                            </div>
                        </div>

                        <div class="mb-3 bg-light p-2 rounded border">
                            <label class="form-label fw-bold small text-muted text-uppercase">Hướng dẫn viên phụ trách</label>
                            <?php if (!empty($booking['TenHuongDanVien'])): ?>
                                <div class="d-flex align-items-center bg-white p-2 rounded shadow-sm border-start border-4 border-primary">
                                    <i class="bi bi-person-check-fill fs-4 me-2 text-primary"></i>
                                    <div class="fw-bold text-dark"><?php echo $booking['TenHuongDanVien']; ?></div>
                                </div>
                                <input type="hidden" name="ma_hdv" value="<?php echo $booking['MaHuongDanVien']; ?>">
                            <?php else: ?>
                                <div class="alert alert-secondary p-2 mb-0 border-0 small">Chưa phân bổ HDV</div>
                                <input type="hidden" name="ma_hdv" value="">
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Trạng thái đơn</label>
                            <select name="trang_thai" class="form-select">
                                <option value="Chờ xác nhận" <?php echo ($booking['TrangThai']=='Chờ xác nhận')?'selected':''; ?>>⏳ Chờ xác nhận</option>
                                <option value="Đã xác nhận" <?php echo ($booking['TrangThai']=='Đã xác nhận')?'selected':''; ?>>✅ Đã xác nhận</option>
                                <option value="Hoàn tất" <?php echo ($booking['TrangThai']=='Hoàn tất')?'selected':''; ?>>🏁 Hoàn tất</option>
                                <option value="Đã hủy" <?php echo ($booking['TrangThai']=='Đã hủy')?'selected':''; ?>>❌ Hủy đơn</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Thanh toán</label>
                            <select name="thanh_toan" class="form-select">
                                <option value="Chưa thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Chưa thanh toán')?'selected':''; ?>>⚪ Chưa thanh toán</option>
                                <option value="Đã cọc" <?php echo ($booking['TrangThaiThanhToan']=='Đã cọc')?'selected':''; ?>>🟡 Đã đặt cọc</option>
                                <option value="Đã thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Đã thanh toán')?'selected':''; ?>>🟢 Đã thanh toán (Full)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary shadow-sm" onclick="return confirm('Cập nhật đơn hàng?')">
                                <i class="bi bi-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Thêm Thành Viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/booking/store_guest/<?php echo $booking['MaBooking']; ?>"
                method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" required placeholder="Nhập tên khách...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại khách</label>
                            <select name="loai_khach" class="form-select">
                                <option value="Người lớn">Người lớn</option>
                                <option value="Trẻ em">Trẻ em</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="sdt" class="form-control" placeholder="Nhập SĐT...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Giấy tờ (CCCD/PP)</label>
                            <input type="text" name="so_giay_to" class="form-control" placeholder="Số giấy tờ...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="2"
                            placeholder="VD: Ăn chay, Dị ứng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu khách</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Cập nhật thông tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/booking/update_guest/<?php echo $booking['MaBooking']; ?>"
                method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ma_chi_tiet" id="edit_ma_chi_tiet">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" id="edit_ho_ten" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại khách</label>
                            <select name="loai_khach" id="edit_loai_khach" class="form-select">
                                <option value="Người lớn">Người lớn</option>
                                <option value="Trẻ em">Trẻ em</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="sdt" id="edit_sdt" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Giấy tờ (CCCD/PP)</label>
                        <input type="text" name="so_giay_to" id="edit_so_giay_to" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú</label>
                        <textarea name="ghi_chu" id="edit_ghi_chu" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning fw-bold">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Import Danh Sách Khách</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/booking/import_guests/<?php echo $booking['MaBooking']; ?>"
                method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <strong>Lưu ý:</strong> Vui lòng sử dụng file <b>.CSV</b><br>
                        Thứ tự cột: <b>Họ Tên | Loại Khách | SĐT | CCCD | Ghi Chú</b>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn file CSV</label>
                        <input type="file" name="file_import" class="form-control" accept=".csv, .xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Tải lên & Xử lý</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Hàm tính toán tổng tiền
    function calculateTotal() {
        // Lấy giá trị gốc là số nguyên từ input hidden (Đã được PHP ép kiểu int)
        // Không dùng replace nữa vì số đã sạch
        let priceAdult = parseFloat(document.getElementById('price_adult').value) || 0;
        let priceChild = parseFloat(document.getElementById('price_child').value) || 0;
        
        let qtyAdult = parseInt(document.getElementById('sl_nguoi_lon').value) || 0;
        let qtyChild = parseInt(document.getElementById('sl_tre_em').value) || 0;

        let total = (priceAdult * qtyAdult) + (priceChild * qtyChild);

        // Input hidden gửi lên server: Lưu số nguyên
        document.getElementById('total_money').value = total;
        // Input hiển thị: Format đẹp
        document.getElementById('total_money_show').value = new Intl.NumberFormat('vi-VN').format(total);
    }

    function openEditGuestModal(id, name, type, phone, card, note) {
        document.getElementById('edit_ma_chi_tiet').value = id;
        document.getElementById('edit_ho_ten').value = name;
        document.getElementById('edit_loai_khach').value = type;
        document.getElementById('edit_sdt').value = phone;
        document.getElementById('edit_so_giay_to').value = card;
        document.getElementById('edit_ghi_chu').value = note;
        new bootstrap.Modal(document.getElementById('editGuestModal')).show();
    }
</script>

<style>
    @media print {

        .d-print-none,
        .header,
        .sidebar,
        .pagetitle {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        body {
            background: white !important;
        }

        th:last-child,
        td:last-child {
            display: none;
        }
    }
</style>