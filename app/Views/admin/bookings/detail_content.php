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
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Người đặt tour (Liên hệ chính)</h6>
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
                                <span class="badge bg-light text-dark border me-1">
                                    <i class="bi bi-qr-code me-1"></i> <?php echo $booking['LichCode']; ?>
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-calendar-check me-1"></i> <?php echo date('d/m/Y', strtotime($booking['NgayKhoiHanh'])); ?>
                                </span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $booking['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-outline-primary d-print-none">
                                <i class="bi bi-list-stars me-1"></i> Xem DS toàn đoàn
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Danh sách thành viên (<?php echo count($guests); ?>/<?php echo $booking['SoLuongKhach']; ?>)</h5>
                    
                    <div class="d-print-none">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Thêm Khách
                        </button>
                        <button onclick="window.print()" class="btn btn-sm btn-secondary">
                            <i class="bi bi-printer me-1"></i> In DS
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Họ và tên</th>
                                <th>Phân loại</th>
                                <th>Liên hệ / SĐT</th>
                                <th>Giấy tờ (CCCD)</th>
                                <th>Ghi chú</th>
                                <th class="text-end d-print-none">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($guests)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="bi bi-people fs-1 opacity-25"></i></div>
                                        Chưa có thông tin thành viên.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($guests as $k => $g): ?>
                                    <tr>
                                        <td class="ps-3"><?php echo $k + 1; ?></td>
                                        
                                        <td class="fw-bold text-primary"><?php echo $g['HoTen']; ?></td>
                                        
                                        <td>
                                            <?php 
                                                $badgeColor = match($g['LoaiKhach']) {
                                                    'Người lớn' => 'bg-success',
                                                    'Trẻ em' => 'bg-info',
                                                    'Em bé' => 'bg-warning text-dark',
                                                    default => 'bg-secondary'
                                                };
                                            ?>
                                            <span class="badge <?php echo $badgeColor; ?> bg-opacity-75 border-0">
                                                <?php echo $g['LoaiKhach']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if(!empty($g['SoDienThoai'])): ?>
                                                <span class="font-monospace text-dark"><?php echo $g['SoDienThoai']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="font-monospace text-secondary"><?php echo $g['SoGiayTo'] ?: '-'; ?></td>
                                        
                                        <td>
                                            <?php if(!empty($g['GhiChu'])): ?>
                                                <span class="text-danger small fst-italic"><i class="bi bi-exclamation-circle me-1"></i><?php echo $g['GhiChu']; ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end d-print-none">
                                            <button type="button" class="btn btn-sm btn-light text-primary border-0 me-1"
                                                    onclick="openEditGuestModal(
                                                        '<?php echo $g['MaChiTiet']; ?>',
                                                        '<?php echo $g['HoTen']; ?>',
                                                        '<?php echo $g['LoaiKhach']; ?>',
                                                        '<?php echo $g['SoDienThoai']; ?>',
                                                        '<?php echo $g['SoGiayTo']; ?>',
                                                        '<?php echo $g['GhiChu']; ?>'
                                                    )" title="Sửa thông tin">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <a href="<?php echo BASE_URL; ?>/booking/delete_guest/<?php echo $g['MaChiTiet']; ?>/<?php echo $booking['MaBooking']; ?>" 
                                               class="btn btn-sm btn-light text-danger border-0"
                                               onclick="return confirm('Xóa khách [<?php echo $g['HoTen']; ?>] khỏi đơn hàng?')" 
                                               title="Xóa khách này">
                                                <i class="bi bi-trash"></i>
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

        <div class="col-lg-4 d-print-none">
            <div class="card sticky-top shadow border-0" style="top: 80px; z-index: 10;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title text-white m-0 p-0"><i class="bi bi-gear-fill me-2"></i> Xử lý Đơn hàng</h5>
                </div>
                <div class="card-body pt-4">
                    <form action="<?php echo BASE_URL; ?>/booking/update/<?php echo $booking['MaBooking']; ?>" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Trạng thái xử lý</label>
                            <select name="trang_thai" class="form-select fw-bold <?php echo ($booking['TrangThai']=='Đã xác nhận')?'border-success text-success':''; ?>">
                                <option value="Chờ xác nhận" <?php echo ($booking['TrangThai']=='Chờ xác nhận')?'selected':''; ?>>⏳ Chờ xác nhận</option>
                                <option value="Đã xác nhận" <?php echo ($booking['TrangThai']=='Đã xác nhận')?'selected':''; ?>>✅ Đã xác nhận</option>
                                <option value="Hoàn tất" <?php echo ($booking['TrangThai']=='Hoàn tất')?'selected':''; ?>>🏁 Hoàn tất</option>
                                <option value="Đã hủy" <?php echo ($booking['TrangThai']=='Đã hủy')?'selected':''; ?>>❌ Hủy đơn</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Thanh toán</label>
                            <select name="thanh_toan" class="form-select">
                                <option value="Chưa thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Chưa thanh toán')?'selected':''; ?>>⚪ Chưa thanh toán</option>
                                <option value="Đã cọc" <?php echo ($booking['TrangThaiThanhToan']=='Đã cọc')?'selected':''; ?>>🟡 Đã đặt cọc</option>
                                <option value="Đã thanh toán" <?php echo ($booking['TrangThaiThanhToan']=='Đã thanh toán')?'selected':''; ?>>🟢 Đã thanh toán (Full)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary shadow-sm">
                                <i class="bi bi-save me-1"></i> Cập nhật Đơn hàng
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light p-3">
                    <?php 
                        $tongTien = (float)($booking['TongTien'] ?? 0);
                        $tienCoc  = (float)($booking['TienCoc'] ?? 0);
                        $conLai   = $tongTien - $tienCoc;
                    ?>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Tổng giá trị:</span>
                        <strong class="text-dark"><?php echo number_format($tongTien); ?> đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Đã đặt cọc:</span>
                        <strong><?php echo number_format($tienCoc); ?> đ</strong>
                    </div>
                    <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-secondary">Còn lại:</span>
                        <span class="fs-5 fw-bold text-danger"><?php echo number_format($conLai); ?> đ</span>
                    </div>
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
            <form action="<?php echo BASE_URL; ?>/booking/store_guest/<?php echo $booking['MaBooking']; ?>" method="POST">
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
                        <textarea name="ghi_chu" class="form-control" rows="2" placeholder="VD: Ăn chay, Dị ứng..."></textarea>
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
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Cập nhật thông tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/booking/update_guest/<?php echo $booking['MaBooking']; ?>" method="POST">
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

<script>
    // Hàm mở modal sửa và điền dữ liệu cũ
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
        .d-print-none, .header, .sidebar, .pagetitle { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .main { margin: 0 !important; padding: 0 !important; }
        body { background: white !important; }
        /* Ẩn cột thao tác khi in */
        th:last-child, td:last-child { display: none; }
    }
</style>