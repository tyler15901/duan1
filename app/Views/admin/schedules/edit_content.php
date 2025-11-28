<h2 class="mb-4">Chỉnh sửa Lịch: <?php echo $schedule['LichCode']; ?></h2>

<form action="<?php echo BASE_URL; ?>/schedule/update/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">1. Thời gian & Địa điểm</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Tour</label>
                        <select name="tour_id" class="form-select bg-light" readonly style="pointer-events: none;">
                            <option value="<?php echo $schedule['MaTour']; ?>"><?php echo $schedule['TenTour']; ?></option>
                        </select>
                        <small class="text-danger">Không thể đổi tour khi đang sửa lịch (Hãy xóa đi tạo mới nếu cần)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày khởi hành</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $schedule['NgayKhoiHanh']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $schedule['NgayKetThuc']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Giờ tập trung</label>
                            <input type="time" name="meeting_time" class="form-control" value="<?php echo date('H:i', strtotime($schedule['GioTapTrung'])); ?>">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Điểm đón</label>
                            <input type="text" name="meeting_place" class="form-control" value="<?php echo $schedule['DiaDiemTapTrung']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="Nhận khách" <?php echo ($schedule['TrangThai']=='Nhận khách')?'selected':''; ?>>Nhận khách</option>
                            <option value="Đã đóng" <?php echo ($schedule['TrangThai']=='Đã đóng')?'selected':''; ?>>Đã đóng</option>
                            <option value="Đang chạy" <?php echo ($schedule['TrangThai']=='Đang chạy')?'selected':''; ?>>Đang chạy</option>
                            <option value="Hoàn tất" <?php echo ($schedule['TrangThai']=='Hoàn tất')?'selected':''; ?>>Hoàn tất</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">2. Điều phối Tài nguyên</div>
                <div class="card-body">
                    
                    <h6 class="fw-bold border-bottom pb-2">Hướng dẫn viên</h6>
                    <div class="mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach($all_staffs as $s): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="staffs[]" 
                                value="<?php echo $s['MaNhanSu']; ?>" 
                                id="staff_<?php echo $s['MaNhanSu']; ?>"
                                <?php echo in_array($s['MaNhanSu'], $assigned_staffs) ? 'checked' : ''; ?>
                            >
                            <label class="form-check-label" for="staff_<?php echo $s['MaNhanSu']; ?>">
                                <?php echo $s['HoTen']; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mt-3">Phương tiện & Khách sạn</h6>
                    <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach($all_resources as $res): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="resources[]" 
                                value="<?php echo $res['MaTaiNguyen']; ?>" 
                                id="res_<?php echo $res['MaTaiNguyen']; ?>"
                                <?php echo in_array($res['MaTaiNguyen'], $assigned_resources) ? 'checked' : ''; ?>
                            >
                            <label class="form-check-label" for="res_<?php echo $res['MaTaiNguyen']; ?>">
                                <strong><?php echo $res['TenTaiNguyen']; ?></strong> <br>
                                <small class="text-muted">- <?php echo $res['TenNhaCungCap']; ?></small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary px-4">Cập nhật Lịch</button>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary">Hủy bỏ</a>
</form>