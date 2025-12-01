<div class="pagetitle">
    <h1>Tạo Đơn Hàng Mới</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/booking/index">Đơn hàng</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/booking/store" method="POST" id="bookingForm">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="bi bi-geo-alt-fill me-2"></i> Thông tin Tour & Lịch trình</h5>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="tour_select" class="form-select" required onchange="loadSchedules()">
                                    <option value="">-- Chọn Tour --</option>
                                    <?php foreach ($tours as $t): ?>
                                        <option value="<?php echo $t['MaTour']; ?>"><?php echo $t['TenTour']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Chọn Lịch Khởi Hành <span class="text-danger">*</span></label>
                                <select name="lich_id" id="schedule_select" class="form-select" required disabled onchange="updatePriceInfo()">
                                    <option value="">-- Vui lòng chọn Tour trước --</option>
                                </select>
                                <div id="price_info" class="alert alert-light border mt-2 d-none">
                                    <div class="d-flex justify-content-between small">
                                        <span><i class="bi bi-person-fill"></i> Giá người lớn: <strong class="text-success" id="lbl_price_adult">0</strong> đ</span>
                                        <span><i class="bi bi-emoji-smile-fill"></i> Giá trẻ em: <strong class="text-info" id="lbl_price_child">0</strong> đ</span>
                                    </div>
                                    <input type="hidden" id="raw_price_adult" value="0">
                                    <input type="hidden" id="raw_price_child" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="bi bi-person-lines-fill me-2"></i> Khách hàng & Số lượng</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="so_dien_thoai" class="form-control" required placeholder="09xxxx">
                            </div>
                        </div>

                        <div class="row g-3 bg-light p-3 rounded border mx-1">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">Người lớn (>11t)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-standing"></i></span>
                                    <input type="number" name="sl_nguoi_lon" id="qty_adult" class="form-control fw-bold" value="1" min="1" required oninput="calculateTotal()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-info">Trẻ em (5-11t)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-emoji-smile"></i></span>
                                    <input type="number" name="sl_tre_em" id="qty_child" class="form-control fw-bold" value="0" min="0" oninput="calculateTotal()">
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <small class="text-muted" id="total_pax_lbl">Tổng: 1 khách</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-white border shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-header bg-warning text-dark fw-bold">
                        <i class="bi bi-calculator me-2"></i> Tạm tính
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tổng tiền đơn hàng</label>
                            <div class="input-group input-group-lg">
                                <input type="text" id="display_money" class="form-control fw-bold text-primary bg-white" value="0" readonly>
                                <span class="input-group-text bg-white">đ</span>
                            </div>
                            <input type="hidden" name="tong_tien" id="real_money" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái thanh toán</label>
                            <select name="trang_thai_tt" class="form-select">
                                <option value="Chưa thanh toán">⚪ Chưa thanh toán</option>
                                <option value="Đã cọc">🟡 Đã đặt cọc</option>
                                <option value="Đã thanh toán">🟢 Đã thanh toán (Full)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Tạo Booking
                            </button>
                            <a href="<?php echo BASE_URL; ?>/booking/index" class="btn btn-outline-secondary">Hủy bỏ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    // 1. Load Lịch trình (AJAX)
    function loadSchedules() {
        const tourId = document.getElementById('tour_select').value;
        const scheduleSelect = document.getElementById('schedule_select');
        const priceInfo = document.getElementById('price_info');

        scheduleSelect.innerHTML = '<option value="">Đang tải...</option>';
        scheduleSelect.disabled = true;
        priceInfo.classList.add('d-none'); // Ẩn bảng giá tạm thời

        if (!tourId) {
            scheduleSelect.innerHTML = '<option value="">-- Vui lòng chọn Tour trước --</option>';
            return;
        }

        fetch('<?php echo BASE_URL; ?>/booking/get_schedules?tour_id=' + tourId)
            .then(res => res.json())
            .then(data => {
                scheduleSelect.innerHTML = '<option value="">-- Chọn ngày khởi hành --</option>';
                
                if (data.length > 0) {
                    data.forEach(item => {
                        const dateStr = new Date(item.NgayKhoiHanh).toLocaleDateString('vi-VN');
                        const option = document.createElement('option');
                        option.value = item.MaLichKhoiHanh;
                        option.text = `[${dateStr}] - ${item.LichCode} (Còn ${item.SoChoToiDa - item.SoKhachHienTai} chỗ)`;
                        
                        // [QUAN TRỌNG] Gắn giá tiền vào data attribute để JS đọc
                        option.setAttribute('data-price-adult', item.GiaNguoiLon || 0);
                        option.setAttribute('data-price-child', item.GiaTreEm || 0);
                        
                        scheduleSelect.add(option);
                    });
                    scheduleSelect.disabled = false;
                } else {
                    scheduleSelect.innerHTML = '<option value="">Không có lịch chạy</option>';
                }
            });
    }

    // 2. Cập nhật thông tin giá khi chọn Lịch
    function updatePriceInfo() {
        const select = document.getElementById('schedule_select');
        const priceInfo = document.getElementById('price_info');
        
        if (select.value) {
            const option = select.options[select.selectedIndex];
            const pAdult = parseFloat(option.getAttribute('data-price-adult'));
            const pChild = parseFloat(option.getAttribute('data-price-child'));

            // Hiển thị ra UI
            document.getElementById('lbl_price_adult').innerText = new Intl.NumberFormat('vi-VN').format(pAdult);
            document.getElementById('lbl_price_child').innerText = new Intl.NumberFormat('vi-VN').format(pChild);
            
            // Lưu vào input ẩn
            document.getElementById('raw_price_adult').value = pAdult;
            document.getElementById('raw_price_child').value = pChild;

            priceInfo.classList.remove('d-none');
            
            // Tính lại tổng tiền ngay
            calculateTotal();
        } else {
            priceInfo.classList.add('d-none');
            document.getElementById('raw_price_adult').value = 0;
            document.getElementById('raw_price_child').value = 0;
            calculateTotal();
        }
    }

    // 3. Tính toán tổng tiền (Logic chính)
    function calculateTotal() {
        // Lấy số lượng
        const qtyAdult = parseInt(document.getElementById('qty_adult').value) || 0;
        const qtyChild = parseInt(document.getElementById('qty_child').value) || 0;

        // Lấy giá vé
        const priceAdult = parseFloat(document.getElementById('raw_price_adult').value) || 0;
        const priceChild = parseFloat(document.getElementById('raw_price_child').value) || 0;

        // Tính toán
        const totalMoney = (qtyAdult * priceAdult) + (qtyChild * priceChild);
        const totalPax = qtyAdult + qtyChild;

        // Hiển thị
        document.getElementById('real_money').value = totalMoney;
        document.getElementById('display_money').value = new Intl.NumberFormat('vi-VN').format(totalMoney);
        document.getElementById('total_pax_lbl').innerText = `Tổng: ${totalPax} khách`;
    }
</script>