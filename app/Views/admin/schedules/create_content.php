<div class="pagetitle">
    <h1>Khởi tạo Lịch trình</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/schedule/index">Lịch trình</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/schedule/store" method="POST" class="needs-validation">
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">1. Thời gian & Địa điểm</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="tour_select" class="form-select" required onchange="calcEndDate()">
                                    <option value="" data-days="0">-- Chọn Tour du lịch --</option>
                                    <?php foreach ($tours as $t): ?>
                                        <option value="<?php echo $t['MaTour']; ?>" data-days="<?php echo $t['SoNgay']; ?>">
                                            <?php echo $t['TenTour']; ?> (<?php echo $t['SoNgay']; ?> ngày)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">Số chỗ mở bán</label>
                                <input type="number" name="so_cho_toi_da" class="form-control" value="20" min="1" required>
                                <div class="form-text small">VD: Xe 29 chỗ, 45 chỗ...</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày khởi hành</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calcEndDate()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày về (Dự kiến)</label>
                                <input type="date" name="end_date" id="end_date" class="form-control bg-light" readonly>
                            </div>
                        </div>

                        <div class="row g-3 mb-3 bg-light p-3 rounded mx-1 border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">Giá Người lớn (VNĐ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" id="price_adult_display"
                                        onkeyup="formatCurrencyInput(this, 'price_adult')" required placeholder="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_adult" id="price_adult">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-info">Giá Trẻ em (VNĐ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" id="price_child_display"
                                        onkeyup="formatCurrencyInput(this, 'price_child')" placeholder="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_child" id="price_child">
                            </div>
                            <div class="col-12 form-text small text-muted mb-0">
                                <i class="bi bi-info-circle"></i> Giá này áp dụng riêng cho lịch khởi hành này.
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Giờ tập trung</label>
                                <input type="time" name="meeting_time" class="form-control" value="05:30">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Điểm đón khách</label>
                                <input type="text" name="meeting_place" class="form-control"
                                    placeholder="VD: Cổng Công viên Thống Nhất...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">2. Điều phối Tài nguyên</h5>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Hướng dẫn viên</h6>
                        <div class="border rounded p-2 mb-4 bg-light" id="guide_container"
                            style="max-height: 200px; overflow-y: auto; min-height: 60px;">
                            <div class="text-muted small text-center pt-3">
                                <i class="bi bi-calendar-event"></i> Chọn ngày khởi hành để xem HDV rảnh.
                            </div>
                        </div>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Xe & Đối tác</h6>
                        <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach ($resources as $res): ?>
                                <label class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0" type="checkbox" name="resources[]"
                                        value="<?php echo $res['MaTaiNguyen']; ?>">
                                    <div class="small">
                                        <div class="fw-bold"><?php echo $res['TenTaiNguyen']; ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">
                                            <?php echo $res['TenNhaCungCap']; ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg shadow">
                <i class="bi bi-check-circle-fill me-2"></i> Hoàn tất & Lưu Lịch
            </button>
            <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary btn-lg ms-2">Hủy</a>
        </div>
    </form>
</section>

<script>
    // Hàm format tiền tệ (thêm dấu chấm)
    function formatCurrencyInput(input, hiddenId) {
        let value = input.value.replace(/\D/g, '');
        document.getElementById(hiddenId).value = value;
        input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
    }

    // Tính ngày về dựa trên số ngày tour
    function calcEndDate() {
        const tourSelect = document.getElementById('tour_select');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        // Lấy số ngày từ data attribute
        const days = parseInt(tourSelect.options[tourSelect.selectedIndex].getAttribute('data-days')) || 0;
        const startDateStr = startDateInput.value;

        if (startDateStr && days > 0) {
            const date = new Date(startDateStr);
            // Ngày về = Ngày đi + (Số ngày - 1)
            date.setDate(date.getDate() + days - 1);
            const endDateStr = date.toISOString().split('T')[0];
            endDateInput.value = endDateStr;
            
            // Gọi AJAX check HDV rảnh
            loadAvailableGuides(startDateStr, endDateStr);
        }
    }

    // Load danh sách HDV rảnh
    function loadAvailableGuides(start, end) {
        const container = document.getElementById('guide_container');
        container.innerHTML = '<div class="text-center p-2"><div class="spinner-border spinner-border-sm text-primary"></div> Checking...</div>';

        fetch(`<?php echo BASE_URL; ?>/schedule/check_guides?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.length === 0) {
                    container.innerHTML = '<div class="text-danger small text-center">Không có HDV trống lịch.</div>';
                    return;
                }
                let html = '<div class="list-group list-group-flush">';
                data.forEach(g => {
                    const isBusy = g.is_busy;
                    const style = isBusy ? 'opacity: 0.6; background: #f8f9fa;' : '';
                    const badge = isBusy ? '<span class="badge bg-danger ms-auto">Bận</span>' : '<span class="badge bg-success ms-auto">Rảnh</span>';

                    html += `<label class="list-group-item d-flex align-items-center" style="${style}">
                                <input class="form-check-input me-2" type="checkbox" name="staffs[]" value="${g.MaNhanSu}" ${isBusy ? 'disabled' : ''}>
                                <div class="small flex-grow-1">
                                    <div class="fw-bold">${g.HoTen}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">${g.SoDienThoai}</div>
                                </div>
                                ${badge}
                            </label>`;
                });
                html += '</div>';
                container.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div class="text-danger small text-center">Lỗi kết nối.</div>';
            });
    }
</script>