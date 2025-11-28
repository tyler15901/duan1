<h2 class="mb-4">Tạo Đơn Hàng Mới</h2>

<form action="<?php echo BASE_URL; ?>/booking/store" method="POST" id="bookingForm">
    <div class="row">
        
        <div class="col-md-6">
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white fw-bold">1. Chọn Sản phẩm</div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <label class="fw-bold">Chọn Tour (*)</label> 
                        <span class="float-end"><a href="<?php echo BASE_URL; ?>/tour/create" target="_blank" class="text-decoration-none small"><i class="bi bi-plus-circle"></i> Tạo Tour Mới (Case 3)</a></span>
                        
                        <select name="tour_id" id="tour_select" class="form-select" required onchange="loadSchedules()">
                            <option value="">-- Vui lòng chọn Tour --</option>
                            <?php foreach($tours as $t): ?>
                                <option value="<?php echo $t['MaTour']; ?>"><?php echo $t['TenTour']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted fst-italic">Nếu là Tour mới, hãy bấm link 'Tạo Tour Mới' ở trên, sau đó F5 lại trang này.</small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Chọn Lịch Khởi Hành (*)</label>
                        <div class="input-group">
                            <select name="lich_id" id="schedule_select" class="form-select" required disabled>
                                <option value="">-- Chọn Tour trước --</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" onclick="loadSchedules()" title="Tải lại danh sách lịch">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        
                        <div id="create_schedule_link" class="mt-2 d-none">
                            <span class="text-danger"><i class="bi bi-exclamation-circle"></i> Chưa có lịch phù hợp?</span>
                            <a href="#" id="btn_new_schedule" target="_blank" class="fw-bold">
                                <i class="bi bi-plus-lg"></i> Tạo lịch mới cho tour này
                            </a>
                        </div>
                    </div>

                    <div id="schedule_info" class="alert alert-info d-none">
                        <strong>Trạng thái:</strong> <span id="info_status"></span> <br>
                        <strong>Còn trống:</strong> <span id="info_slots" class="fw-bold text-danger"></span> chỗ
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white fw-bold">2. Thông tin Khách hàng</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Số điện thoại (*)</label>
                        <input type="text" name="so_dien_thoai" class="form-control" required placeholder="Nhập SĐT để tìm hoặc tạo mới">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Họ và tên (*)</label>
                        <input type="text" name="ho_ten" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Số lượng khách (*)</label>
                        <input type="number" name="so_luong" id="pax_count" class="form-control" value="1" min="1" required>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark fw-bold">3. Thanh toán</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Tổng tiền (VNĐ)</label>
                        <input type="number" name="tong_tien" class="form-control" required placeholder="Nhập số tiền...">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Trạng thái thanh toán</label>
                        <select name="trang_thai_tt" class="form-select">
                            <option value="Chưa thanh toán">Chưa thanh toán</option>
                            <option value="Đã cọc">Đã đặt cọc (50%)</option>
                            <option value="Đã thanh toán">Đã thanh toán hết</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="<?php echo BASE_URL; ?>/booking/index" class="btn btn-secondary me-md-2">Hủy bỏ</a>
        <button type="submit" class="btn btn-primary btn-lg px-5">Xác nhận tạo đơn</button>
    </div>
</form>

<script>
    function loadSchedules() {
        const tourId = document.getElementById('tour_select').value;
        const scheduleSelect = document.getElementById('schedule_select');
        const createLinkDiv = document.getElementById('create_schedule_link');
        const btnNewSchedule = document.getElementById('btn_new_schedule');
        const infoDiv = document.getElementById('schedule_info');

        // Reset
        scheduleSelect.innerHTML = '<option value="">Đang tải...</option>';
        scheduleSelect.disabled = true;
        createLinkDiv.classList.add('d-none');
        infoDiv.classList.add('d-none');

        if (!tourId) {
            scheduleSelect.innerHTML = '<option value="">-- Chọn Tour trước --</option>';
            return;
        }

        // Cập nhật link tạo lịch mới (Case 2)
        // Link này sẽ mở tab mới đến trang tạo lịch, với tour_id đã chọn sẵn
        // Lưu ý: Logic này giả định bạn có thể truyền tour_id qua URL ở trang Create Schedule (cần chỉnh sửa trang đó xíu nếu muốn tự động chọn)
        btnNewSchedule.href = "<?php echo BASE_URL; ?>/schedule/create?tour_id=" + tourId;
        createLinkDiv.classList.remove('d-none');

        // Gọi AJAX
        fetch('<?php echo BASE_URL; ?>/booking/get_schedules?tour_id=' + tourId)
            .then(response => response.json())
            .then(data => {
                scheduleSelect.innerHTML = '<option value="">-- Chọn ngày khởi hành --</option>';
                
                if (data.length > 0) {
                    data.forEach(item => {
                        const slotsLeft = item.SoChoToiDa - item.SoKhachHienTai;
                        // Format ngày
                        const dateStr = new Date(item.NgayKhoiHanh).toLocaleDateString('vi-VN');
                        
                        const option = document.createElement('option');
                        option.value = item.MaLichKhoiHanh;
                        option.text = `${item.LichCode} - Khởi hành: ${dateStr} (Còn ${slotsLeft} chỗ)`;
                        
                        // Lưu data vào attribute để dùng sau
                        option.setAttribute('data-slots', slotsLeft);
                        
                        scheduleSelect.add(option);
                    });
                    scheduleSelect.disabled = false;
                } else {
                    scheduleSelect.innerHTML = '<option value="">Chưa có lịch cho tour này</option>';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Sự kiện khi chọn Lịch -> Hiện thông tin chỗ trống
    document.getElementById('schedule_select').addEventListener('change', function() {
        const infoDiv = document.getElementById('schedule_info');
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const slots = selectedOption.getAttribute('data-slots');
            document.getElementById('info_status').innerText = "Đang nhận khách";
            document.getElementById('info_slots').innerText = slots;
            infoDiv.classList.remove('d-none');
            
            // Validate số lượng khách nhập vào
            const paxInput = document.getElementById('pax_count');
            paxInput.max = slots; // Không cho nhập quá số chỗ còn lại
        } else {
            infoDiv.classList.add('d-none');
        }
    });
</script>