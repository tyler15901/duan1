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
    <form action="<?php echo BASE_URL; ?>/booking/store" method="POST" id="bookingForm" onsubmit="return validateForm()">
        <div class="row">
            <div class="col-lg-8">

                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="bi bi-geo-alt-fill me-2"></i> Thông tin Tour</h5>

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

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian khởi hành <span class="text-danger">*</span></label>

                                <div class="d-flex gap-3 mb-2 p-2 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="option_date" id="opt_exist" value="exist" checked onchange="toggleDateOption()">
                                        <label class="form-check-label fw-bold" for="opt_exist">Chọn lịch có sẵn</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="option_date" id="opt_custom" value="custom" onchange="toggleDateOption()">
                                        <label class="form-check-label fw-bold" for="opt_custom">Chọn ngày khác</label>
                                    </div>
                                </div>

                                <div id="box_exist_schedule">
                                    <select name="lich_id" id="schedule_select" class="form-select" onchange="updatePriceFromSchedule()">
                                        <option value="">-- Vui lòng chọn Tour trước --</option>
                                    </select>
                                    <div class="form-text text-muted" id="schedule_info">
                                        Giá vé sẽ lấy theo lịch đã chọn.
                                    </div>
                                </div>

                                <div id="box_custom_date" class="d-none bg-white p-3 rounded border border-warning">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Ngày khởi hành mong muốn:</label>
                                        <input type="date" name="custom_date" id="custom_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <label class="form-label small fw-bold text-primary">Thiết lập giá cho Lịch mới này (VNĐ):</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text fw-bold">Người lớn</span>
                                                <input type="text" name="price_adult_new" id="price_adult_new" 
                                                       class="form-control fw-bold text-end" placeholder="0" 
                                                       onkeyup="formatCurrencyInput(this); calculateTotal();">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text fw-bold">Trẻ em</span>
                                                <input type="text" name="price_child_new" id="price_child_new" 
                                                       class="form-control fw-bold text-end" placeholder="0" 
                                                       onkeyup="formatCurrencyInput(this); calculateTotal();">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text small fst-italic mt-2 text-warning">
                                        <i class="bi bi-info-circle"></i> Hệ thống sẽ tự động tạo lịch mới. Vui lòng nhập giá bán để tính tiền.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="bi bi-person-lines-fill me-2"></i> Người liên hệ</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="so_dien_thoai" class="form-control" required placeholder="09xxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title m-0 text-info"><i class="bi bi-people-fill me-2"></i> Danh sách đoàn khách</h5>
                            
                            <div>
                                <input type="file" id="csv_input" accept=".csv" class="d-none" onchange="importGuestCSV(this)">
                                <button type="button" class="btn btn-sm btn-success me-1" onclick="document.getElementById('csv_input').click()">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Import CSV
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addGuestRow()">
                                    <i class="bi bi-plus-lg"></i> Thêm dòng
                                </button>
                            </div>
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
                                        <th style="min-width: 150px;">Họ tên</th>
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
                                        <td><input type="text" name="guests[0][name]" class="form-control form-control-sm" placeholder="Họ tên..." required></td>
                                        <td>
                                            <select name="guests[0][type]" class="form-select form-select-sm">
                                                <option value="Người lớn">Người lớn</option>
                                                <option value="Trẻ em">Trẻ em</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="guests[0][phone]" class="form-control form-control-sm" placeholder="SĐT"></td>
                                        <td><input type="text" name="guests[0][id_card]" class="form-control form-control-sm" placeholder="Giấy tờ"></td>
                                        <td><input type="text" name="guests[0][note]" class="form-control form-control-sm" placeholder="VD: Ăn chay"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm text-danger" onclick="removeGuestRow(this)"><i class="bi bi-x-lg"></i></button>
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
                        <i class="bi bi-calculator me-2"></i> Thanh toán
                    </div>
                    <div class="card-body pt-3">

                        <input type="hidden" id="current_price_adult" value="0">
                        <input type="hidden" id="current_price_child" value="0">

                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tạm tính (Hệ thống)</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small" id="total_pax_lbl">0 khách</span>
                                <span class="fw-bold fs-5 text-secondary" id="display_calc_money">0 đ</span>
                            </div>
                            <div id="price_info_detail" class="small text-end fst-italic mt-1 text-muted"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Giá chốt đơn (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="tong_tien_chot" id="final_money"
                                    class="form-control fw-bold text-primary fs-5" value="0" required
                                    onkeyup="formatCurrencyInput(this)">
                                <span class="input-group-text bg-white">đ</span>
                            </div>
                            <div class="form-text small">Số tiền thực tế khách phải trả.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái thanh toán</label>
                            <select name="trang_thai_tt" id="payment_status" class="form-select" onchange="toggleDepositInput()">
                                <option value="Chưa thanh toán">⚪ Chưa thanh toán</option>
                                <option value="Đã cọc">🟡 Đã đặt cọc</option>
                                <option value="Đã thanh toán">🟢 Đã thanh toán (Full)</option>
                            </select>
                        </div>

                        <div class="mb-4" id="deposit_box" style="display: none;">
                            <label class="form-label fw-bold text-warning">Số tiền cọc (VNĐ)</label>
                            <div class="input-group">
                                <input type="text" name="tien_coc" id="deposit_amount"
                                    class="form-control fw-bold text-warning" placeholder="Nhập số tiền..."
                                    onkeyup="formatCurrencyInput(this)">
                                <span class="input-group-text bg-white">đ</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Xác nhận Đặt
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
    // 1. AJAX Load Lịch trình
    function loadSchedules() {
        const tourId = document.getElementById('tour_select').value;
        const scheduleSelect = document.getElementById('schedule_select');
        
        scheduleSelect.innerHTML = '<option value="">Đang tải...</option>';
        scheduleSelect.disabled = true;
        resetPrices();

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
                        const slotsLeft = (item.SoChoToiDa || 20) - (item.SoKhachHienTai || 0);
                        option.text = `[${dateStr}] - ${item.LichCode} (Còn ${slotsLeft} chỗ)`;

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

    // 2. Toggle giữa Case 1 (Lịch có sẵn) và Case 2 (Tự chọn ngày)
    function toggleDateOption() {
        const isCustom = document.getElementById('opt_custom').checked;
        const boxExist = document.getElementById('box_exist_schedule');
        const boxCustom = document.getElementById('box_custom_date');
        const selectLich = document.getElementById('schedule_select');
        const inputDate = document.getElementById('custom_date');
        const priceAdultNew = document.getElementById('price_adult_new');
        
        resetPrices();

        if (isCustom) {
            // Hiện Case 2, Ẩn Case 1
            boxExist.classList.add('d-none');
            boxCustom.classList.remove('d-none');
            
            selectLich.value = "";
            selectLich.required = false;
            
            inputDate.required = true;
            priceAdultNew.required = true; // Bắt buộc nhập giá khi tạo mới
        } else {
            // Hiện Case 1, Ẩn Case 2
            boxExist.classList.remove('d-none');
            boxCustom.classList.add('d-none');
            
            inputDate.value = "";
            inputDate.required = false;
            priceAdultNew.required = false;
            
            selectLich.required = true;
            
            if(selectLich.value) updatePriceFromSchedule();
        }
        calculateTotal();
    }

    // 3. Cập nhật giá từ Lịch có sẵn
    function updatePriceFromSchedule() {
        const select = document.getElementById('schedule_select');
        if (select.value) {
            const option = select.options[select.selectedIndex];
            const pAdult = parseFloat(option.getAttribute('data-price-adult')) || 0;
            const pChild = parseFloat(option.getAttribute('data-price-child')) || 0;

            document.getElementById('current_price_adult').value = pAdult;
            document.getElementById('current_price_child').value = pChild;
            
            calculateTotal();
        } else {
            resetPrices();
        }
    }

    // 4. Tính tổng tiền
    function calculateTotal() {
        let priceAdult = 0;
        let priceChild = 0;
        const isCustom = document.getElementById('opt_custom').checked;

        if (isCustom) {
            // Lấy giá từ ô nhập tay (Case 2)
            const rawAdult = document.getElementById('price_adult_new').value.replace(/\D/g, '');
            const rawChild = document.getElementById('price_child_new').value.replace(/\D/g, '');
            priceAdult = parseFloat(rawAdult) || 0;
            priceChild = parseFloat(rawChild) || 0;
        } else {
            // Lấy giá từ lịch có sẵn (Case 1)
            priceAdult = parseFloat(document.getElementById('current_price_adult').value) || 0;
            priceChild = parseFloat(document.getElementById('current_price_child').value) || 0;
        }

        const qtyAdult = parseInt(document.getElementById('qty_adult').value) || 0;
        const qtyChild = parseInt(document.getElementById('qty_child').value) || 0;

        const totalMoney = (qtyAdult * priceAdult) + (qtyChild * priceChild);
        const totalPax = qtyAdult + qtyChild;

        document.getElementById('display_calc_money').innerText = new Intl.NumberFormat('vi-VN').format(totalMoney) + ' đ';
        document.getElementById('total_pax_lbl').innerText = `${totalPax} khách`;
        document.getElementById('final_money').value = new Intl.NumberFormat('vi-VN').format(totalMoney);
        
        const detailInfo = document.getElementById('price_info_detail');
        if(priceAdult > 0 || priceChild > 0) {
            detailInfo.innerHTML = `Đơn giá: NL ${new Intl.NumberFormat('vi-VN').format(priceAdult)}đ | TE ${new Intl.NumberFormat('vi-VN').format(priceChild)}đ`;
        } else {
            detailInfo.innerHTML = 'Chưa có đơn giá';
        }
    }

    function resetPrices() {
        document.getElementById('current_price_adult').value = 0;
        document.getElementById('current_price_child').value = 0;
        calculateTotal();
    }

    // 5. Format tiền tệ
    function formatCurrencyInput(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value ? new Intl.NumberFormat('vi-VN').format(value) : '';
    }

    // 6. Ẩn hiện ô Tiền cọc
    function toggleDepositInput() {
        const status = document.getElementById('payment_status').value;
        const depositBox = document.getElementById('deposit_box');
        const depositInput = document.getElementById('deposit_amount');

        if (status === 'Đã cọc') {
            depositBox.style.display = 'block';
            depositInput.required = true;
        } else {
            depositBox.style.display = 'none';
            depositInput.required = false;
            depositInput.value = '';
        }
    }

    // 7. Đồng bộ dòng khách
    function syncGuestRows() {
        const totalPax = (parseInt(document.getElementById('qty_adult').value) || 0) + (parseInt(document.getElementById('qty_child').value) || 0);
        const currentRows = document.querySelectorAll('.guest-row').length;

        if (totalPax > currentRows) {
            for (let i = 0; i < (totalPax - currentRows); i++) {
                addGuestRow();
            }
        }
    }

    // 8. IMPORT CSV (CHỨC NĂNG MỚI)
    function importGuestCSV(input) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const rows = text.split(/\r\n|\n/);
            const tbody = document.getElementById('guest_list_body');
            
            // Reset bảng
            tbody.innerHTML = ''; 
            
            let countAdult = 0;
            let countChild = 0;
            let index = 0;

            rows.forEach((row, i) => {
                if (!row.trim()) return;
                const cols = row.split(','); 
                
                // Bỏ qua dòng tiêu đề
                if (i === 0 && (cols[0].toLowerCase().includes('họ tên') || cols[0].toLowerCase().includes('name'))) return;

                const name = cols[0] ? cols[0].trim() : '';
                if (!name) return;

                const type = cols[1] ? cols[1].trim() : 'Người lớn';
                const phone = cols[2] ? cols[2].trim() : '';
                const idCard = cols[3] ? cols[3].trim() : '';
                const note = cols[4] ? cols[4].trim() : '';

                if (type.toLowerCase().includes('trẻ') || type.toLowerCase() === 'child') {
                    countChild++;
                } else {
                    countAdult++;
                }

                const tr = `
                    <tr class="guest-row">
                        <td class="text-center fw-bold row-index">${index + 1}</td>
                        <td><input type="text" name="guests[${index}][name]" class="form-control form-control-sm" value="${name}" required></td>
                        <td>
                            <select name="guests[${index}][type]" class="form-select form-select-sm">
                                <option value="Người lớn" ${!type.toLowerCase().includes('trẻ') ? 'selected' : ''}>Người lớn</option>
                                <option value="Trẻ em" ${type.toLowerCase().includes('trẻ') ? 'selected' : ''}>Trẻ em</option>
                            </select>
                        </td>
                        <td><input type="text" name="guests[${index}][phone]" class="form-control form-control-sm" value="${phone}"></td>
                        <td><input type="text" name="guests[${index}][id_card]" class="form-control form-control-sm" value="${idCard}"></td>
                        <td><input type="text" name="guests[${index}][note]" class="form-control form-control-sm" value="${note}"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm text-danger" onclick="removeGuestRow(this)"><i class="bi bi-x-lg"></i></button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', tr);
                index++;
            });

            document.getElementById('qty_adult').value = countAdult;
            document.getElementById('qty_child').value = countChild;
            calculateTotal();
            input.value = '';
        };
        reader.readAsText(file);
    }

    function addGuestRow() {
        const tbody = document.getElementById('guest_list_body');
        const index = document.querySelectorAll('.guest-row').length;

        const row = `
            <tr class="guest-row">
                <td class="text-center fw-bold row-index">${index + 1}</td>
                <td><input type="text" name="guests[${index}][name]" class="form-control form-control-sm" placeholder="Họ tên..." required></td>
                <td>
                    <select name="guests[${index}][type]" class="form-select form-select-sm">
                        <option value="Người lớn">Người lớn</option>
                        <option value="Trẻ em">Trẻ em</option>
                    </select>
                </td>
                <td><input type="text" name="guests[${index}][phone]" class="form-control form-control-sm" placeholder="SĐT"></td>
                <td><input type="text" name="guests[0][id_card]" class="form-control form-control-sm" placeholder="Giấy tờ"></td>
                <td><input type="text" name="guests[${index}][note]" class="form-control form-control-sm" placeholder="VD: Ăn chay"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm text-danger" onclick="removeGuestRow(this)"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    }

    function removeGuestRow(btn) {
        const row = btn.closest('tr');
        if (document.querySelectorAll('.guest-row').length > 1) {
            row.remove();
            document.querySelectorAll('.guest-row').forEach((tr, idx) => {
                tr.querySelector('.row-index').innerText = idx + 1;
            });
        }
    }
    
    function validateForm() {
        if(!document.getElementById('tour_select').value) {
            alert('Vui lòng chọn Tour!');
            return false;
        }
        return true;
    }
</script>