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
                        <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Tạo Lịch Mới
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">Mã Lịch / Tour</th>
                                    <th scope="col">Thời gian & Giá</th>
                                    <th scope="col">Điểm đón</th>
                                    <th scope="col" class="text-center">Số khách</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($schedules)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có lịch trình nào.</td></tr>
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
                                                <div class="fw-bold"><i class="bi bi-calendar-check me-1 text-success"></i> <?php echo date('d/m', strtotime($s['NgayKhoiHanh'])); ?></div>
                                                <div class="text-muted"><i class="bi bi-calendar-x me-1"></i> <?php echo date('d/m', strtotime($s['NgayKetThuc'])); ?></div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge bg-light text-danger border border-danger">
                                                    <i class="bi bi-tag-fill me-1"></i> 
                                                    <?php echo ($s['GiaNguoiLon'] > 0) ? number_format($s['GiaNguoiLon']).' đ' : 'Liên hệ'; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted" style="line-height: 1.4;">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $s['DiaDiemTapTrung']; ?><br>
                                                <i class="bi bi-alarm"></i> <?php echo date('H:i', strtotime($s['GioTapTrung'])); ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $percent = ($s['SoChoToiDa'] > 0) ? round(($s['SoKhachHienTai'] / $s['SoChoToiDa']) * 100) : 0;
                                                $barColor = ($percent < 70) ? 'success' : (($percent < 90) ? 'warning' : 'danger');
                                            ?>
                                            <div class="fw-bold small mb-1"><?php echo $s['SoKhachHienTai']; ?> / <?php echo $s['SoChoToiDa']; ?></div>
                                            <div class="progress" style="height: 6px; width: 80px; margin: 0 auto;">
                                                <div class="progress-bar bg-<?php echo $barColor; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $badge = 'bg-secondary';
                                                if ($s['TrangThai'] == 'Nhận khách') $badge = 'bg-success';
                                                elseif ($s['TrangThai'] == 'Đang chạy') $badge = 'bg-warning text-dark';
                                                elseif ($s['TrangThai'] == 'Hoàn tất') $badge = 'bg-info text-dark';
                                            ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo $s['TrangThai']; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-eye text-primary me-2"></i> Chi tiết vận hành</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-people text-info me-2"></i> Danh sách khách</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/expenses/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-coin text-success me-2"></i> Chi phí & Lãi</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $s['MaLichKhoiHanh']; ?>"><i class="bi bi-pencil text-warning me-2"></i> Chỉnh sửa</a></li>
                                                    <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/schedule/delete/<?php echo $s['MaLichKhoiHanh']; ?>" onclick="return confirm('Xóa lịch này sẽ ảnh hưởng đến các đơn hàng liên quan?')"><i class="bi bi-trash me-2"></i> Xóa lịch</a></li>
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