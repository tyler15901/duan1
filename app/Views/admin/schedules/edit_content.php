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
                            <input type="text" class="form-control bg-light fw-bold" value="<?php echo $schedule['TenTour']; ?>" readonly>
                            <input type="hidden" name="tour_id" value="<?php echo $schedule['MaTour']; ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày khởi hành</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="<?php echo $schedule['NgayKhoiHanh']; ?>" 
                                       onchange="checkGuidesAvailability()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày kết thúc</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="<?php echo $schedule['NgayKetThuc']; ?>"
                                       onchange="checkGuidesAvailability()">
                            </div>
                        </div>

                        <div class="row g-3 mb-3 bg-light p-3 rounded mx-1 border">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số chỗ Tối đa</label>
                                <input type="number" name="so_cho_toi_da" class="form-control" value="<?php echo $schedule['SoChoToiDa']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">Số khách Tối thiểu</label>
                                <input type="number" name="so_cho_min" class="form-control border-danger" value="<?php echo $schedule['SoChoMin']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Trạng thái</label>
                                <select name="status" class="form-select border-primary fw-bold text-dark">
                                    <option value="Đang gom khách" <?php echo ($schedule['TrangThai']=='Đang gom khách')?'selected':''; ?>>⚪ Đang gom khách</option>
                                    <option value="Nhận khách" <?php echo ($schedule['TrangThai']=='Nhận khách')?'selected':''; ?>>🟢 Nhận khách</option>
                                    <option value="Đã đóng sổ" <?php echo ($schedule['TrangThai']=='Đã đóng sổ')?'selected':''; ?>>🔴 Đã đóng</option>
                                    <option value="Hủy chuyến" <?php echo ($schedule['TrangThai']=='Hủy chuyến')?'selected':''; ?>>⚫ Hủy chuyến</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">Giá Người lớn</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" 
                                           value="<?php echo number_format($schedule['GiaNguoiLon'], 0, '', '.'); ?>" 
                                           onkeyup="formatCurrencyInput(this, 'price_adult')" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_adult" id="price_adult" value="<?php echo (int)$schedule['GiaNguoiLon']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-info">Giá Trẻ em</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" 
                                           value="<?php echo number_format($schedule['GiaTreEm'], 0, '', '.'); ?>" 
                                           onkeyup="formatCurrencyInput(this, 'price_child')">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_child" id="price_child" value="<?php echo (int)$schedule['GiaTreEm']; ?>">
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
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Điều chỉnh Tài nguyên</h5>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Hướng dẫn viên</h6>
                        
                        <?php if ($schedule['SoKhachHienTai'] < $schedule['SoChoMin']): ?>
                            <div class="alert alert-warning small border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Chưa đủ khách tối thiểu (<?php echo $schedule['SoKhachHienTai']; ?>/<?php echo $schedule['SoChoMin']; ?>).
                                <br><strong>Chưa thể phân bổ Hướng dẫn viên.</strong>
                            </div>
                        <?php else: ?>
                            <div id="guide_list_container" class="list-group mb-4 border" style="max-height: 300px; overflow-y: auto; min-height: 100px;">
                                <div class="text-center p-3 text-muted small"><i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-1"></i> Đang kiểm tra lịch bận...</div>
                            </div>
                            <div class="form-text small text-muted mb-3 fst-italic">
                                <i class="bi bi-info-circle"></i> Trạng thái "Bận" được tính dựa trên các lịch trình khác trùng ngày (ngoại trừ lịch hiện tại).
                            </div>
                        <?php endif; ?>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2 mt-3">Xe & Khách sạn</h6>
                        <div class="list-group border" style="max-height: 250px; overflow-y: auto;">
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
    // Dữ liệu từ PHP truyền sang JS để biết ai đang được chọn
    const assignedStaffIds = <?php echo json_encode($assigned_staffs); ?>; 
    const currentScheduleId = <?php echo $schedule['MaLichKhoiHanh']; ?>;

    // Chạy ngay khi tải trang để hiện trạng thái
    document.addEventListener("DOMContentLoaded", function() {
        checkGuidesAvailability();
    });

    // 1. Hàm kiểm tra HDV (AJAX)
    function checkGuidesAvailability() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        const container = document.getElementById('guide_list_container');

        if (!container) return; // Nếu bị ẩn do chưa đủ khách thì thoát

        // Gọi API check_guides
        // Truyền thêm tham số &id=... để loại trừ lịch này khỏi check trùng
        fetch(`<?php echo BASE_URL; ?>/schedule/check_guides?start=${start}&end=${end}&id=${currentScheduleId}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = ''; // Xóa nội dung cũ
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="p-3 text-center text-muted small">Không có dữ liệu HDV.</div>';
                } else {
                    data.forEach(g => {
                        // Kiểm tra xem ông này có đang được chọn trong lịch này không
                        // Lưu ý: assignedStaffIds chứa các ID dạng string hoặc number, nên so sánh lỏng (==)
                        const isChecked = assignedStaffIds.some(id => id == g.MaNhanSu);
                        
                        const isBusy = g.is_busy;
                        const badgeClass = isBusy ? 'bg-danger' : 'bg-success';
                        const badgeText = isBusy ? 'Bận lịch khác' : 'Đang rảnh';
                        // Nếu bận thì làm mờ dòng đó đi, nhưng vẫn cho click nếu admin muốn ép
                        const rowStyle = isBusy ? 'background-color: #fff5f5;' : ''; 
                        
                        const html = `
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2" style="${rowStyle}">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" name="staffs[]" 
                                           value="${g.MaNhanSu}" 
                                           ${isChecked ? 'checked' : ''}>
                                    <div>
                                        <div class="fw-bold small text-dark">${g.HoTen}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">${g.SoDienThoai}</div>
                                    </div>
                                </div>
                                <span class="badge ${badgeClass}" style="font-size: 0.65rem;">${badgeText}</span>
                            </label>
                        `;
                        container.insertAdjacentHTML('beforeend', html);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div class="text-danger small p-2">Lỗi kết nối server.</div>';
            });
    }

    // 2. Hàm format tiền tệ
    function formatCurrencyInput(input, hiddenId) {
        let value = input.value;
        if (value.indexOf(',') !== -1) {
            value = value.split(',')[0]; 
        }
        value = value.replace(/\D/g, ''); 
        if(hiddenId) {
            document.getElementById(hiddenId).value = value;
        }
        input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
    }
</script>