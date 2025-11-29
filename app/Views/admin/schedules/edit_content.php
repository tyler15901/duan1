<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Cập nhật Lịch trình</h4>
        <h2 class="text-primary fw-bold"><?php echo $schedule['LichCode']; ?></h2>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/schedule/update/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square"></i> Thông tin chung</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tour (Đã cố định)</label>
                        <input type="text" class="form-control bg-light fw-bold" value="<?php echo $schedule['TenTour']; ?>" readonly>
                        <input type="hidden" name="tour_id" value="<?php echo $schedule['MaTour']; ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Ngày khởi hành</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $schedule['NgayKhoiHanh']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $schedule['NgayKetThuc']; ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Giờ tập trung</label>
                            <input type="time" name="meeting_time" class="form-control" value="<?php echo date('H:i', strtotime($schedule['GioTapTrung'])); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Điểm đón</label>
                            <input type="text" name="meeting_place" class="form-control" value="<?php echo $schedule['DiaDiemTapTrung']; ?>">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border border-warning border-start-4">
                        <label class="form-label fw-bold text-warning mb-2">Trạng thái vận hành</label>
                        <select name="status" class="form-select border-warning text-dark fw-bold">
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
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear"></i> Điều chỉnh Tài nguyên</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Hướng dẫn viên</h6>
                    <div class="list-group mb-4" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach($all_staffs as $s): ?>
                        <label class="list-group-item list-group-item-action">
                            <input class="form-check-input me-2" type="checkbox" name="staffs[]" 
                                value="<?php echo $s['MaNhanSu']; ?>" 
                                <?php echo in_array($s['MaNhanSu'], $assigned_staffs) ? 'checked' : ''; ?>>
                            <?php echo $s['HoTen']; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Xe & Khách sạn</h6>
                    <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach($all_resources as $res): ?>
                        <label class="list-group-item list-group-item-action">
                            <input class="form-check-input me-2" type="checkbox" name="resources[]" 
                                value="<?php echo $res['MaTaiNguyen']; ?>" 
                                <?php echo in_array($res['MaTaiNguyen'], $assigned_resources) ? 'checked' : ''; ?>>
                            <span><?php echo $res['TenTaiNguyen']; ?></span>
                            <small class="d-block text-muted ps-4"><?php echo $res['TenNhaCungCap']; ?></small>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-warning px-5 fw-bold shadow">
            <i class="bi bi-check-circle"></i> Cập nhật thay đổi
        </button>
    </div>
</form>