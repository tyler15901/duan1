<div class="d-flex align-items-center justify-content-between mb-3">
    <a href="<?php echo BASE_URL; ?>/guide/detail/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-light border btn-sm shadow-sm">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
    <div class="text-end">
        <h6 class="m-0 fw-bold">Ngày <?php echo $dayNum; ?></h6>
        <small class="text-muted"><?php echo date('d/m/Y', strtotime($realDate)); ?></small>
    </div>
</div>

<ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded shadow-sm" id="pills-tab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-plan">
            <i class="bi bi-info-circle"></i> Lịch trình
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-checkin">
            <i class="bi bi-check2-square"></i> Điểm danh
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-diary">
            <i class="bi bi-journal-text"></i> Nhật ký
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-photos">
            <i class="bi bi-images"></i> Ảnh
        </button>
    </li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane fade show active" id="tab-plan">
        <div class="card shadow-sm border-0 border-top border-4 border-info">
            <div class="card-body p-4">
                <?php if (!empty($dayPlan)): ?>
                    <h5 class="fw-bold text-primary mb-3 text-uppercase">
                        <i class="bi bi-geo-alt-fill me-2"></i><?php echo $dayPlan['TieuDe']; ?>
                    </h5>
                    <div class="content-body text-dark" style="white-space: pre-line; line-height: 1.8; font-size: 1.05rem;">
                        <?php echo $dayPlan['NoiDung']; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-x fs-1 opacity-25"></i>
                        <p class="mt-2">Chưa có nội dung chi tiết cho ngày này.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-checkin">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-success"><i class="bi bi-people-fill me-2"></i> Danh sách khách</span>
                <div class="small">
                    <span class="me-2"><i class="bi bi-sun-fill text-warning"></i> Sáng</span>
                    <span><i class="bi bi-moon-stars-fill text-primary"></i> Chiều</span>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if(empty($guests)): ?>
                    <div class="p-4 text-center text-muted">Không có khách.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                    <?php foreach($guests as $g): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center" style="max-width: 60%;">
                                <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px;height:40px">
                                    <?php echo substr($g['HoTen'],0,1); ?>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate"><?php echo $g['HoTen']; ?></div>
                                    <div class="small text-muted"><?php echo $g['SoDienThoai']; ?></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch text-center">
                                    <input class="form-check-input fs-5 cursor-pointer border-warning" type="checkbox" 
                                           onchange="toggleCheckIn(this, <?php echo $g['MaChiTiet']; ?>, 'AM')"
                                           <?php echo (!empty($g['CheckInAM']) && $g['CheckInAM'] > 0) ? 'checked' : ''; ?>>
                                </div>

                                <div class="form-check form-switch text-center">
                                    <input class="form-check-input fs-5 cursor-pointer border-primary" type="checkbox" 
                                           onchange="toggleCheckIn(this, <?php echo $g['MaChiTiet']; ?>, 'PM')"
                                           <?php echo (!empty($g['CheckInPM']) && $g['CheckInPM'] > 0) ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-diary">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form action="<?php echo BASE_URL; ?>/guide/save_day_diary" method="POST">
                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['MaLichKhoiHanh']; ?>">
                    <input type="hidden" name="day_num" value="<?php echo $dayNum; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tiêu đề nhật ký</label>
                        <input type="text" name="tieu_de" class="form-control" 
                               value="<?php echo $diary['TieuDe'] ?? ($dayPlan['TieuDe'] ?? "Ngày " . $dayNum); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Nội dung ghi chép</label>
                        <textarea name="noi_dung" class="form-control" rows="6" placeholder="Ghi lại sự kiện, cảm nhận, vấn đề phát sinh..." required><?php echo $diary['NoiDung'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Lưu Nhật Ký</button>
                </form>
                <?php if(!empty($diary['NgayGhi'])): ?>
                    <div class="mt-3 text-end small text-muted">
                        <i class="bi bi-clock-history"></i> Cập nhật: <?php echo date('H:i d/m', strtotime($diary['NgayGhi'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-photos">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <form action="<?php echo BASE_URL; ?>/guide/upload_photo" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['MaLichKhoiHanh']; ?>">
                    <input type="hidden" name="day_num" value="<?php echo $dayNum; ?>">
                    <label class="form-label fw-bold">Thêm ảnh hoạt động</label>
                    <div class="input-group">
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                        <button class="btn btn-success" type="submit"><i class="bi bi-upload"></i> Tải lên</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-2">
            <?php if(empty($photos)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-images fs-1 opacity-25"></i>
                    <p class="mt-2">Chưa có ảnh nào cho ngày này.</p>
                </div>
            <?php else: foreach($photos as $p): ?>
                <div class="col-6 col-md-4">
                    <div class="position-relative">
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/tours/<?php echo $p['DuongDan']; ?>" class="img-fluid rounded shadow-sm w-100" style="height: 150px; object-fit: cover;">
                        <a href="<?php echo BASE_URL; ?>/guide/delete_photo/<?php echo $p['MaHinhAnh']; ?>/<?php echo $schedule['MaLichKhoiHanh']; ?>/<?php echo $dayNum; ?>" 
                           class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 py-0 px-1 rounded-circle shadow" 
                           onclick="return confirm('Xóa ảnh này?')">
                           <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<script>
    // Logic check-in AJAX (Đã cập nhật để nhận biết loại AM/PM)
    function toggleCheckIn(checkbox, guestId, type) {
        const status = checkbox.checked ? 'check' : 'uncheck';
        const date = '<?php echo $realDate; ?>';
        const sid = <?php echo $schedule['MaLichKhoiHanh']; ?>;

        fetch('<?php echo BASE_URL; ?>/guide/ajax_checkin_day', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `schedule_id=${sid}&guest_id=${guestId}&status=${status}&date=${date}&type=${type}`
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) {
                alert('Lỗi cập nhật!');
                checkbox.checked = !checkbox.checked;
            }
        });
    }

    // Logic giữ Tab khi reload
    document.addEventListener("DOMContentLoaded", function(){
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if(tab) {
            const triggerEl = document.querySelector(`button[data-bs-target="#tab-${tab}"]`);
            if(triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    });
</script>