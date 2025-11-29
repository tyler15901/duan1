<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold border-start border-4 border-primary ps-3 mb-0">Quản lý Lịch Khởi Hành</h2>
        <p class="text-muted small ps-3 mb-0 mt-1">Theo dõi và điều phối các chuyến đi sắp tới.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tạo Lịch Mới
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 bg-light" placeholder="Tìm theo Mã lịch, Tên tour...">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control bg-light text-secondary" title="Lọc theo ngày đi">
            </div>
            <div class="col-md-2">
                <select class="form-select bg-light">
                    <option value="">-- Trạng thái --</option>
                    <option value="Nhận khách">Đang nhận khách</option>
                    <option value="Đang chạy">Đang chạy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-dark w-100"><i class="bi bi-funnel"></i> Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr class="text-uppercase small fw-bold">
                        <th class="ps-4">Mã Lịch</th>
                        <th>Thông tin Tour</th>
                        <th>Thời gian</th>
                        <th>Điểm tập trung</th>
                        <th class="text-center">Số khách</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="64" class="mb-3 opacity-50">
                                <p>Chưa có lịch khởi hành nào.</p>
                                <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-sm btn-outline-primary">Tạo ngay</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $item['MaLichKhoiHanh']; ?>" class="fw-bold text-decoration-none text-primary">
                                        <?php echo $item['LichCode']; ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                        <?php echo $item['TenTour']; ?>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-clock"></i> <?php echo $item['SoNgay']; ?> ngày</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="text-center border rounded px-2 py-1 bg-light me-2">
                                            <div class="small fw-bold text-uppercase text-danger"><?php echo date('M', strtotime($item['NgayKhoiHanh'])); ?></div>
                                            <div class="h5 mb-0 fw-bold"><?php echo date('d', strtotime($item['NgayKhoiHanh'])); ?></div>
                                        </div>
                                        <div class="small text-muted">
                                            Đến: <?php echo date('d/m', strtotime($item['NgayKetThuc'])); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-geo-alt text-danger me-1 mt-1"></i>
                                        <div>
                                            <div class="fw-bold"><?php echo date('H:i', strtotime($item['GioTapTrung'])); ?></div>
                                            <div class="small text-muted text-truncate" style="max-width: 150px;"><?php echo $item['DiaDiemTapTrung']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark bg-opacity-10 border border-info px-3 py-2 rounded-pill">
                                        <?php echo $item['SoKhachHienTai']; ?> / <?php echo $item['SoChoToiDa'] ?? '??'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($item['TrangThai']) {
                                        'Nhận khách' => 'success',
                                        'Đã đóng' => 'secondary',
                                        'Đang chạy' => 'warning text-dark',
                                        default => 'light text-dark border'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?> bg-opacity-75">
                                        <?php echo $item['TrangThai']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $item['MaLichKhoiHanh']; ?>"><i class="bi bi-eye text-info me-2"></i> Chi tiết</a></li>
                                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/guests/<?php echo $item['MaLichKhoiHanh']; ?>"><i class="bi bi-people text-primary me-2"></i> Danh sách khách</a></li>
                                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/expenses/<?php echo $item['MaLichKhoiHanh']; ?>"><i class="bi bi-cash-coin text-success me-2"></i> Thu chi & Lãi</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $item['MaLichKhoiHanh']; ?>"><i class="bi bi-pencil text-warning me-2"></i> Sửa lịch</a></li>
                                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/schedule/delete/<?php echo $item['MaLichKhoiHanh']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?')"><i class="bi bi-trash me-2"></i> Xóa</a></li>
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
</div>