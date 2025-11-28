<h2 class="mb-4">Tạo Lịch Khởi Hành Mới</h2>

<form action="<?php echo BASE_URL; ?>/schedule/store" method="POST">
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">1. Thiết lập thời gian</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Chọn Tour (*)</label>
                        <select name="tour_id" id="tour_select" class="form-select" required onchange="calcEndDate()">
                            <option value="" data-days="0">-- Chọn Tour --</option>
                            <?php foreach($tours as $t): ?>
                                <option value="<?php echo $t['MaTour']; ?>" data-days="<?php echo $t['SoNgay']; ?>">
                                    <?php echo $t['TenTour']; ?> (<?php echo $t['SoNgay']; ?> ngày)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày khởi hành</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calcEndDate()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" readonly>
                            <small class="text-muted">Tự động tính theo số ngày của Tour</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Giờ tập trung</label>
                            <input type="time" name="meeting_time" class="form-control" value="05:30">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Điểm đón khách</label>
                            <input type="text" name="meeting_place" class="form-control" placeholder="VD: Nhà hát lớn Hà Nội">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">2. Gán Nhân sự & Tài nguyên</div>
                <div class="card-body">
                    
                    <h6 class="fw-bold text-dark border-bottom pb-2">Hướng dẫn viên</h6>
                    <div class="mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach($staffs as $s): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="staffs[]" value="<?php echo $s['MaNhanSu']; ?>" id="staff_<?php echo $s['MaNhanSu']; ?>">
                            <label class="form-check-label" for="staff_<?php echo $s['MaNhanSu']; ?>">
                                <?php echo $s['HoTen']; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mt-3">Phương tiện & Khách sạn</h6>
                    <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach($resources as $res): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="resources[]" value="<?php echo $res['MaTaiNguyen']; ?>" id="res_<?php echo $res['MaTaiNguyen']; ?>">
                            <label class="form-check-label" for="res_<?php echo $res['MaTaiNguyen']; ?>">
                                <strong><?php echo $res['TenTaiNguyen']; ?></strong> <br>
                                <small class="text-muted">- <?php echo $res['TenNhaCungCap']; ?> (<?php echo $res['LoaiCungCap']; ?>)</small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary btn-lg px-5">Tạo Lịch Trình</button>
</form>

<script>
// Hàm tự động tính ngày kết thúc
function calcEndDate() {
    const tourSelect = document.getElementById('tour_select');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    const days = parseInt(tourSelect.options[tourSelect.selectedIndex].getAttribute('data-days')) || 0;
    const startDateStr = startDateInput.value;

    if (startDateStr && days > 0) {
        const date = new Date(startDateStr);
        // Trừ 1 vì ngày đi cũng tính là 1 ngày (VD: Đi 3 ngày 26,27,28. Thì 26 + 3 - 1 = 28)
        date.setDate(date.getDate() + days - 1); 
        
        // Format lại thành YYYY-MM-DD
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        
        endDateInput.value = `${yyyy}-${mm}-${dd}`;
    }
}
</script>