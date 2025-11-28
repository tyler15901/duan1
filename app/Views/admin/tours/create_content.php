<h2 class="mb-4">Tạo Tour Du Lịch Mới</h2>

<form action="<?php echo BASE_URL; ?>/tour/store" method="POST" enctype="multipart/form-data">
    
    <ul class="nav nav-tabs mb-4" id="tourTab" role="tablist">
        <li class="nav-item">
            <button class="nav-button nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">Thông tin chung</button>
        </li>
        <li class="nav-item">
            <button class="nav-button nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">Lịch trình chi tiết</button>
        </li>
        <li class="nav-item">
            <button class="nav-button nav-link" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy" type="button">Chính sách & Giá</button>
        </li>
    </ul>

    <div class="tab-content" id="tourTabContent">
        
        <div class="tab-pane fade show active" id="info">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Tour (*)</label>
                        <input type="text" name="ten_tour" class="form-control" required placeholder="VD: Khám phá Đà Nẵng - Hội An">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại Tour</label>
                            <select name="loai_tour" class="form-select">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['MaLoaiTour']; ?>"><?php echo $cat['TenLoai']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số ngày đi</label>
                            <input type="number" name="so_ngay" class="form-control" required value="1" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số chỗ tối đa (mỗi đoàn)</label>
                        <input type="number" name="so_cho" class="form-control" value="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn (Giới thiệu)</label>
                        <textarea name="mo_ta" class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Hình ảnh</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện (Thumbnail)</label>
                                <input type="file" name="hinhanh" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thư viện ảnh (Gallery)</label>
                                <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="schedule">
            <div id="schedule-container">
                <div class="card mb-3 schedule-item">
                    <div class="card-header fw-bold">
                        Ngày 1
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label>Tiêu đề (VD: Hà Nội - Sapa)</label>
                            <input type="text" name="schedule[0][title]" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Nội dung chi tiết</label>
                            <textarea name="schedule[0][content]" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-info text-white mt-2" onclick="addScheduleDay()">
                + Thêm ngày tiếp theo
            </button>
        </div>

        <div class="tab-pane fade" id="policy">
            <div class="alert alert-warning">
                <i class="bi bi-info-circle"></i> Lưu ý: Đây là giá tham khảo hiển thị trên web. Giá thực tế của từng đoàn sẽ được cấu hình khi tạo Lịch Khởi Hành.
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Giá Người Lớn (VNĐ)</label>
                    <input type="number" name="gia_nguoi_lon" class="form-control" placeholder="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Giá Trẻ Em (VNĐ)</label>
                    <input type="number" name="gia_tre_em" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Chính sách Tour (Bao gồm/Không bao gồm/Hủy hoãn)</label>
                <textarea name="chinh_sach" class="form-control" rows="10"></textarea>
            </div>
        </div>

    </div>

    <hr class="mt-4">
    <button type="submit" class="btn btn-success btn-lg px-5">Lưu Tour Mới</button>
    <a href="<?php echo BASE_URL; ?>/tour" class="btn btn-secondary btn-lg">Hủy</a>
</form>

<script>
    let dayCount = 1;

    function addScheduleDay() {
        dayCount++;
        const container = document.getElementById('schedule-container');
        
        const html = `
            <div class="card mb-3 schedule-item">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>Ngày ${dayCount}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.schedule-item').remove()">Xóa</button>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label>Tiêu đề</label>
                        <input type="text" name="schedule[${dayCount-1}][title]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Nội dung chi tiết</label>
                        <textarea name="schedule[${dayCount-1}][content]" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
        
        // Thêm HTML mới vào cuối container
        container.insertAdjacentHTML('beforeend', html);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>