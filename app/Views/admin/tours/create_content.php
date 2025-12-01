<div class="pagetitle">
    <h1>Thêm Tour Mới</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/tour/index">Tour</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/tour/store" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-tab" type="button">Thông tin chung</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule-tab" type="button">Lịch trình</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy-tab" type="button">Chính sách & Giá</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-4">
                            <div class="tab-pane fade show active" id="info-tab">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_tour" class="form-control" required placeholder="VD: Khám phá Đà Nẵng - Hội An">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Loại Tour</label>
                                        <select name="loai_tour" class="form-select">
                                            <?php foreach($categories as $cat): ?>
                                                <option value="<?php echo $cat['MaLoaiTour']; ?>"><?php echo $cat['TenLoai']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Số ngày</label>
                                        <input type="number" name="so_ngay" class="form-control" required value="1" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Số chỗ (Max)</label>
                                        <input type="number" name="so_cho" class="form-control" value="20" min="1">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Mô tả ngắn</label>
                                    <textarea name="mo_ta" class="form-control" rows="5" placeholder="Giới thiệu điểm nổi bật của tour..."></textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="schedule-tab">
                                <div id="schedule-container">
                                    <div class="card mb-3 border schedule-item bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">Ngày 1</h6>
                                            <div class="mb-2">
                                                <input type="text" name="schedule[0][title]" class="form-control fw-bold" required placeholder="Tiêu đề ngày 1">
                                            </div>
                                            <textarea name="schedule[0][content]" class="form-control" rows="3" placeholder="Nội dung hoạt động..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary w-100 dashed-btn" onclick="addScheduleDay()">
                                    <i class="bi bi-plus-circle"></i> Thêm ngày tiếp theo
                                </button>
                            </div>

                            <div class="tab-pane fade" id="policy-tab">
                                <div class="alert alert-light border-light mb-3">
                                    <strong><i class="bi bi-tag-fill text-primary"></i> Giá tham khảo (VNĐ)</strong>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Người lớn</label>
                                        <input type="text" name="gia_nguoi_lon" class="form-control" onkeyup="formatCurrency(this)">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Trẻ em</label>
                                        <input type="text" name="gia_tre_em" class="form-control" onkeyup="formatCurrency(this)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Điều khoản & Chính sách</label>
                                    <textarea name="chinh_sach" class="form-control" rows="8"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ảnh đại diện</h5>
                        <div class="text-center mb-3">
                            <img id="thumbPreview" src="https://via.placeholder.com/400x250?text=Upload+Image" class="img-fluid rounded border" style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <input type="file" name="hinhanh" class="form-control" accept="image/*" onchange="previewImage(this, 'thumbPreview')">
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thư viện ảnh</h5>
                        <input type="file" name="gallery[]" class="form-control mb-2" multiple accept="image/*">
                        <small class="text-muted d-block">Giữ Ctrl để chọn nhiều ảnh.</small>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Lưu Tour</button>
                    <a href="<?php echo BASE_URL; ?>/tour/index" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    let dayCount = 1;
    function addScheduleDay() {
        dayCount++;
        const html = `
            <div class="card mb-3 border schedule-item bg-light animation-fade">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeDay(this)"></button>
                    <h6 class="card-title text-primary">Ngày ${dayCount}</h6>
                    <div class="mb-2">
                        <input type="text" name="schedule[${dayCount-1}][title]" class="form-control fw-bold" placeholder="Tiêu đề ngày ${dayCount}">
                    </div>
                    <textarea name="schedule[${dayCount-1}][content]" class="form-control" rows="3"></textarea>
                </div>
            </div>`;
        document.getElementById('schedule-container').insertAdjacentHTML('beforeend', html);
    }
    
    function removeDay(btn) { btn.closest('.schedule-item').remove(); }
    
    function previewImage(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById(imgId).src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = new Intl.NumberFormat('vi-VN').format(value);
    }
</script>