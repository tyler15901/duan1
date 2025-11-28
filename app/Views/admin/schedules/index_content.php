<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Danh sách Lịch Khởi Hành</h2>
    <a href="<?php echo BASE_URL; ?>/schedule/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tạo Lịch Mới
    </a>
</div>

<div class="card mb-4 bg-light">
    <div class="card-body p-3">
        <form class="row g-2">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Tìm theo mã lịch hoặc tên tour...">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary w-100">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã Lịch</th>
                <th>Tên Tour</th>
                <th>Khởi hành - Kết thúc</th>
                <th>Điểm đón</th>
                <th>Số khách</th>
                <th>Trạng thái</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($schedules)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Chưa có lịch khởi hành nào được tạo. <br>
                        <a href="<?php echo BASE_URL; ?>/schedule/create">Tạo ngay</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($schedules as $item): ?>
                    <tr>
                        <td class="fw-bold text-primary">
                            <?php echo $item['LichCode']; ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?php echo $item['TenTour']; ?></div>
                            <small class="text-muted"><?php echo $item['SoNgay']; ?> ngày</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span><i class="bi bi-calendar-check"></i>
                                    <?php echo date('d/m/Y', strtotime($item['NgayKhoiHanh'])); ?></span>
                                <small class="text-muted">đến
                                    <?php echo date('d/m/Y', strtotime($item['NgayKetThuc'])); ?></small>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo date('H:i', strtotime($item['GioTapTrung'])); ?></strong> <br>
                            <?php echo $item['DiaDiemTapTrung']; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark" style="font-size: 0.9rem">
                                <?php echo $item['SoKhachHienTai']; ?> khách
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusColor = 'secondary';
                            if ($item['TrangThai'] == 'Nhận khách')
                                $statusColor = 'success';
                            if ($item['TrangThai'] == 'Đã đóng')
                                $statusColor = 'danger';
                            if ($item['TrangThai'] == 'Đang chạy')
                                $statusColor = 'warning';
                            ?>
                            <span class="badge bg-<?php echo $statusColor; ?>">
                                <?php echo $item['TrangThai']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="<?php echo BASE_URL; ?>/schedule/show/<?php echo $item['MaLichKhoiHanh']; ?>"
                                    class="btn btn-sm btn-outline-info" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $item['MaLichKhoiHanh']; ?>"
                                    class="btn btn-sm btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <a href="<?php echo BASE_URL; ?>/schedule/delete/<?php echo $item['MaLichKhoiHanh']; ?>"
                                    class="btn btn-sm btn-outline-danger" title="Xóa"
                                    onclick="return confirm('Bạn có chắc muốn xóa lịch này?')"><i class="bi bi-trash"></i></a>
                            </div>  
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>