<div class="pagetitle">
    <h1>Lịch trình công việc</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card info-card sales-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Tour Được Giao</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo count($schedules); ?></h6>
                            <span class="text-muted small pt-2 ps-1">lịch trình</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card recent-sales overflow-auto">
                <div class="card-body">
                    <h5 class="card-title">Danh sách phân công <span>| Của bạn</span></h5>

                    <?php if (empty($schedules)): ?>
                        <div class="alert alert-light text-center border">
                            <i class="bi bi-info-circle me-1"></i> Bạn chưa có lịch trình nào sắp tới.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Mã Lịch</th>
                                        <th scope="col">Tên Tour</th>
                                        <th scope="col">Thời gian</th>
                                        <th scope="col">Khách</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col" class="text-end">Tác vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedules as $s): ?>
                                        <tr>
                                            <td><span
                                                    class="badge bg-light text-primary border fw-bold"><?php echo $s['LichCode']; ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo $s['TenTour']; ?></div>
                                                <div class="small text-muted"><i class="bi bi-geo-alt"></i>
                                                    <?php echo $s['DiaDiemTapTrung']; ?></div>
                                            </td>
                                            <td>
                                                <div><?php echo date('d/m', strtotime($s['NgayKhoiHanh'])); ?></div>
                                                <div class="small text-muted">đến
                                                    <?php echo date('d/m', strtotime($s['NgayKetThuc'])); ?></div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-info text-dark rounded-pill"><?php echo $s['SoKhachHienTai']; ?>
                                                    pax</span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'bg-secondary';
                                                if ($s['TrangThai'] == 'Đang chạy')
                                                    $statusClass = 'bg-success';
                                                elseif ($s['TrangThai'] == 'Nhận khách')
                                                    $statusClass = 'bg-warning text-dark';
                                                elseif ($s['TrangThai'] == 'Đã đóng sổ')
                                                    $statusClass = 'bg-danger';
                                                ?>
                                                <span
                                                    class="badge <?php echo $statusClass; ?>"><?php echo $s['TrangThai']; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?php echo BASE_URL; ?>/guide/detail/<?php echo $s['MaLichKhoiHanh']; ?>"
                                                    class="btn btn-sm btn-primary fw-bold me-1" title="Lịch trình chi tiết">
                                                    <i class="bi bi-calendar-week me-1"></i> Lịch trình
                                                </a>

                                                <a href="<?php echo BASE_URL; ?>/guide/guests/<?php echo $s['MaLichKhoiHanh']; ?>"
                                                    class="btn btn-sm btn-outline-secondary" title="Danh sách khách">
                                                    <i class="bi bi-people-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>