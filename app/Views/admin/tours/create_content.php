<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary fw-bold">Khởi tạo Tour Mới</h3>
    <a href="<?php echo BASE_URL; ?>/tour/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/tour/store" method="POST" enctype="multipart/form-data">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <ul class="nav nav-tabs card-header-tabs" id="tourTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                                <i class="bi bi-info-circle"></i> Thông tin chung
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                                <i class="bi bi-list-task"></i> Lịch trình chi tiết
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy" type="button">
                                <i class="bi bi-shield-check"></i> Chính sách & Giá
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="tourTabContent">
                        <div class="tab-pane fade show active" id="info">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                <input type="text" name="ten_tour" class="form-control form-control-lg" required placeholder="VD: Hành trình Di sản Miền Trung">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Loại Tour</label>
                                    <select name="loai_tour" class="form-select">
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['MaLoaiTour']; ?>"><?php echo $cat['TenLoai']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted">Số ngày</label>
                                    <input type="number" name="so_ngay" class="form-control" required value="1" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted">Số chỗ (Max)</label>
                                    <input type="number" name="so_cho" class="form-control" value="20" min="1">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Mô tả ngắn (Intro)</label>
                                <textarea name="mo_ta" class="form-control" rows="5" placeholder="Viết vài dòng giới thiệu hấp dẫn về tour này..."></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="schedule">
                            <div class="alert alert-info small mb-3">
                                <i class="bi bi-lightbulb"></i> Hãy nhập chi tiết từng ngày để khách hàng dễ hình dung lộ trình.
                            </div>
                            
                            <div id="schedule-container">
                                <div class="card mb-3 schedule-item border-start border-4 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="badge bg-primary">Ngày 1</span>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="schedule[0][title]" class="form-control fw-bold" required placeholder="Tiêu đề (VD: Đón bay - Check in khách sạn)">
                                        </div>
                                        <div>
                                            <textarea name="schedule[0][content]" class="form-control" rows="3" placeholder="Nội dung hoạt động trong ngày..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-light border border-dashed w-100 py-2 fw-bold text-primary" onclick="addScheduleDay()">
                                <i class="bi bi-plus-circle"></i> Thêm ngày tiếp theo
                            </button>
                        </div>

                        <div class="tab-pane fade" id="policy">
                            <div class="row g-3 mb-4 bg-light p-3 rounded">
                                <div class="col-12">
                                    <h6 class="fw-bold text-success"><i class="bi bi-tag"></i> Giá tham khảo (Hiển thị trên web)</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Giá Người Lớn</label>
                                    <div class="input-group">
                                        <input type="text" name="gia_nguoi_lon" class="form-control" placeholder="0" onkeyup="formatCurrency(this)">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Giá Trẻ Em</label>
                                    <div class="input-group">
                                        <input type="text" name="gia_tre_em" class="form-control" placeholder="0" onkeyup="formatCurrency(this)">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Điều khoản & Chính sách Tour</label>
                                <textarea name="chinh_sach" class="form-control" rows="8" placeholder="Quy định hủy tour, bao gồm, không bao gồm..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-image"></i> Hình ảnh đại diện</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="thumbPreview" src="https://via.placeholder.com/300x200?text=No+Image" class="img-fluid rounded border mb-2" style="max-height: 200px; object-fit: cover;">
                        <input type="file" name="hinhanh" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'thumbPreview')">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-images"></i> Thư viện ảnh (Gallery)</h6>
                </div>
                <div class="card-body">
                    <input type="file" name="gallery[]" class="form-control form-control-sm mb-2" multiple accept="image/*">
                    <div class="small text-muted">
                        <i class="bi bi-info-circle"></i> Giữ phím <b>Ctrl</b> để chọn nhiều ảnh cùng lúc.
                    </div>
                </div>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="bi bi-check-lg"></i> Hoàn tất & Lưu
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    let dayCount = 1;

    function addScheduleDay() {
        dayCount++;
        const container = document.getElementById('schedule-container');
        
        const html = `
            <div class="card mb-3 schedule-item border-start border-4 border-primary position-relative animation-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-primary">Ngày ${dayCount}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0" onclick="removeDay(this)" title="Xóa ngày này">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="schedule[${dayCount-1}][title]" class="form-control fw-bold" placeholder="Tiêu đề ngày ${dayCount}...">
                    </div>
                    <div>
                        <textarea name="schedule[${dayCount-1}][content]" class="form-control" rows="3" placeholder="Nội dung..."></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
    
    function removeDay(btn) {
        if(confirm('Xóa ngày lịch trình này?')) {
            btn.closest('.schedule-item').remove();
            // Optional: Renumber days logic here if needed
        }
    }

    function previewImage(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(imgId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = new Intl.NumberFormat('vi-VN').format(value);
    }
</script>

<style>
    .animation-fade-in { animation: fadeIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>