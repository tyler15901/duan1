<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold mb-0">Lịch Khởi Hành</h2>
        <?php if(!empty($filter_msg)): ?>
            <span class="badge bg-warning text-dark mt-2">
                <i class="bi bi-funnel-fill"></i> <?php echo $filter_msg; ?>
                <a href="<?php echo BASE_URL; ?>/schedule/index" class="text-dark ms-2 text-decoration-none"><i class="bi bi-x-lg"></i></a>
            </span>
        <?php endif; ?>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-calendar-plus"></i> Tạo Lịch Mới
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Mã Lịch / Tour</th>
                        <th>Thời gian</th>
                        <th>Điểm đón</th>
                        <th class="text-center">Số khách</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($schedules)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có lịch trình nào.</td></tr>
                    <?php else: ?>
                        <?php foreach($schedules as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border mb-1"><?php echo $s['LichCode']; ?></span>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;"><?php echo $s['TenTour']; ?></div>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-success fw-bold"><i class="bi bi-box-arrow-right me-1"></i> <?php echo date('d/m/Y', strtotime($s['NgayKhoiHanh'])); ?></span>
                                    <span class="text-muted"><i class="bi bi-box-arrow-in-left me-1"></i> <?php echo date('d/m/Y', strtotime($s['NgayKetThuc'])); ?></span>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i> <?php echo $s['DiaDiemTapTrung']; ?><br>
                                    <i class="bi bi-clock me-1"></i> <?php echo date('H:i', strtotime($s['GioTapTrung'])); ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <?php 
                                    $percent = ($s['SoChoToiDa'] > 0) ? round(($s['SoKhachHienTai'] / $s['SoChoToiDa']) * 100) : 0;
                                    $progressColor = ($percent < 50) ? 'success' : (($percent < 90) ? 'warning' : 'danger');
                                ?>
                                <div class="fw-bold text-dark"><?php echo $s['SoKhachHienTai']; ?> <span class="text-muted fw-normal">/ <?php echo $s['SoChoToiDa']; ?></span></div>
                                <div class="progress mt-1" style="height: 4px; width: 80px; margin: 0 auto;">
                                    <div class="progress-bar bg-<?php echo $progressColor; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $statusClass = 'secondary text-white';
                                    if ($s['TrangThai'] == 'Nhận khách') $statusClass = 'success text-white';
                                    elseif ($s['TrangThai'] == 'Đang chạy') $statusClass = 'warning text-dark';
                                    elseif ($s['TrangThai'] == 'Hoàn tất') $statusClass = 'info text-dark';
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?> bg-opacity-75 px-2 py-1 border border-white shadow-sm">
                                    <?php echo $s['TrangThai']; ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-eye me-2 text-info"></i> Chi tiết</a></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-people me-2 text-primary"></i> Danh sách khách</a></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/expenses/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-cash-stack me-2 text-success"></i> Chi phí & Lãi</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-pencil me-2 text-warning"></i> Sửa lịch</a></li>
                                        <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/schedule/delete/<?php echo $s['MaLichKhoiHanh']; ?>" onclick="return confirm('Xóa lịch này?')"><i class="bi bi-trash me-2"></i> Xóa</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white border-0 py-3">
        <?php include '../app/Views/layouts/pagination.php'; ?>
    </div>
</div>