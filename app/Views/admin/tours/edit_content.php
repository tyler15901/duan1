<div class="pagetitle">
    <h1>Cập nhật Tour</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/tour/index">Tour</a></li>
            <li class="breadcrumb-item active">#<?php echo $tour['MaTour']; ?></li>
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
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info">Thông tin</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule">Lịch trình</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy">Chính sách</button></li>
                        </ul>

                        <div class="tab-content pt-4">
                            <div class="tab-pane fade show active" id="info">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Tên Tour</label>
                                    <input type="text" name="ten_tour" class="form-control" value="<?php echo $tour['TenTour']; ?>" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Loại Tour</label>
                                        <select name="loai_tour" class="form-select">
                                            <?php foreach($categories as $cat): ?>
                                                <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($cat['MaLoaiTour'] == $tour['MaLoaiTour'])?'selected':''; ?>>
                                                    <?php echo $cat['TenLoai']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="trang_thai" class="form-select">
                                            <option value="Hoạt động" <?php echo ($tour['TrangThai']=='Hoạt động')?'selected':''; ?>>Hoạt động</option>
                                            <option value="Tạm dừng" <?php echo ($tour['TrangThai']=='Tạm dừng')?'selected':''; ?>>Tạm dừng</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Số ngày</label>
                                        <input type="number" name="so_ngay" class="form-control" value="<?php echo $tour['SoNgay']; ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Số chỗ</label>
                                        <input type="number" name="so_cho" class="form-control" value="<?php echo $tour['SoChoToiDa']; ?>">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="mo_ta" class="form-control" rows="5"><?php echo $tour['MoTa']; ?></textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="schedule">
                                <div id="schedule-container">
                                    <?php foreach($schedule as $k => $day): ?>
                                    <div class="card mb-3 border schedule-item bg-light">
                                        <div class="card-body position-relative">
                                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeDay(this)"></button>
                                            <h6 class="card-title text-primary">Ngày <?php echo $k + 1; ?></h6>
                                            <div class="mb-2">
                                                <input type="text" name="schedule[<?php echo $k; ?>][title]" class="form-control fw-bold" value="<?php echo $day['TieuDe']; ?>">
                                            </div>
                                            <textarea name="schedule[<?php echo $k; ?>][content]" class="form-control" rows="3"><?php echo $day['NoiDung']; ?></textarea>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-outline-primary w-100 dashed-btn" onclick="addScheduleDay()">
                                    <i class="bi bi-plus-circle"></i> Thêm ngày tiếp theo
                                </button>
                            </div>

                            <div class="tab-pane fade" id="policy">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Chính sách Tour</label>
                                    <textarea name="chinh_sach" class="form-control" rows="10"><?php echo $tour['ChinhSach']; ?></textarea>
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
                        <?php $imgSrc = $tour['HinhAnh'] ? BASE_URL.'/assets/uploads/'.$tour['HinhAnh'] : 'https://via.placeholder.com/400x250'; ?>
                        <div class="text-center mb-3">
                            <img id="thumbPreview" src="<?php echo $imgSrc; ?>" class="img-fluid rounded border" style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <input type="file" name="hinhanh" class="form-control" onchange="previewImage(this, 'thumbPreview')">
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thư viện ảnh</h5>
                        <input type="file" name="gallery[]" class="form-control" multiple>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Lưu Cập Nhật</button>
                    <a href="<?php echo BASE_URL; ?>/tour/index" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    let currentIndex = <?php echo count($schedule); ?>;
    let dayLabel = <?php echo count($schedule); ?>;

    function addScheduleDay() {
        currentIndex++; dayLabel++;
        const html = `
            <div class="card mb-3 border schedule-item bg-light animation-fade">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeDay(this)"></button>
                    <h6 class="card-title text-primary">Ngày ${dayLabel}</h6>
                    <div class="mb-2">
                        <input type="text" name="schedule[${currentIndex}][title]" class="form-control fw-bold" placeholder="Tiêu đề...">
                    </div>
                    <textarea name="schedule[${currentIndex}][content]" class="form-control" rows="3"></textarea>
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
</script>