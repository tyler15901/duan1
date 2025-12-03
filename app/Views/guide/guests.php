<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0">Danh sách đoàn</h5>
    <a href="<?php echo BASE_URL; ?>/guide/index" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <?php if(empty($guests)): ?>
                <li class="list-group-item text-center py-4">Chưa có khách trong đoàn này.</li>
            <?php else: ?>
                <?php foreach($guests as $g): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center" style="max-width: 70%;">
                        <div class="bg-light rounded-circle p-2 me-3 text-secondary position-relative">
                            <i class="bi bi-person-fill fs-4"></i>
                            <?php if($g['YeuCauDacBiet']): ?>
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <div class="fw-bold text-dark"><?php echo $g['HoTen']; ?></div>
                            <div class="small text-muted">
                                <a href="tel:<?php echo $g['SoDienThoai']; ?>" class="text-decoration-none text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i> <?php echo $g['SoDienThoai']; ?>
                                </a>
                            </div>
                            <?php if($g['YeuCauDacBiet']): ?>
                                <div class="text-danger small fst-italic mt-1 border-start border-danger ps-2">
                                    NOTE: <?php echo $g['YeuCauDacBiet']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-light text-primary border-0 rounded-circle" 
                                onclick="openNoteModal(<?php echo $g['MaChiTiet']; ?>, '<?php echo $g['HoTen']; ?>')">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </button>

                        <div class="form-check form-switch">
                            <input class="form-check-input fs-3" type="checkbox" role="switch" 
                                   onchange="toggleCheckIn(this, <?php echo $scheduleId; ?>, <?php echo $g['MaChiTiet']; ?>)"
                                   <?php echo ($g['IsCheckedIn'] > 0) ? 'checked' : ''; ?>>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/guide/update_request" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Ghi chú: <span id="guestName" class="fw-bold text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                    <input type="hidden" name="guest_id" id="guestIdInput">
                    <label class="form-label">Yêu cầu đặc biệt / Sức khỏe / Ăn uống:</label>
                    <textarea name="content" class="form-control" rows="4" placeholder="VD: Khách ăn chay trường, dị ứng hải sản..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Lưu Ghi Chú</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JS Mở modal
    function openNoteModal(id, name) {
        document.getElementById('guestIdInput').value = id;
        document.getElementById('guestName').innerText = name;
        new bootstrap.Modal(document.getElementById('noteModal')).show();
    }

    // JS Check-in AJAX
    function toggleCheckIn(checkbox, scheduleId, guestId) {
        const status = checkbox.checked ? 'check' : 'uncheck';
        
        const formData = new FormData();
        formData.append('schedule_id', scheduleId);
        formData.append('guest_id', guestId);
        formData.append('status', status);

        fetch('<?php echo BASE_URL; ?>/guide/ajax_checkin', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) {
                alert('Lỗi cập nhật!');
                checkbox.checked = !checkbox.checked; // Revert nếu lỗi
            }
        })
        .catch(err => {
            console.error(err);
            checkbox.checked = !checkbox.checked;
        });
    }
</script>