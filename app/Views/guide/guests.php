<div class="pagetitle">
    <h1>Quản lý Đoàn khách</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/guide/index">Lịch trình</a></li>
            <li class="breadcrumb-item active">Danh sách khách</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="bi bi-people-fill me-2"></i> Danh sách thành viên
                    </h5>
                    <span class="badge bg-light text-dark border fs-6">
                        Tổng: <?php echo count($guests); ?> pax
                    </span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Họ và tên</th>
                                    <th>Liên hệ</th>
                                    <th class="text-center">Giới tính</th>
                                    <th>Ghi chú / Yêu cầu</th>
                                    <th class="text-center">Check-in</th>
                                    <th class="text-end pe-4">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($guests)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có dữ liệu khách hàng.</td></tr>
                                <?php else: ?>
                                    <?php foreach($guests as $g): ?>
                                    <tr class="<?php echo $g['IsCheckedIn'] ? 'table-success bg-opacity-10' : ''; ?>">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center text-secondary me-3 shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo $g['HoTen']; ?></div>
                                                    <div class="small text-muted">Mã vé: <?php echo $g['MaBookingCode']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><i class="bi bi-telephone me-1"></i> <?php echo $g['SoDienThoai']; ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?php echo $g['GioiTinh'] == 'Nam' ? '<i class="bi bi-gender-male text-primary"></i>' : '<i class="bi bi-gender-female text-danger"></i>'; ?>
                                        </td>
                                        <td>
                                            <?php if($g['YeuCauDacBiet']): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                    <i class="bi bi-exclamation-triangle me-1"></i> <?php echo $g['YeuCauDacBiet']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">- Không -</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input fs-4 cursor-pointer" type="checkbox" role="switch" 
                                                       onchange="toggleCheckIn(this, <?php echo $scheduleId; ?>, <?php echo $g['MaChiTiet']; ?>)"
                                                       <?php echo ($g['IsCheckedIn'] > 0) ? 'checked' : ''; ?>
                                                       title="Xác nhận khách đã đến">
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="openNoteModal(<?php echo $g['MaChiTiet']; ?>, '<?php echo $g['HoTen']; ?>', '<?php echo $g['YeuCauDacBiet']; ?>')">
                                                <i class="bi bi-pencil-square me-1"></i> Note
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/guide/update_request" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Ghi chú: <span id="guestName" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                    <input type="hidden" name="guest_id" id="guestIdInput">
                    <label class="form-label fw-bold small text-uppercase">Yêu cầu đặc biệt / Sức khỏe / Ăn uống:</label>
                    <textarea name="content" id="noteContent" class="form-control" rows="4" placeholder="VD: Khách ăn chay trường, dị ứng hải sản, say xe..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Ghi Chú</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openNoteModal(id, name, content) {
        document.getElementById('guestIdInput').value = id;
        document.getElementById('guestName').innerText = name;
        document.getElementById('noteContent').value = content || '';
        new bootstrap.Modal(document.getElementById('noteModal')).show();
    }

    function toggleCheckIn(checkbox, scheduleId, guestId) {
        const status = checkbox.checked ? 'check' : 'uncheck';
        const row = checkbox.closest('tr');

        fetch('<?php echo BASE_URL; ?>/guide/ajax_checkin', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `schedule_id=${scheduleId}&guest_id=${guestId}&status=${status}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(checkbox.checked) {
                    row.classList.add('table-success', 'bg-opacity-10');
                } else {
                    row.classList.remove('table-success', 'bg-opacity-10');
                }
            } else {
                alert('Lỗi cập nhật!');
                checkbox.checked = !checkbox.checked;
            }
        });
    }
</script>