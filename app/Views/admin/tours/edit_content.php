<h2 class="mb-4">Chỉnh Sửa Tour: <?php echo $tour['TenTour']; ?></h2>

<form action="<?php echo BASE_URL; ?>/tour/update/<?php echo $tour['MaTour']; ?>" method="POST" enctype="multipart/form-data">
    
    <ul class="nav nav-tabs mb-4" id="editTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info" type="button">Thông tin</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule" type="button">Lịch trình</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy" type="button">Chính sách</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="info">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label>Tên Tour</label>
                        <input type="text" name="ten_tour" class="form-control" value="<?php echo $tour['TenTour']; ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Loại Tour</label>
                            <select name="loai_tour" class="form-select">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($cat['MaLoaiTour'] == $tour['MaLoaiTour'])?'selected':''; ?>>
                                        <?php echo $cat['TenLoai']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="Hoạt động" <?php echo ($tour['TrangThai']=='Hoạt động')?'selected':''; ?>>Hoạt động</option>
                                <option value="Tạm dừng" <?php echo ($tour['TrangThai']=='Tạm dừng')?'selected':''; ?>>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Số ngày</label>
                            <input type="number" name="so_ngay" class="form-control" value="<?php echo $tour['SoNgay']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Số chỗ tối đa</label>
                            <input type="number" name="so_cho" class="form-control" value="<?php echo $tour['SoChoToiDa']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="4"><?php echo $tour['MoTa']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Ảnh đại diện hiện tại</label> <br>
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" width="100%" class="rounded border mb-2">
                        <input type="file" name="hinhanh" class="form-control">
                        <small>Chỉ chọn nếu muốn thay ảnh mới</small>
                    </div>
                    <div class="mb-3">
                        <label>Thêm ảnh vào thư viện</label>
                        <input type="file" name="gallery[]" class="form-control" multiple>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="schedule">
            <div id="schedule-container">
                <?php foreach($schedule as $k => $day): ?>
                <div class="card mb-3 schedule-item">
                    <div class="card-header fw-bold d-flex justify-content-between">
                        <span>Ngày <?php echo $k + 1; ?></span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.schedule-item').remove()">Xóa</button>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label>Tiêu đề</label>
                            <input type="text" name="schedule[<?php echo $k; ?>][title]" class="form-control" value="<?php echo $day['TieuDe']; ?>">
                        </div>
                        <div class="mb-2">
                            <label>Nội dung</label>
                            <textarea name="schedule[<?php echo $k; ?>][content]" class="form-control" rows="2"><?php echo $day['NoiDung']; ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-info text-white mt-2" onclick="addScheduleDay()">+ Thêm ngày tiếp theo</button>
        </div>

        <div class="tab-pane fade" id="policy">
            <div class="mb-3">
                <label>Chính sách Tour</label>
                <textarea name="chinh_sach" class="form-control" rows="10"><?php echo $tour['ChinhSach']; ?></textarea>
            </div>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary btn-lg">Cập nhật thay đổi</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let dayCount = <?php echo count($schedule); ?>; // Bắt đầu từ số lượng hiện có
    function addScheduleDay() {
        // ... (Code JS y hệt file create.php, chỉ cần paste vào đây) ...
        // Để ngắn gọn tôi không paste lại, bạn copy đoạn script từ create_content.php sang nhé.
        // Nhớ sửa dòng: name="schedule[${dayCount}][title]" (để index tiếp tục tăng)
    }
</script>

<script>
    let currentIndex = <?php echo count($schedule); ?>;

    function addScheduleDay() {
        const container = document.getElementById('schedule-container');
        currentIndex++;
        const html = `
            <div class="card mb-3 schedule-item">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>Ngày mới</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.schedule-item').remove()">Xóa</button>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label>Tiêu đề</label>
                        <input type="text" name="schedule[${currentIndex}][title]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Nội dung chi tiết</label>
                        <textarea name="schedule[${currentIndex}][content]" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>