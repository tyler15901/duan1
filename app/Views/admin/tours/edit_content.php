<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Cập nhật Tour</h4>
        <h2 class="text-primary fw-bold"><?php echo $tour['TenTour']; ?></h2>
    </div>
    <a href="<?php echo BASE_URL; ?>/tour/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/tour/update/<?php echo $tour['MaTour']; ?>" method="POST" enctype="multipart/form-data">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                 <div class="card-header bg-white border-bottom-0 pt-3">
                    <ul class="nav nav-tabs card-header-tabs" id="editTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#info" type="button">Thông tin chung</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#schedule" type="button">Lịch trình</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#policy" type="button">Chính sách</button></li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="info">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên Tour</label>
                                <input type="text" name="ten_tour" class="form-control fw-bold" value="<?php echo $tour['TenTour']; ?>" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Loại Tour</label>
                                    <select name="loai_tour" class="form-select">
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($cat['MaLoaiTour'] == $tour['MaLoaiTour'])?'selected':''; ?>>
                                                <?php echo $cat['TenLoai']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Trạng thái</label>
                                    <select name="trang_thai" class="form-select <?php echo ($tour['TrangThai']=='Hoạt động')?'text-success fw-bold':'text-muted'; ?>">
                                        <option value="Hoạt động" <?php echo ($tour['TrangThai']=='Hoạt động')?'selected':''; ?>>Hoạt động</option>
                                        <option value="Tạm dừng" <?php echo ($tour['TrangThai']=='Tạm dừng')?'selected':''; ?>>Tạm dừng</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Số ngày</label>
                                    <input type="number" name="so_ngay" class="form-control" value="<?php echo $tour['SoNgay']; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Số chỗ</label>
                                    <input type="number" name="so_cho" class="form-control" value="<?php echo $tour['SoChoToiDa']; ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Mô tả</label>
                                <textarea name="mo_ta" class="form-control" rows="5"><?php echo $tour['MoTa']; ?></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="schedule">
                            <div id="schedule-container">
                                <?php foreach($schedule as $k => $day): ?>
                                <div class="card mb-3 schedule-item border-start border-4 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="badge bg-primary">Ngày <?php echo $k + 1; ?></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0" onclick="removeDay(this)">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="schedule[<?php echo $k; ?>][title]" class="form-control fw-bold" value="<?php echo $day['TieuDe']; ?>">
                                        </div>
                                        <div>
                                            <textarea name="schedule[<?php echo $k; ?>][content]" class="form-control" rows="3"><?php echo $day['NoiDung']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-light border border-dashed w-100 py-2 fw-bold text-primary" onclick="addScheduleDay()">
                                <i class="bi bi-plus-circle"></i> Thêm ngày tiếp theo
                            </button>
                        </div>

                        <div class="tab-pane fade" id="policy">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chính sách Tour</label>
                                <textarea name="chinh_sach" class="form-control" rows="12"><?php echo $tour['ChinhSach']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Ảnh đại diện</h6>
                </div>
                <div class="card-body text-center">
                    <?php $imgSrc = $tour['HinhAnh'] ? BASE_URL.'/assets/uploads/'.$tour['HinhAnh'] : 'https://via.placeholder.com/300x200'; ?>
                    <img id="thumbPreview" src="<?php echo $imgSrc; ?>" class="img-fluid rounded border mb-3" style="max-height: 200px; object-fit: cover;">
                    
                    <input type="file" name="hinhanh" class="form-control form-control-sm" onchange="previewImage(this, 'thumbPreview')">
                    <div class="form-text small">Chọn file mới để thay thế ảnh hiện tại.</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Thư viện ảnh</h6>
                </div>
                <div class="card-body">
                    <input type="file" name="gallery[]" class="form-control form-control-sm mb-3" multiple>
                    <div class="row g-2">
                         <div class="col-12 text-muted small fst-italic">
                             Tính năng xóa ảnh thư viện cũ đang cập nhật...
                         </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="bi bi-check-circle"></i> Lưu Cập Nhật
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    // Logic đếm số ngày hiện tại để tạo index tiếp theo
    let currentIndex = <?php echo count($schedule); ?>;
    let dayLabel = <?php echo count($schedule); ?>;

    function addScheduleDay() {
        const container = document.getElementById('schedule-container');
        currentIndex++;
        dayLabel++;
        
        const html = `
            <div class="card mb-3 schedule-item border-start border-4 border-primary position-relative animation-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-primary">Ngày ${dayLabel}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0" onclick="removeDay(this)">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="schedule[${currentIndex}][title]" class="form-control fw-bold" placeholder="Tiêu đề...">
                    </div>
                    <div>
                        <textarea name="schedule[${currentIndex}][content]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeDay(btn) {
        if(confirm('Xóa ngày này?')) {
            btn.closest('.schedule-item').remove();
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
</script>

<style>
    .animation-fade-in { animation: fadeIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>