<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Lịch làm việc</h2>
        <div class="text-muted mt-1">
            Nhân sự: <strong class="text-primary"><?php echo $guide['HoTen']; ?></strong> 
            (<?php echo $guide['PhanLoai']; ?>)
        </div>
    </div>
    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="card mb-4 border-0 shadow-sm bg-light">
    <div class="card-body d-flex align-items-center">
        <img src="<?php echo !empty($guide['AnhDaiDien']) ? BASE_URL.'/assets/uploads/'.$guide['AnhDaiDien'] : 'https://ui-avatars.com/api/?name='.$guide['HoTen']; ?>" 
             class="rounded-circle me-3 border border-2 border-white shadow-sm" width="60" height="60">
        <div>
            <div class="d-flex gap-3 text-small text-muted">
                <span><i class="bi bi-telephone"></i> <?php echo $guide['SoDienThoai']; ?></span>
                <span><i class="bi bi-envelope"></i> <?php echo $guide['Email']; ?></span>
            </div>
            <div class="mt-1">
                <span class="badge bg-primary">Tổng số tour: <?php echo count($schedules); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-calendar-week"></i> Danh sách tour được phân công
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã Lịch</th>
                    <th>Tên Tour</th>
                    <th>Thời gian</th>
                    <th>Địa điểm & Ghi chú</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($schedules)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="64" class="opacity-25 mb-2">
                            <p class="text-muted mb-0">Nhân viên này chưa có lịch trình nào.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($schedules as $s): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?php echo $s['LichCode']; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $s['TenTour']; ?></div>
                            <small class="text-muted"><?php echo $s['SoNgay']; ?> ngày</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-success"><i class="bi bi-box-arrow-right"></i> <?php echo date('d/m/Y', strtotime($s['NgayKhoiHanh'])); ?></span>
                                <span class="text-danger"><i class="bi bi-box-arrow-in-left"></i> <?php echo date('d/m/Y', strtotime($s['NgayKetThuc'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <small class="d-block text-truncate" style="max-width: 250px;">
                                <i class="bi bi-geo-alt"></i> <?php echo $s['DiaDiemTapTrung']; ?>
                            </small>
                        </td>
                        <td>
                            <?php 
                                $badge = 'secondary';
                                if($s['TrangThai'] == 'Nhận khách') $badge = 'success';
                                if($s['TrangThai'] == 'Đang chạy') $badge = 'warning text-dark';
                                if($s['TrangThai'] == 'Hoàn tất') $badge = 'primary';
                            ?>
                            <span class="badge bg-<?php echo $badge; ?>"><?php echo $s['TrangThai']; ?></span>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $s['MaLichKhoiHanh']; ?>" class="btn btn-sm btn-outline-info">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>