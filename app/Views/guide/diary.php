<div class="pagetitle">
    <h1>Nhật ký hành trình</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/guide/index">Lịch trình</a></li>
            <li class="breadcrumb-item active">Nhật ký tour</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-pen-fill me-2"></i> Viết nhật ký mới
                </div>
                <div class="card-body pt-3">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề</label>
                            <input type="text" name="tieu_de" class="form-control" placeholder="VD: Ngày 1 - Đón khách tại sân bay" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung chi tiết</label>
                            <textarea name="noi_dung" class="form-control" rows="6" placeholder="Ghi lại các sự kiện, sự cố, phản hồi của khách hàng..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send-fill me-2"></i> Lưu Nhật Ký
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i> Lịch sử hành trình</h5>
                </div>
                <div class="card-body pt-4">
                    <?php if(empty($logs)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-1 opacity-25"></i>
                            <p class="mt-2">Chưa có nhật ký nào được ghi lại.</p>
                        </div>
                    <?php else: ?>
                        <div class="activity">
                            <?php foreach($logs as $log): ?>
                            <div class="activity-item d-flex mb-4">
                                <div class="activity-label text-muted text-end pe-3" style="min-width: 90px;">
                                    <div class="fw-bold fs-6"><?php echo date('H:i', strtotime($log['NgayGhi'])); ?></div>
                                    <div class="small"><?php echo date('d/m', strtotime($log['NgayGhi'])); ?></div>
                                </div>
                                <div class="position-relative border-start border-2 ps-4 pb-1 w-100">
                                    <i class='bi bi-circle-fill activity-badge text-success position-absolute top-0 start-0 translate-middle' style="margin-top: 5px;"></i>
                                    
                                    <div class="bg-light p-3 rounded border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 fw-bold text-dark"><?php echo $log['TieuDe']; ?></h6>
                                            <a href="<?php echo BASE_URL; ?>/guide/delete_diary/<?php echo $log['MaNhatKy']; ?>/<?php echo $scheduleId; ?>" 
                                               class="btn btn-sm btn-link text-danger p-0" 
                                               onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                        <p class="mb-0 text-secondary" style="white-space: pre-line;"><?php echo $log['NoiDung']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>