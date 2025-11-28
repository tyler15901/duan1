<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Quản lý Chi phí & Lợi nhuận</h2>
        <small class="text-muted">Tour: <?php echo $schedule['TenTour']; ?> (<?php echo $schedule['LichCode']; ?>)</small>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-secondary">Quay lại</a>
</div>

<div class="row mb-4 text-center">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h3><?php echo number_format($total_revenue); ?> đ</h3>
                <span>TỔNG DOANH THU</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h3><?php echo number_format($total_expense); ?> đ</h3>
                <span>TỔNG CHI PHÍ</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card <?php echo ($profit >= 0) ? 'bg-primary' : 'bg-warning text-dark'; ?> text-white">
            <div class="card-body">
                <h3><?php echo number_format($profit); ?> đ</h3>
                <span>LỢI NHUẬN THỰC TẾ</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-dark fw-bold">Thêm khoản chi mới</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/schedule/store_expense/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
                    <div class="mb-3">
                        <label class="fw-bold">Loại chi phí</label>
                        <select name="loai_chi_phi" class="form-select">
                            <option value="Thuê xe">Thuê xe</option>
                            <option value="Khách sạn">Khách sạn</option>
                            <option value="Ăn uống">Ăn uống</option>
                            <option value="Vé tham quan">Vé tham quan</option>
                            <option value="Hướng dẫn viên">Lương HDV</option>
                            <option value="Khác">Khác (Nước, quà...)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Số tiền (VNĐ)</label>
                        <input type="number" name="so_tien" class="form-control" required placeholder="VD: 5000000">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Ghi chú chi tiết</label>
                        <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Lưu chi phí</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Danh sách các khoản đã chi</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Loại</th>
                            <th>Diễn giải</th>
                            <th>Số tiền</th>
                            <th>Ngày nhập</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($expenses)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có khoản chi nào.</td></tr>
                        <?php else: ?>
                            <?php foreach($expenses as $e): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $e['LoaiChiPhi']; ?></td>
                                <td><?php echo $e['GhiChu']; ?></td>
                                <td class="text-danger fw-bold"><?php echo number_format($e['SoTien']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($e['NgayChi'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/schedule/delete_expense/<?php echo $e['MaChiPhi']; ?>/<?php echo $schedule['MaLichKhoiHanh']; ?>" 
                                       class="text-danger" onclick="return confirm('Xóa khoản chi này?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>