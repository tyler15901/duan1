<div class="pagetitle">
    <h1>Nhật ký hành trình</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/guide/index">Dashboard</a></li>
            <li class="breadcrumb-item active">Nhật ký</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Viết nhật ký mới</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề</label>
                            <input type="text" name="tieu_de" class="form-control" placeholder="VD: Ngày 1 - Đón khách" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung</label>
                            <textarea name="noi_dung" class="form-control" rows="5" placeholder="Ghi lại sự kiện..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Lưu Nhật Ký
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Dòng thời gian <span>| Tour này</span></h5>

                    <div class="activity">
                        <?php if(empty($logs)): ?>
                            <div class="text-center text-muted py-3">Chưa có nhật ký nào.</div>
                        <?php else: ?>
                            <?php foreach($logs as $log): ?>
                            <div class="activity-item d-flex">
                                <div class="activity-label">
                                    <?php echo date('H:i', strtotime($log['NgayGhi'])); ?>
                                    <div class="small text-muted"><?php echo date('d/m', strtotime($log['NgayGhi'])); ?></div>
                                </div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content w-100">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-dark"><?php echo $log['TieuDe']; ?></strong>
                                        <a href="<?php echo BASE_URL; ?>/guide/delete_diary/<?php echo $log['MaNhatKy']; ?>/<?php echo $scheduleId; ?>" 
                                           class="text-danger small" onclick="return confirm('Xóa?')">
                                           <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <p class="mb-0 mt-1 text-muted small" style="white-space: pre-line;"><?php echo $log['NoiDung']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>