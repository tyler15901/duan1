<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">Tạo Đơn Hàng Mới</h2>
    <a href="<?php echo BASE_URL; ?>/booking/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/booking/store" method="POST" id="bookingForm" class="needs-validation" novalidate>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-info-circle"></i> 1. Thông tin Tour & Lịch trình</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                            <select name="tour_id" id="tour_select" class="form-select form-select-lg" required onchange="loadSchedules()">
                                <option value="">-- Tìm kiếm và chọn Tour --</option>
                                <?php foreach($tours as $t): ?>
                                    <option value="<?php echo $t['MaTour']; ?>"><?php echo $t['TenTour']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Không thấy tour? <a href="<?php echo BASE_URL; ?>/tour/create" target="_blank">Tạo Tour mới</a> rồi tải lại trang.
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Chọn Ngày Khởi Hành <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="lich_id" id="schedule_select" class="form-select" required disabled>
                                    <option value="">-- Vui lòng chọn Tour trước --</option>
                                </select>
                                <button class="btn btn-light border" type="button" onclick="loadSchedules()" title="Tải lại">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div id="schedule_message" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-success fw-bold"><i class="bi bi-people"></i> 2. Thông tin Khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" class="form-control" required placeholder="09xxx...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" class="form-control" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Số lượng khách <span class="text-danger">*</span></label>
                            <input type="number" name="so_luong" id="pax_count" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-header bg-warning bg-opacity-10 py-3 border-bottom-0">
                    <h5 class="mb-0 text-warning text-dark fw-bold"><i class="bi bi-wallet2"></i> 3. Thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tổng tiền (VNĐ)</label>
                        <input type="text" id="display_money" class="form-control form-control-lg fw-bold text-primary" placeholder="0" onkeyup="formatCurrency(this)">
                        <input type="hidden" name="tong_tien" id="real_money">
                        <div class="form-text small">Nhập số tiền, hệ thống tự thêm dấu phẩy.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái TT</label>
                        <select name="trang_thai_tt" class="form-select">
                            <option value="Chưa thanh toán">Chưa thanh toán</option>
                            <option value="Đã cọc">Đã đặt cọc</option>
                            <option value="Đã thanh toán">Đã thanh toán hết</option>
                        </select>
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
                        <i class="bi bi-check-lg"></i> Xác nhận Đặt Tour
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Hàm format tiền tệ (Hiển thị 1,000,000)
    function formatCurrency(input) {
        // Xóa mọi ký tự không phải số
        let value = input.value.replace(/\D/g, '');
        // Cập nhật giá trị thực vào input hidden
        document.getElementById('real_money').value = value;
        // Format lại hiển thị
        input.value = new Intl.NumberFormat('vi-VN').format(value);
    }

    function loadSchedules() {
        const tourId = document.getElementById('tour_select').value;
        const scheduleSelect = document.getElementById('schedule_select');
        const messageDiv = document.getElementById('schedule_message');

        // Reset UI Loading
        scheduleSelect.innerHTML = '<option value="">Đang tải dữ liệu...</option>';
        scheduleSelect.disabled = true;
        messageDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Đang tìm lịch...';

        if (!tourId) {
            scheduleSelect.innerHTML = '<option value="">-- Vui lòng chọn Tour trước --</option>';
            messageDiv.innerHTML = '';
            return;
        }

        // Fetch Data
        fetch('<?php echo BASE_URL; ?>/booking/get_schedules?tour_id=' + tourId)
            .then(response => response.json())
            .then(data => {
                scheduleSelect.innerHTML = '<option value="">-- Chọn ngày khởi hành --</option>';
                
                if (data.length > 0) {
                    messageDiv.innerHTML = ''; // Clear loading
                    data.forEach(item => {
                        const slotsLeft = item.SoChoToiDa - item.SoKhachHienTai;
                        const dateStr = new Date(item.NgayKhoiHanh).toLocaleDateString('vi-VN');
                        const option = document.createElement('option');
                        
                        option.value = item.MaLichKhoiHanh;
                        option.text = `[${dateStr}] - ${item.LichCode} (Còn ${slotsLeft} chỗ)`;
                        option.setAttribute('data-slots', slotsLeft);
                        
                        if (slotsLeft <= 0) {
                            option.disabled = true;
                            option.text += ' - HẾT CHỖ';
                        }

                        scheduleSelect.add(option);
                    });
                    scheduleSelect.disabled = false;
                } else {
                    scheduleSelect.innerHTML = '<option value="">Không có lịch phù hợp</option>';
                    // Hiển thị nút tạo lịch nhanh
                    messageDiv.innerHTML = `
                        <div class="alert alert-warning d-flex align-items-center mt-2 p-2">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                Tour này chưa có lịch khởi hành. 
                                <a href="<?php echo BASE_URL; ?>/schedule/create?tour_id=${tourId}" target="_blank" class="fw-bold text-dark text-decoration-underline">Tạo lịch ngay</a>
                            </div>
                        </div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = '<span class="text-danger">Lỗi kết nối server!</span>';
            });
    }
    
    // Logic cập nhật max slot
    document.getElementById('schedule_select').addEventListener('change', function() {
        const messageDiv = document.getElementById('schedule_message');
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const slots = selectedOption.getAttribute('data-slots');
            messageDiv.innerHTML = `<span class="badge bg-info text-dark">Lịch trình OK</span> <span class="text-success fw-bold">Còn ${slots} chỗ trống</span>`;
            document.getElementById('pax_count').max = slots;
        } else {
            messageDiv.innerHTML = '';
        }
    });
</script>