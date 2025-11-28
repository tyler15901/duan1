<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Chi tiết Lịch Khởi Hành: <span class="text-primary"><?php echo $schedule['LichCode']; ?></span></h2>
    <div>
        <a href="<?php echo BASE_URL; ?>/schedule/edit/<?php echo $schedule['MaLichKhoiHanh']; ?>" class="btn btn-warning">Sửa Lịch</a>
        <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary">Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white fw-bold">Thông tin chung</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="150" class="fw-bold">Tour:</td>
                        <td><?php echo $schedule['TenTour']; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Ngày đi:</td>
                        <td><?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Ngày về:</td>
                        <td><?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tập trung:</td>
                        <td><?php echo $schedule['GioTapTrung']; ?> tại <?php echo $schedule['DiaDiemTapTrung']; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Số khách hiện tại:</td>
                        <td><span class="badge bg-primary fs-6"><?php echo $schedule['SoKhachHienTai']; ?></span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Trạng thái:</td>
                        <td><span class="badge bg-success"><?php echo $schedule['TrangThai']; ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-success text-white fw-bold">Nhân sự phụ trách</div>
            <ul class="list-group list-group-flush">
                <?php if(empty($staffs)): ?>
                    <li class="list-group-item text-muted">Chưa phân bổ hướng dẫn viên</li>
                <?php else: ?>
                    <?php foreach($staffs as $s): ?>
                        <li class="list-group-item">
                            <i class="bi bi-person-badge"></i> <?php echo $s['HoTen']; ?> - <small><?php echo $s['SoDienThoai']; ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-dark fw-bold">Tài nguyên (Xe, KS)</div>
            <ul class="list-group list-group-flush">
                <?php if(empty($resources)): ?>
                    <li class="list-group-item text-muted">Chưa phân bổ xe/khách sạn</li>
                <?php else: ?>
                    <?php foreach($resources as $r): ?>
                        <li class="list-group-item">
                            <strong><?php echo $r['TenTaiNguyen']; ?></strong> <br>
                            <small class="text-muted">NCC: <?php echo $r['TenNhaCungCap']; ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>