<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted small">Dashboard</h4>
        <h2 class="text-primary fw-bold mb-0">Tổng quan Hệ thống</h2>
    </div>
    <div>
        <span class="text-muted me-3 small"><i class="bi bi-calendar-check"></i> Hôm nay: <b><?php echo date('d/m/Y'); ?></b></span>
        <a href="<?php echo BASE_URL; ?>/booking/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Tạo Đơn Mới
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Doanh thu năm</div>
                        <h3 class="text-primary fw-bold mb-0"><?php echo number_format($total_revenue_year / 1000000, 1); ?> <span class="fs-6 text-muted">Tr</span></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Tổng Đơn Hàng</div>
                        <h3 class="text-success fw-bold mb-0"><?php echo $total_bookings; ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Tour Đang chạy</div>
                        <h3 class="text-info text-dark fw-bold mb-0"><?php echo $total_tours; ?> <span class="fs-6 text-muted">đoàn</span></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="bi bi-bus-front fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Cần Xử Lý</div>
                        <h3 class="text-danger fw-bold mb-0"><?php echo $new_orders; ?> <span class="fs-6 text-muted">đơn</span></h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded p-3 position-relative">
                        <i class="bi bi-bell fs-3"></i>
                        <?php if($new_orders > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/booking/index?status=Chờ xác nhận" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-graph-up-arrow text-primary"></i> Hiệu quả kinh doanh</h6>
                <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-secondary">
                    <option>Năm nay</option>
                    <option>Năm ngoái</option>
                </select>
            </div>
            <div class="card-body">
                <canvas id="dashboardChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-clock-history text-warning"></i> Đơn hàng vừa nhận</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if(empty($recent_bookings)): ?>
                        <div class="text-center p-4 text-muted">Chưa có đơn hàng mới nào.</div>
                    <?php else: ?>
                        <?php foreach($recent_bookings as $b): ?>
                        <a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>" class="list-group-item list-group-item-action px-3 py-3 border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3" 
                                     style="width: 45px; height: 45px; min-width: 45px;">
                                    <span class="fw-bold text-secondary"><?php echo substr($b['TenKhach'], 0, 1); ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark text-truncate" style="max-width: 140px;"><?php echo $b['TenKhach']; ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <?php echo date('d/m H:i', strtotime($b['NgayDat'])); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="text-primary fw-bold small"><?php echo number_format($b['TongTien']); ?>đ</span>
                                        <?php 
                                            $bgClass = match($b['TrangThai']) {
                                                'Chờ xác nhận' => 'warning text-dark',
                                                'Đã xác nhận' => 'success',
                                                'Đã hủy' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo $bgClass; ?> bg-opacity-75 rounded-pill" style="font-size: 0.65rem;">
                                            <?php echo $b['TrangThai']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-white text-center border-0 py-3">
                <a href="<?php echo BASE_URL; ?>/booking/index" class="text-decoration-none fw-bold small">Xem tất cả đơn hàng <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    
    // Gradient màu cho đẹp hơn
    let gradientRev = ctx.createLinearGradient(0, 0, 0, 400);
    gradientRev.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // Blue
    gradientRev.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

    let gradientProfit = ctx.createLinearGradient(0, 0, 0, 400);
    gradientProfit.addColorStop(0, 'rgba(25, 135, 84, 0.5)'); // Green
    gradientProfit.addColorStop(1, 'rgba(25, 135, 84, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $chart_labels; ?>, // Đảm bảo controller trả về JSON String
            datasets: [
                {
                    label: 'Doanh thu',
                    data: <?php echo $chart_revenue; ?>,
                    borderColor: '#0d6efd', // Bootstrap Primary
                    backgroundColor: gradientRev,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4 // Đường cong mềm mại
                },
                {
                    label: 'Lợi nhuận',
                    data: <?php echo $chart_profit; ?>,
                    borderColor: '#198754', // Bootstrap Success
                    backgroundColor: gradientProfit,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#198754',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'top', align: 'end' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4], color: '#f0f0f0' },
                    ticks: { callback: function(value) { return value.toLocaleString() + ' đ'; } }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>