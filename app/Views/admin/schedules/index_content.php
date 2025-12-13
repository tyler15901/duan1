<div class="pagetitle">
    <h1>Quản lý Lịch Khởi Hành</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Lịch trình</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h5 class="card-title p-0 m-0">Danh sách Lịch chạy</h5>
                        <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-primary shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tạo Lịch Mới
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-3">Mã Lịch / Tour</th>
                                    <th scope="col">Thời gian & Giá</th>
                                    <th scope="col">Điểm đón</th>
                                    <th scope="col" class="text-center">Tiến độ (Min/Max)</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($schedules)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>Chưa có lịch trình nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach($schedules as $s): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-light text-dark border mb-1"><?php echo $s['LichCode']; ?></span>
                                            <div class="fw-bold text-primary text-truncate" style="max-width: 280px;" title="<?php echo $s['TenTour']; ?>">
                                                <?php echo $s['TenTour']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="fw-bold">
                                                    <i class="bi bi-calendar-check me-1 text-success"></i> 
                                                    <?php echo !empty($s['NgayKhoiHanh']) ? date('d/m/Y', strtotime($s['NgayKhoiHanh'])) : '...'; ?>
                                                </div>
                                                <div class="text-muted">
                                                    <i class="bi bi-arrow-return-right me-1"></i> 
                                                    <?php echo !empty($s['NgayKetThuc']) ? date('d/m/Y', strtotime($s['NgayKetThuc'])) : '...'; ?>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-light text-danger border border-danger">
                                                    <?php echo ($s['GiaNguoiLon'] > 0) ? number_format($s['GiaNguoiLon']).' đ' : 'Liên hệ'; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted" style="line-height: 1.4;">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $s['DiaDiemTapTrung']; ?><br>
                                                <i class="bi bi-clock"></i> <?php echo !empty($s['GioTapTrung']) ? date('H:i', strtotime($s['GioTapTrung'])) : '--:--'; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                // Tính % dựa trên Max
                                                $max = $s['SoChoToiDa'] > 0 ? $s['SoChoToiDa'] : 1;
                                                $percent = round(($s['SoKhachHienTai'] / $max) * 100);
                                                
                                                // Màu thanh: Xanh (ít khách) -> Vàng (gần đủ) -> Đỏ (Full)
                                                // Hoặc logic theo Min: Đỏ (Chưa đủ min) -> Xanh (Đủ min)
                                                $barColor = ($s['SoKhachHienTai'] < $s['SoChoMin']) ? 'warning' : 'success';
                                                if ($percent >= 100) $barColor = 'danger';
                                            ?>
                                            <div class="fw-bold small mb-1">
                                                <?php echo $s['SoKhachHienTai']; ?> khách
                                                <span class="text-muted fw-normal" style="font-size: 0.8em;">(Min: <?php echo $s['SoChoMin']; ?>)</span>
                                            </div>
                                            <div class="progress" style="height: 6px; width: 100px; margin: 0 auto;" title="Đã đạt <?php echo $percent; ?>%">
                                                <div class="progress-bar bg-<?php echo $barColor; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                            <small class="text-muted" style="font-size: 10px;">Max: <?php echo $s['SoChoToiDa']; ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                $badge = 'bg-secondary';
                                                $icon = '';
                                                if ($s['TrangThai'] == 'Nhận khách') { $badge = 'bg-success'; $icon='<i class="bi bi-check-circle me-1"></i>'; }
                                                elseif ($s['TrangThai'] == 'Đang gom khách') { $badge = 'bg-warning text-dark'; $icon='<i class="bi bi-hourglass-split me-1"></i>'; }
                                                elseif ($s['TrangThai'] == 'Đã đóng sổ') { $badge = 'bg-danger'; $icon='<i class="bi bi-lock-fill me-1"></i>'; }
                                                elseif ($s['TrangThai'] == 'Hủy chuyến') { $badge = 'bg-dark'; $icon='<i class="bi bi-x-circle me-1"></i>'; }
                                            ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo $icon . $s['TrangThai']; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-eye text-primary me-2"></i> Chi tiết vận hành</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-people text-info me-2"></i> Danh sách khách</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-pencil text-warning me-2"></i> Chỉnh sửa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (isset($pagination)): ?>
                        <div class="mt-4 d-flex justify-content-center">
                            <?php include '../app/Views/layouts/pagination.php'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>