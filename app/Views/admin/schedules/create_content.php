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
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">1. Thông tin Lịch khởi hành</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn Tour du lịch <span class="text-danger">*</span></label>
                            <select name="tour_id" id="tour_select" class="form-select" required onchange="updateTourInfo()">
                                <option value="" data-days="0" data-price-adult="0" data-price-child="0">-- Chọn Tour --</option>
                                <?php foreach ($tours as $t): ?>
                                    <option value="<?php echo $t['MaTour']; ?>" 
                                            data-days="<?php echo $t['SoNgay']; ?>"
                                            data-min="<?php echo $t['SoKhachToiThieu'] ?? 15; ?>"
                                            >
                                        <?php echo $t['TenTour']; ?> (<?php echo $t['SoNgay']; ?> ngày)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày khởi hành <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calcEndDate()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày về (Dự kiến)</label>
                                <input type="date" name="end_date" id="end_date" class="form-control bg-light" readonly>
                            </div>
                        </div>

                        <div class="row g-3 mb-3 bg-light p-3 rounded mx-1 border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Số khách tối đa</label>
                                <input type="number" name="so_cho_toi_da" class="form-control fw-bold" value="30" min="1" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">Số khách tối thiểu<span class="text-danger"></span></label>
                                <input type="number" name="so_cho_min" id="so_cho_min" class="form-control fw-bold border-danger" value="15" min="1" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">Giá vé Người lớn (VNĐ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" id="price_adult_display"
                                        onkeyup="formatCurrencyInput(this, 'price_adult')" required placeholder="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_adult" id="price_adult">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-info">Giá vé Trẻ em (VNĐ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control fw-bold text-end" id="price_child_display"
                                        onkeyup="formatCurrencyInput(this, 'price_child')" placeholder="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <input type="hidden" name="price_child" id="price_child">
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

            <div class="col-lg-4">
                

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">2. Tài nguyên dự kiến</h5>
                        
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i> Hướng dẫn viên sẽ được phân bổ sau khi lịch đủ điều kiện khởi hành.
                        </div>

                        <h6 class="fw-bold small text-muted text-uppercase mb-2 mt-3">Xe & Đối tác liên kết</h6>
                        <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($resources as $res): ?>
                                <label class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0" type="checkbox" name="resources[]"
                                        value="<?php echo $res['MaTaiNguyen']; ?>">
                                    <div class="small">
                                        <div class="fw-bold"><?php echo $res['TenTaiNguyen']; ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">
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

        <div class="text-center mt-3 mb-5">
            <button type="submit" class="btn btn-primary btn-lg shadow px-5">
                <i class="bi bi-check-circle-fill me-2"></i> Tạo Lịch Mới
            </button>
            <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary btn-lg ms-2">Hủy</a>
        </div>
    </form>
</section>

<script>
    // Hàm format tiền tệ
    function formatCurrencyInput(input) {
    // 1. Lấy giá trị thô
    let value = input.value;

    // 2. Nếu phát hiện dấu phẩy hoặc chấm ở cuối (dạng thập phân), cắt bỏ phần sau nó
    // Ví dụ: 1.000.000,00 -> Lấy 1.000.000
    if (value.indexOf(',') !== -1) {
        value = value.split(',')[0]; 
    } else if (value.indexOf('.') !== -1 && value.split('.').length > 1) {
        // Trường hợp nhập 100.00 (kiểu Mỹ), logic này phức tạp hơn vì dấu . cũng là ngăn cách nghìn
        // Nên ta dùng cách đơn giản: Chỉ giữ lại số
    }

    // 3. Xóa hết các ký tự không phải số
    value = value.replace(/\D/g, ''); 

    // 4. Định dạng lại
    input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
}

    function updateTourInfo() {
        calcEndDate();
        // Có thể thêm logic lấy giá mặc định từ Tour nếu cần (qua AJAX hoặc data attribute)
        // Hiện tại để trống để người dùng tự nhập cho linh hoạt
        
        // Cập nhật số khách tối thiểu mặc định từ Tour
        const tourSelect = document.getElementById('tour_select');
        const minPax = parseInt(tourSelect.options[tourSelect.selectedIndex].getAttribute('data-min')) || 15;
        document.getElementById('so_cho_min').value = minPax;
    }

    function calcEndDate() {
        const tourSelect = document.getElementById('tour_select');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        const days = parseInt(tourSelect.options[tourSelect.selectedIndex].getAttribute('data-days')) || 0;
        const startDateStr = startDateInput.value;

        if (startDateStr && days > 0) {
            const date = new Date(startDateStr);
            date.setDate(date.getDate() + days - 1);
            endDateInput.value = date.toISOString().split('T')[0];
        }
    }
</script>