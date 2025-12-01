<div class="pagetitle">
    <h1>Cập nhật Lịch trình</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/schedule/index">Lịch trình</a></li>
            <li class="breadcrumb-item active"><?php echo $schedule['LichCode']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/schedule/update/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin chung</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tour du lịch (Cố định)</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $schedule['TenTour']; ?>" readonly>
                            <input type="hidden" name="tour_id" value="<?php echo $schedule['MaTour']; ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày khởi hành</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo $schedule['NgayKhoiHanh']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo $schedule['NgayKetThuc']; ?>">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-bold text-success">Giá Người lớn</label>
        <div class="input-group">
            <input type="text" class="form-control fw-bold text-end" 
                   value="<?php echo number_format($schedule['GiaNguoiLon']); ?>" 
                   onkeyup="formatCurrencyInput(this, 'price_adult')" required>
            <span class="input-group-text">đ</span>
        </div>
        <input type="hidden" name="price_adult" id="price_adult" value="<?php echo $schedule['GiaNguoiLon']; ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-info">Giá Trẻ em</label>
        <div class="input-group">
            <input type="text" class="form-control fw-bold text-end" 
                   value="<?php echo number_format($schedule['GiaTreEm']); ?>" 
                   onkeyup="formatCurrencyInput(this, 'price_child')">
            <span class="input-group-text">đ</span>
        </div>
        <input type="hidden" name="price_child" id="price_child" value="<?php echo $schedule['GiaTreEm']; ?>">
    </div>
</div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Giờ tập trung</label>
                                <input type="time" name="meeting_time" class="form-control" value="<?php echo date('H:i', strtotime($schedule['GioTapTrung'])); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Điểm đón</label>
                                <input type="text" name="meeting_place" class="form-control" value="<?php echo $schedule['DiaDiemTapTrung']; ?>">
                            </div>
                        </div>

                        <div class="alert alert-light border">
                            <label class="form-label fw-bold text-primary mb-2">Trạng thái vận hành</label>
                            <select name="status" class="form-select border-primary fw-bold text-dark">
                                <option value="Nhận khách" <?php echo ($schedule['TrangThai']=='Nhận khách')?'selected':''; ?>>🟢 Đang nhận khách</option>
                                <option value="Đã đóng" <?php echo ($schedule['TrangThai']=='Đã đóng')?'selected':''; ?>>🔴 Đã đóng sổ (Full)</option>
                                <option value="Đang chạy" <?php echo ($schedule['TrangThai']=='Đang chạy')?'selected':''; ?>>🟡 Đang chạy tour</option>
                                <option value="Hoàn tất" <?php echo ($schedule['TrangThai']=='Hoàn tất')?'selected':''; ?>>🏁 Hoàn tất</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Điều chỉnh Tài nguyên</h5>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Hướng dẫn viên</h6>
                        <div class="list-group mb-4" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach($all_staffs as $s): ?>
                            <label class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <input class="form-check-input flex-shrink-0" type="checkbox" name="staffs[]" 
                                    value="<?php echo $s['MaNhanSu']; ?>" 
                                    <?php echo in_array($s['MaNhanSu'], $assigned_staffs) ? 'checked' : ''; ?>>
                                <div>
                                    <div class="fw-bold small"><?php echo $s['HoTen']; ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Xe & Khách sạn</h6>
                        <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach($all_resources as $res): ?>
                            <label class="list-group-item list-group-item-action d-flex align-items-start gap-2">
                                <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="resources[]" 
                                    value="<?php echo $res['MaTaiNguyen']; ?>" 
                                    <?php echo in_array($res['MaTaiNguyen'], $assigned_resources) ? 'checked' : ''; ?>>
                                <div class="small">
                                    <div class="fw-bold"><?php echo $res['TenTaiNguyen']; ?></div>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?php echo $res['TenNhaCungCap']; ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                Lưu Cập Nhật
            </button>
            <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary btn-lg ms-2">Hủy</a>
        </div>
    </form>
</section>

<script>
    function formatCurrencyInput(input, hiddenId) {
        let value = input.value.replace(/\D/g, '');
        document.getElementById(hiddenId).value = value;
        input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
    }
</script>