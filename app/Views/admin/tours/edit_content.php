<div class="pagetitle">
    <h1>Cập nhật Tour</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/tour/index">Tour</a></li>
            <li class="breadcrumb-item active"><?php echo $tour['TenTour']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/tour/update/<?php echo $tour['MaTour']; ?>" method="POST" enctype="multipart/form-data">
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
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy-tab" type="button">Chính sách</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-4">
                            <div class="tab-pane fade show active" id="info-tab">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_tour" class="form-control" value="<?php echo htmlspecialchars($tour['TenTour']); ?>" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Loại Tour</label>
                                        <select name="loai_tour" class="form-select">
                                            <?php foreach($categories as $cat): ?>
                                                <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($cat['MaLoaiTour'] == $tour['MaLoaiTour']) ? 'selected' : ''; ?>>
                                                    <?php echo $cat['TenLoai']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Số ngày</label>
                                        <input type="number" name="so_ngay" class="form-control" value="<?php echo $tour['SoNgay']; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Trạng thái</label>
                                        <select name="trang_thai" class="form-select">
                                            <option value="Hoạt động" <?php echo ($tour['TrangThai'] == 'Hoạt động') ? 'selected' : ''; ?>>Hoạt động</option>
                                            <option value="Tạm dừng" <?php echo ($tour['TrangThai'] == 'Tạm dừng') ? 'selected' : ''; ?>>Tạm dừng</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label fw-bold">Mô tả ngắn</label>
                                    <textarea name="mo_ta" class="form-control" rows="5"><?php echo $tour['MoTa']; ?></textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="schedule-tab">
                                <div id="schedule-container">
                                    <?php if(!empty($schedule)): ?>
                                        <?php foreach($schedule as $k => $day): ?>
                                        <div class="card mb-3 border schedule-item bg-light">
                                            <div class="card-body position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeDay(this)" title="Xóa ngày này"></button>
                                                <h6 class="card-title text-primary">Ngày <?php echo $k + 1; ?></h6>
                                                <div class="mb-2">
                                                    <input type="text" name="schedule[<?php echo $k; ?>][title]" class="form-control fw-bold" value="<?php echo $day['TieuDe']; ?>" placeholder="Tiêu đề (VD: Hà Nội - Sapa)">
                                                </div>
                                                <textarea name="schedule[<?php echo $k; ?>][content]" class="form-control" rows="3" placeholder="Nội dung chi tiết..."><?php echo $day['NoiDung']; ?></textarea>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted text-center mb-3">Chưa có lịch trình chi tiết.</div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-outline-primary w-100 dashed-btn" onclick="addScheduleDay()">
                                    <i class="bi bi-plus-circle"></i> Thêm ngày tiếp theo
                                </button>
                            </div>

                            <div class="tab-pane fade" id="policy-tab">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Điều khoản & Chính sách Tour</label>
                                    <textarea name="chinh_sach" class="form-control" rows="10" placeholder="Nhập quy định hủy tour, bao gồm, không bao gồm..."><?php echo $tour['ChinhSach']; ?></textarea>
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
                            <?php $imgSrc = $tour['HinhAnh'] ? BASE_URL.'/assets/uploads/'.$tour['HinhAnh'] : 'https://via.placeholder.com/400x250'; ?>
                            <img id="thumbPreview" src="<?php echo $imgSrc; ?>" class="img-fluid rounded border" style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <input type="file" name="hinhanh" class="form-control" accept="image/*" onchange="previewImage(this, 'thumbPreview')">
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thư viện ảnh</h5>
                        <input type="file" name="gallery[]" class="form-control mb-2" multiple accept="image/*">
                        <small class="text-muted d-block">Lưu ý: Chọn ảnh mới sẽ thêm vào thư viện hiện có.</small>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Cập nhật Tour</button>
                    <a href="<?php echo BASE_URL; ?>/tour/index" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    // Khởi tạo số lượng ngày dựa trên dữ liệu PHP
    let currentIndex = <?php echo count($schedule); ?>;

    function addScheduleDay() {
        let dayLabel = currentIndex + 1; // Nhãn hiển thị (Ngày 1, Ngày 2...)
        
        const html = `
            <div class="card mb-3 border schedule-item bg-light animation-fade">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeDay(this)" title="Xóa ngày này"></button>
                    <h6 class="card-title text-primary">Ngày ${dayLabel}</h6>
                    <div class="mb-2">
                        <input type="text" name="schedule[${currentIndex}][title]" class="form-control fw-bold" placeholder="Tiêu đề ngày ${dayLabel}...">
                    </div>
                    <textarea name="schedule[${currentIndex}][content]" class="form-control" rows="3" placeholder="Nội dung hoạt động..."></textarea>
                </div>
            </div>`;
        
        document.getElementById('schedule-container').insertAdjacentHTML('beforeend', html);
        currentIndex++; // Tăng chỉ số cho lần thêm tiếp theo
    }
    
    function removeDay(btn) { 
        if(confirm('Bạn có chắc muốn xóa ngày lịch trình này?')) {
            btn.closest('.schedule-item').remove(); 
        }
    }
    
    function previewImage(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById(imgId).src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>