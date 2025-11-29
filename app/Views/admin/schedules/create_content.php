<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary fw-bold">Khởi tạo Lịch trình</h3>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-outline-secondary">
        <i class="bi bi-x-lg"></i> Hủy bỏ
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/schedule/store" method="POST" class="needs-validation">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary"></i> 1. Thời gian & Địa điểm</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted">Tour du lịch <span class="text-danger">*</span></label>
                        <select name="tour_id" id="tour_select" class="form-select form-select-lg fw-bold text-primary" required onchange="calcEndDate()">
                            <option value="" data-days="0">-- Vui lòng chọn Tour --</option>
                            <?php foreach ($tours as $t): ?>
                                <option value="<?php echo $t['MaTour']; ?>" data-days="<?php echo $t['SoNgay']; ?>">
                                    <?php echo $t['TenTour']; ?> (<?php echo $t['SoNgay']; ?> ngày)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Ngày khởi hành</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calcEndDate()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Ngày kết thúc (Dự kiến)</label>
                            <input type="date" name="end_date" id="end_date" class="form-control bg-light" readonly>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Giờ tập trung</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-alarm"></i></span>
                                <input type="time" name="meeting_time" class="form-control" value="05:30">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Điểm đón khách</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="meeting_place" class="form-control" placeholder="VD: Cổng Công viên Thống Nhất...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-layers text-success"></i> 2. Điều phối Tài nguyên</h5>
                </div>
                <div class="card-body">

                    <h6 class="fw-bold text-dark border-bottom pb-2">Hướng dẫn viên</h6>
                    
                    <div class="mb-4 shadow-sm" id="guide_container" style="max-height: 200px; overflow-y: auto; border: 1px solid #e9ecef; min-height: 50px;">
                        <div class="text-muted small fst-italic p-3 text-center">
                            <i class="bi bi-info-circle"></i> Vui lòng chọn <b>Ngày khởi hành</b> để hệ thống kiểm tra lịch làm việc của HDV.
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mt-4">Xe & Khách sạn (Đối tác)</h6>
                    <div class="list-group shadow-sm" style="max-height: 250px; overflow-y: auto; border: 1px solid #e9ecef;">
                        <?php foreach ($resources as $res): ?>
                            <label class="list-group-item list-group-item-action d-flex align-items-start">
                                <input class="form-check-input me-3 mt-2" type="checkbox" name="resources[]" value="<?php echo $res['MaTaiNguyen']; ?>">
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $res['TenTaiNguyen']; ?></div>
                                    <div class="small text-muted">
                                        <span class="badge bg-light text-dark border"><?php echo $res['LoaiCungCap']; ?></span>
                                        <?php echo $res['TenNhaCungCap']; ?>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-bottom bg-white border-top py-3 shadow" style="padding-left: 270px; z-index: 99;">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
            <div class="text-muted small">
                <i class="bi bi-info-circle"></i> Vui lòng kiểm tra kỹ ngày khởi hành trước khi lưu.
            </div>
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                <i class="bi bi-save"></i> Hoàn tất & Lưu
            </button>
        </div>
    </div>
</form>

<br><br><br>

<script>
    // Hàm tính ngày kết thúc & gọi load HDV
    function calcEndDate() {
        const tourSelect = document.getElementById('tour_select');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        const days = parseInt(tourSelect.options[tourSelect.selectedIndex].getAttribute('data-days')) || 0;
        const startDateStr = startDateInput.value;

        if (startDateStr && days > 0) {
            const date = new Date(startDateStr);
            date.setDate(date.getDate() + days - 1);
            
            // Format YYYY-MM-DD
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            
            const endDateStr = `${yyyy}-${mm}-${dd}`;
            endDateInput.value = endDateStr;

            // SAU KHI CÓ NGÀY -> GỌI HÀM CHECK HDV
            loadAvailableGuides(startDateStr, endDateStr);
        }
    }

    // --- HÀM LOAD HDV THEO NGÀY ---
    function loadAvailableGuides(start, end) {
        const container = document.getElementById('guide_container');
        // Hiển thị trạng thái đang tải
        container.innerHTML = '<div class="text-primary p-3 text-center"><span class="spinner-border spinner-border-sm"></span> Đang kiểm tra lịch làm việc...</div>';

        fetch(`<?php echo BASE_URL; ?>/schedule/check_guides?start=${start}&end=${end}`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = ''; // Xóa sạch nội dung loading
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="text-danger p-3 text-center">Không tìm thấy HDV nào trong hệ thống.</div>';
                    return;
                }

                // Render danh sách mới
                let htmlContent = '<div class="list-group list-group-flush">';
                
                data.forEach(g => {
                    const isBusy = g.is_busy;
                    const disabledAttr = isBusy ? 'disabled' : '';
                    const colorClass = isBusy ? 'list-group-item-light text-muted' : 'list-group-item-action';
                    const textDecor = isBusy ? 'text-decoration-line-through' : 'fw-bold';
                    const busyLabel = isBusy ? '<span class="badge bg-danger ms-auto">Đang bận</span>' : '<span class="badge bg-success bg-opacity-10 text-success ms-auto">Rảnh</span>';

                    htmlContent += `
                        <label class="list-group-item ${colorClass} d-flex align-items-center">
                            <input class="form-check-input me-3" type="checkbox" name="staffs[]" 
                                value="${g.MaNhanSu}" id="staff_${g.MaNhanSu}" ${disabledAttr}>
                            
                            <div class="flex-grow-1">
                                <div class="${textDecor}">${g.HoTen}</div>
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    ${g.SoDienThoai}
                                </small>
                            </div>
                            ${busyLabel}
                        </label>
                    `;
                });
                
                htmlContent += '</div>';
                container.innerHTML = htmlContent;
            })
            .catch(error => {
                console.error(error);
                container.innerHTML = '<div class="text-danger p-3 text-center">Lỗi kết nối server!</div>';
            });
    }
</script>