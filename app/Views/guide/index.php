<div class="pagetitle">
    <h1>Xin chào, <?php echo $_SESSION['fullname']; ?>!</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item active">Lịch làm việc</li></ol></nav>
</div>

<section class="section">
    <div class="row">
        <?php if (empty($schedules)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-1"></i>
                <p class="mt-2">Bạn chưa có lịch tour nào sắp tới.</p>
            </div>
        <?php else: ?>
            <?php foreach ($schedules as $s): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3 shadow-sm border-0 h-100">
                    <div class="row g-0">
                        <div class="col-4">
                            <?php $img = $s['HinhAnh'] ? BASE_URL.'/assets/uploads/'.$s['HinhAnh'] : 'https://via.placeholder.com/150'; ?>
                            <img src="<?php echo $img; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;">
                        </div>
                        <div class="col-8">
                            <div class="card-body py-2 pe-2">
                                <h6 class="card-title text-primary mb-1 text-truncate fw-bold"><?php echo $s['TenTour']; ?></h6>
                                <span class="badge bg-light text-dark border mb-2"><?php echo $s['LichCode']; ?></span>
                                
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-calendar-event me-1"></i> <?php echo date('d/m', strtotime($s['NgayKhoiHanh'])); ?> - <?php echo date('d/m', strtotime($s['NgayKetThuc'])); ?>
                                </div>
                                <div class="small mb-2 text-truncate">
                                    <i class="bi bi-geo-alt text-danger me-1"></i> <?php echo $s['DiaDiemTapTrung']; ?>
                                </div>

                                <div class="d-flex gap-2 mt-2">
                                    <a href="<?php echo BASE_URL; ?>/guide/guests/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-primary flex-fill">
                                        <i class="bi bi-people-fill"></i> Khách
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/guide/diary/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-success flex-fill">
                                        <i class="bi bi-journal-text"></i> Nhật ký
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>