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
        </div><div class="col-lg-4">

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
                <div class="card-body">
                    <h5 class="card-title">Hoạt Động <span>| Gần đây</span></h5>

                    <div class="activity">
                        <?php if (!empty($recent_bookings)): ?>
                            <?php foreach (array_slice($recent_bookings, 0, 5) as $item): ?>
                            <div class="activity-item d-flex">
                                <div class="activity-label"><?php echo date('H:i', strtotime($item['NgayDat'])); ?></div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                    Khách <strong><?php echo $item['TenKhach']; ?></strong> vừa đặt tour
                                    <div class="text-muted small"><?php echo number_format($item['TongTien']); ?> vnđ</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-3">
                <a href="<?php echo BASE_URL; ?>/booking/create" class="btn btn-primary btn-lg shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Tạo Booking Mới
                </a>
            </div>

        </div></div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Dữ liệu từ PHP
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
                    formatter: function(val) {
                        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
                    }
                }
            }
        }).render();
    });
</script>