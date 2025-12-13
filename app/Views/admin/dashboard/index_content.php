<div class="pagetitle mb-4">
    <h1>Tổng quan hệ thống</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <div class="col-lg-8">
            <div class="row">

                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start"><h6>Thời gian</h6></li>
                                <li><a class="dropdown-item" href="#">Hôm nay</a></li>
                                <li><a class="dropdown-item" href="#">Tháng này</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Doanh Thu <span>| Năm nay</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo number_format($total_revenue_year / 1000000, 1); ?> Tr</h6>
                                    <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">tăng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Đơn <span>| Toàn bộ</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo $total_bookings; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">đơn hàng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-12">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Tour Đang Chạy <span>| Hiện tại</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bus-front"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo $total_tours; ?></h6>
                                    <span class="text-danger small pt-1 fw-bold">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start"><h6>Bộ lọc</h6></li>
                                <li><a class="dropdown-item" href="#">Năm nay</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Biểu Đồ Kinh Doanh <span>/Năm nay</span></h5>
                            <div id="reportsChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng mới nhất <span>| Hôm nay</span></h5>

                            <table class="table table-borderless datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Mã</th>
                                        <th scope="col">Khách hàng</th>
                                        <th scope="col">Ngày đặt</th>
                                        <th scope="col">Giá trị</th>
                                        <th scope="col">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_bookings)): ?>
                                        <?php foreach ($recent_bookings as $b): ?>
                                            <tr>
                                                <th scope="row"><a href="<?php echo BASE_URL; ?>/booking/detail/<?php echo $b['MaBooking']; ?>">#<?php echo $b['MaBooking']; ?></a></th>
                                                <td><?php echo $b['TenKhach']; ?></td>
                                                <td><a href="#" class="text-primary"><?php echo date('d/m', strtotime($b['NgayDat'])); ?></a></td>
                                                <td><?php echo number_format($b['TongTien']); ?>đ</td>
                                                <td>
                                                    <?php
                                                    $badgeClass = 'bg-secondary';
                                                    if ($b['TrangThai'] == 'Đã xác nhận') $badgeClass = 'bg-success';
                                                    elseif ($b['TrangThai'] == 'Chờ xác nhận') $badgeClass = 'bg-warning text-dark';
                                                    elseif ($b['TrangThai'] == 'Đã hủy') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $b['TrangThai']; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center">Chưa có đơn hàng nào</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-4">

            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Cần Xử Lý <span>| Chờ duyệt</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background: #ffe0e3; color: #dc3545;">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo $new_orders; ?></h6>
                            <span class="text-muted small pt-2 ps-1">đơn chờ duyệt</span>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/booking/index?status=Chờ xác nhận" class="stretched-link"></a>
                </div>
            </div>

            <div class="card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start"><h6>Bộ lọc</h6></li>
                        <li><a class="dropdown-item" href="#">Mới nhất</a></li>
                        <li><a class="dropdown-item" href="#">Đã xem</a></li>
                    </ul>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Thông Báo <span>| Mới nhất</span></h5>

                    <div class="activity">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $noti): ?>
            <div class="activity-item d-flex">
                <div class="activite-label">
                    <?php echo date('d/m H:i', strtotime($noti['NgayTao'])); ?>
                </div>
                
                <?php 
                    // [LOGIC MÀU SẮC]
                    // Nếu DaXem = 0 (Chưa xem) -> Màu Vàng (warning)
                    // Nếu DaXem = 1 (Đã xem) -> Màu Xanh (success) hoặc Xám (muted)
                    $statusColor = ($noti['DaXem'] == 0) ? 'text-warning' : 'text-success';
                    
                    // Icon tùy loại
                    $iconType = 'bi-circle-fill';
                ?>
                
                <i class='bi <?php echo $iconType; ?> activity-badge <?php echo $statusColor; ?> align-self-start'></i>
                
                <div class="activity-content">
                    <strong><?php echo $noti['TieuDe']; ?></strong>
                    <p class="small text-muted mb-1"><?php echo $noti['NoiDung']; ?></p>
                    
                    <?php if (!empty($noti['LienKet'])): ?>
                        <a href="<?php echo BASE_URL; ?>/dashboard/handle_notification/<?php echo $noti['MaThongBao']; ?>" 
                           class="btn btn-sm btn-outline-primary py-0 mt-1" 
                           style="font-size: 11px;">
                            <i class="bi bi-arrow-right-short"></i> Xử lý ngay
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-bell-slash fs-1 opacity-25"></i>
            <p class="small mt-2">Hiện tại không có thông báo mới.</p>
        </div>
    <?php endif; ?>
</div>

                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const labels = <?php echo $chart_labels; ?>;
        const revenueData = <?php echo $chart_revenue; ?>;
        const profitData = <?php echo $chart_profit; ?>;

        new ApexCharts(document.querySelector("#reportsChart"), {
            series: [{
                name: 'Doanh thu',
                data: revenueData,
            }, {
                name: 'Lợi nhuận',
                data: profitData
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
            },
            markers: { size: 4 },
            colors: ['#4154f1', '#2eca6a'],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.4,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                type: 'category',
                categories: labels,
                tooltip: { enabled: false }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
                    }
                }
            }
        }).render();
    });
</script>