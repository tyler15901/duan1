<div class="pagetitle">
    <h1><?php echo $schedule['LichCode']; ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/guide/index">Dashboard</a></li>
            <li class="breadcrumb-item active">Lịch trình</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm mb-4 border-start border-4 border-primary">
    <div class="card-body p-3">
        <h5 class="fw-bold text-primary m-0"><?php echo $schedule['TenTour']; ?></h5>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="badge bg-light text-dark border">
                <i class="bi bi-clock"></i> <?php echo $schedule['SoNgay']; ?> ngày
            </span>
            <span class="small text-muted">
                <?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?> 
                <i class="bi bi-arrow-right"></i> 
                <?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?>
            </span>
        </div>
    </div>
</div>

<h6 class="fw-bold text-muted text-uppercase mb-3 ps-2 small">Chọn ngày làm việc</h6>

<div class="row g-3">
    <?php foreach($timeline as $day): ?>
    <div class="col-12"> <a href="<?php echo BASE_URL; ?>/guide/day/<?php echo $schedule['MaLichKhoiHanh']; ?>/<?php echo $day['NgayThu']; ?>" 
           class="text-decoration-none">
            
            <div class="card border-0 shadow-sm hover-shadow mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="flex-shrink-0 text-center me-3 bg-light rounded p-2 border" style="min-width: 60px;">
                            <span class="d-block small text-muted text-uppercase" style="font-size: 10px;">Ngày</span>
                            <span class="d-block fs-4 fw-bold text-primary lh-1"><?php echo $day['NgayThu']; ?></span>
                        </div>
                        
                        <div class="text-truncate">
                            <h6 class="card-title text-dark mb-1 fw-bold text-truncate"><?php echo $day['TieuDe']; ?></h6>
                            <div class="small text-muted">
                                <i class="bi bi-calendar-event me-1"></i> 
                                <span class="text-danger fw-bold"><?php echo date('d/m/Y', strtotime($day['RealDate'])); ?></span>
                                <span class="mx-1">•</span>
                                <span class="text-secondary text-truncate" style="max-width: 150px; display: inline-block; vertical-align: bottom;">
                                    <?php echo strip_tags($day['NoiDung']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-primary ms-2">
                        <i class="bi bi-chevron-right fs-5"></i>
                    </div>

                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all 0.2s;
    }
</style>