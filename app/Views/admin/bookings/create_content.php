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
                        <h5 class="card-title text-primary"><i class="bi bi-geo-alt-fill me-2"></i> Thông tin Tour &
                            Lịch trình</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="tour_select" class="form-select" required
                                    onchange="loadSchedules()">
                                    <option value="">-- Chọn Tour --</option>
                                    <?php foreach ($tours as $t): ?>
                                        <option value="<?php echo $t['MaTour']; ?>"><?php echo $t['TenTour']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Chọn Lịch Khởi Hành <span
                                        class="text-danger">*</span></label>
                                <select name="lich_id" id="schedule_select" class="form-select" required disabled
                                    onchange="updatePriceInfo()">
                                    <option value="">-- Vui lòng chọn Tour trước --</option>
                                </select>

                                <input type="hidden" id="raw_price_adult" value="0">
                                <input type="hidden" id="raw_price_child" value="0">

                                <div id="price_info" class="mt-2 small text-muted fst-italic"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="bi bi-person-lines-fill me-2"></i> Người đại diện
                            đặt tour</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required
                                    placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SĐT <span class="text-danger">*</span></label>
                                <input type="text" name="so_dien_thoai" class="form-control" required
                                    placeholder="09xxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title m-0 text-info"><i class="bi bi-people-fill me-2"></i> Danh sách đoàn
                                khách</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addGuestRow()">
                                <i class="bi bi-plus-lg"></i> Thêm dòng
                            </button>
                        </div>

                        <div class="row g-3 bg-light p-3 rounded border mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">Người lớn (>11t)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-standing"></i></span>
                                    <input type="number" name="sl_nguoi_lon" id="qty_adult" class="form-control fw-bold"
                                        value="1" min="1" onchange="calculateTotal(); syncGuestRows();">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-info">Trẻ em (5-11t)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-emoji-smile"></i></span>
                                    <input type="number" name="sl_tre_em" id="qty_child" class="form-control fw-bold"
                                        value="0" min="0" onchange="calculateTotal(); syncGuestRows();">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" id="guestTable">
                                <thead class="table-light small text-muted text-uppercase">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th style="min-width: 180px;">Họ và tên</th>
                                        <th style="width: 110px;">Loại</th>
                                        <th style="width: 120px;">SĐT</th>
                                        <th style="width: 120px;">CCCD/PP</th>
                                        <th>Ghi chú</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="guest_list_body">
                                    <tr class="guest-row">
                                        <td class="text-center fw-bold row-index">1</td>
                                        <td><input type="text" name="guests[0][name]"
                                                class="form-control form-control-sm" placeholder="Họ tên..." required>
                                        </td>
                                        <td>
                                            <select name="guests[0][type]" class="form-select form-select-sm">
                                                <option value="Người lớn">Người lớn</option>
                                                <option value="Trẻ em">Trẻ em</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="guests[0][phone]"
                                                class="form-control form-control-sm" placeholder="Số ĐT"></td>
                                        <td><input type="text" name="guests[0][id_card]"
                                                class="form-control form-control-sm" placeholder="Số giấy tờ"></td>
                                        <td><input type="text" name="guests[0][note]"
                                                class="form-control form-control-sm" placeholder="VD: Ăn chay"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm text-danger"
                                                onclick="removeGuestRow(this)"><i class="bi bi-x-lg"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Giá hệ thống tính (Tham
                                khảo)</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" id="total_pax_lbl_pay">0 khách</span>
                                <span class="fw-bold fs-5 text-secondary" id="display_calc_money">0 đ</span>
                            </div>
                            <input type="hidden" id="calc_money_raw" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Giá chốt với khách (VNĐ) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="tong_tien_chot" id="final_money"
                                    class="form-control fw-bold text-primary fs-5" value="0" required
                                    onkeyup="formatCurrencyInput(this)">
                                <span class="input-group-text bg-white">đ</span>
                            </div>
                            <div class="form-text small">Đây là số tiền thực tế sẽ lưu vào đơn hàng.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái thanh toán</label>
                            <select name="trang_thai_tt" id="payment_status" class="form-select"
                                onchange="toggleDepositInput()">
                                <option value="Chưa thanh toán">⚪ Chưa thanh toán</option>
                                <option value="Đã cọc">🟡 Đã đặt cọc</option>
                                <option value="Đã thanh toán">🟢 Đã thanh toán (Full)</option>
                            </select>
                        </div>

                        <div class="mb-4" id="deposit_box" style="display: none;">
                            <label class="form-label fw-bold text-warning">Số tiền khách cọc (VNĐ)</label>
                            <div class="input-group">
                                <input type="text" name="tien_coc" id="deposit_amount"
                                    class="form-control fw-bold text-warning" placeholder="Nhập số tiền..."
                                    onkeyup="formatCurrencyInput(this)">
                                <span class="input-group-text bg-white">đ</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Xác nhận Đặt Tour
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
        priceInfo.innerHTML = ''; // Xóa thông tin giá cũ

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

                        // Gắn giá tiền vào thẻ option để dùng sau
                        option.setAttribute('data-price-adult', item.GiaNguoiLon || 0);
                        option.setAttribute('data-price-child', item.GiaTreEm || 0);

                        scheduleSelect.add(option);
                    });
                    scheduleSelect.disabled = false;
                } else {
                    scheduleSelect.innerHTML = '<option value="">Không có lịch chạy</option>';
                }
            })
            .catch(err => {
                console.error(err);
                scheduleSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
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

            // Lưu vào input ẩn
            document.getElementById('raw_price_adult').value = pAdult;
            document.getElementById('raw_price_child').value = pChild;

            // Hiển thị text giá vé
            priceInfo.innerHTML = `<i class="bi bi-tag-fill text-warning"></i> Giá vé: 
                <span class="text-success fw-bold">${new Intl.NumberFormat('vi-VN').format(pAdult)}đ</span> (NL) - 
                <span class="text-info fw-bold">${new Intl.NumberFormat('vi-VN').format(pChild)}đ</span> (TE)`;

            // Tính lại tổng tiền ngay
            calculateTotal();
        } else {
            priceInfo.innerHTML = '';
            document.getElementById('raw_price_adult').value = 0;
            document.getElementById('raw_price_child').value = 0;
            calculateTotal();
        }
    }

    // 3. Tính toán tổng tiền
    function calculateTotal() {
        const qtyAdult = parseInt(document.getElementById('qty_adult').value) || 0;
        const qtyChild = parseInt(document.getElementById('qty_child').value) || 0;
        const priceAdult = parseFloat(document.getElementById('raw_price_adult').value) || 0;
        const priceChild = parseFloat(document.getElementById('raw_price_child').value) || 0;

        const total = (qtyAdult * priceAdult) + (qtyChild * priceChild);
        const totalPax = qtyAdult + qtyChild;

        // 1. Hiển thị giá tạm tính (Tham khảo)
        document.getElementById('calc_money_raw').value = total;
        document.getElementById('display_calc_money').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        document.getElementById('total_pax_lbl_pay').innerText = `${totalPax} khách`;

        // 2. Tự động điền vào ô "Giá chốt" (Admin có thể sửa lại sau)
        // Chỉ tự điền nếu Admin chưa sửa gì (hoặc đang để trống/0)
        // Hoặc bạn có thể cho nó luôn tự update, tùy nhu cầu. Ở đây tôi cho luôn update để tiện.
        document.getElementById('final_money').value = new Intl.NumberFormat('vi-VN').format(total);

        // Update gợi ý giá vé
        document.getElementById('price_info').innerText =
            `Giá vé: NL ${new Intl.NumberFormat('vi-VN').format(priceAdult)}đ | TE ${new Intl.NumberFormat('vi-VN').format(priceChild)}đ`;
    }

    // [MỚI] Hàm format tiền khi gõ tay (thêm dấu chấm)
    function formatCurrencyInput(input) {
        // Xóa hết ký tự không phải số
        let value = input.value.replace(/\D/g, '');
        // Format lại có dấu chấm
        input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
    }

    // [MỚI] Ẩn/Hiện ô nhập tiền cọc
    function toggleDepositInput() {
        const status = document.getElementById('payment_status').value;
        const depositBox = document.getElementById('deposit_box');

        if (status === 'Đã cọc') {
            depositBox.style.display = 'block';
            document.getElementById('deposit_amount').required = true;
        } else {
            depositBox.style.display = 'none';
            document.getElementById('deposit_amount').required = false;
            document.getElementById('deposit_amount').value = ''; // Reset về rỗng
        }
    }

    // 4. Đồng bộ số dòng (Tự động thêm dòng khi nhập số lượng)
    function syncGuestRows() {
        const totalPax = (parseInt(document.getElementById('qty_adult').value) || 0) + (parseInt(document.getElementById('qty_child').value) || 0);
        const currentRows = document.querySelectorAll('.guest-row').length;

        if (totalPax > currentRows) {
            for (let i = 0; i < (totalPax - currentRows); i++) {
                addGuestRow();
            }
        }
    }

    // --- [SỬA LỖI] HÀM THÊM DÒNG MỚI ĐẦY ĐỦ CỘT ---
    function addGuestRow() {
        const tbody = document.getElementById('guest_list_body');
        const index = document.querySelectorAll('.guest-row').length;

        // HTML dòng mới: Phải có đủ 7 cột (td) khớp với <th> ở trên
        const row = `
            <tr class="guest-row">
                <td class="text-center fw-bold row-index">${index + 1}</td>
                
                <td>
                    <input type="text" name="guests[${index}][name]" class="form-control form-control-sm" placeholder="Họ tên..." required>
                </td>
                
                <td>
                    <select name="guests[${index}][type]" class="form-select form-select-sm">
                        <option value="Người lớn">Người lớn</option>
                        <option value="Trẻ em">Trẻ em</option>
                    </select>
                </td>
                
                <td>
                    <input type="text" name="guests[${index}][phone]" class="form-control form-control-sm" placeholder="SĐT">
                </td>
                
                <td>
                    <input type="text" name="guests[${index}][id_card]" class="form-control form-control-sm" placeholder="Số giấy tờ">
                </td>
                
                <td>
                    <input type="text" name="guests[${index}][note]" class="form-control form-control-sm" placeholder="VD: Ăn chay">
                </td>
                
                <td class="text-center">
                    <button type="button" class="btn btn-sm text-danger" onclick="removeGuestRow(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', row);
    }

    // Hàm xóa dòng
    function removeGuestRow(btn) {
        const row = btn.closest('tr');
        // Không cho xóa dòng cuối cùng nếu chỉ còn 1 dòng
        if (document.querySelectorAll('.guest-row').length > 1) {
            row.remove();
            // Cập nhật lại số thứ tự (STT)
            document.querySelectorAll('.guest-row').forEach((tr, idx) => {
                tr.querySelector('.row-index').innerText = idx + 1;
            });
        }
    }
</script>