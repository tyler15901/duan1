<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0">Nhật ký hành trình</h5>
    <a href="<?php echo BASE_URL; ?>/guide/index" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light rounded">
        <form action="" method="POST">
            <div class="mb-2">
                <input type="text" name="tieu_de" class="form-control fw-bold" placeholder="Tiêu đề (VD: Ngày 1 - Đón khách)" required>
            </div>
            <div class="mb-2">
                <textarea name="noi_dung" class="form-control" rows="3" placeholder="Ghi lại sự kiện, sự cố, phản hồi khách..." required></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Gửi nhật ký</button>
            </div>
        </form>
    </div>
</div>

<div class="activity">
    <?php if(empty($logs)): ?>
        <p class="text-center text-muted py-3">Chưa có nhật ký nào.</p>
    <?php else: ?>
        <?php foreach($logs as $log): ?>
        <div class="activity-item d-flex mb-4">
            <div class="activity-label text-muted small text-end pe-3" style="min-width: 80px;">
                <div class="fw-bold"><?php echo date('H:i', strtotime($log['NgayGhi'])); ?></div>
                <div><?php echo date('d/m', strtotime($log['NgayGhi'])); ?></div>
            </div>
            <div class="position-relative border-start border-2 ps-4 pb-0">
                <i class='bi bi-circle-fill activity-badge text-success position-absolute top-0 start-0 translate-middle' style="margin-top: 5px;"></i>
                <div class="d-flex justify-content-between align-items-start">
                    <strong class="text-dark"><?php echo $log['TieuDe']; ?></strong>
                    <a href="<?php echo BASE_URL; ?>/guide/delete_diary/<?php echo $log['MaNhatKy']; ?>/<?php echo $scheduleId; ?>" 
                       class="text-danger small ms-2" onclick="return confirm('Xóa dòng này?')"><i class="bi bi-trash"></i></a>
                </div>
                <p class="text-muted small mt-1 mb-0" style="white-space: pre-line;"><?php echo $log['NoiDung']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>