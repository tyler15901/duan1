<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Hạch toán Chi phí</h4>
        <h3 class="text-primary fw-bold"><?php echo $schedule['LichCode']; ?></h3>
    </div>
    <a href="<?php echo BASE_URL; ?>/schedule/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-success bg-opacity-10 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-success fw-bold text-uppercase small">Tổng Doanh Thu</span>
                    <i class="bi bi-graph-up-arrow text-success fs-4"></i>
                </div>
                <h2 class="text-success fw-bold mb-0"><?php echo number_format($total_revenue); ?> <span class="fs-6">đ</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-danger bg-opacity-10 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-danger fw-bold text-uppercase small">Tổng Chi Phí</span>
                    <i class="bi bi-cart-x text-danger fs-4"></i>
                </div>
                <h2 class="text-danger fw-bold mb-0"><?php echo number_format($total_expense); ?> <span class="fs-6">đ</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-white h-100 <?php echo ($profit >= 0) ? 'bg-primary' : 'bg-warning'; ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-uppercase small text-white-50">Lợi Nhuận Ròng</span>
                    <i class="bi bi-cash-stack text-white fs-4"></i>
                </div>
                <h2 class="fw-bold mb-0"><?php echo number_format($profit); ?> <span class="fs-6">đ</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle"></i> Thêm khoản chi</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/schedule/store_expense/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Loại chi phí</label>
                        <select name="loai_chi_phi" class="form-select">
                            <option value="Thuê xe">🚌 Thuê xe</option>
                            <option value="Khách sạn">🏨 Khách sạn</option>
                            <option value="Ăn uống">🍽️ Ăn uống</option>
                            <option value="Vé tham quan">🎫 Vé tham quan</option>
                            <option value="Hướng dẫn viên">👤 Lương HDV</option>
                            <option value="Khác">📦 Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Số tiền (VNĐ)</label>
                        <input type="number" name="so_tien" class="form-control" required placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Ghi chú / Diễn giải</label>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Chi tiết..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 fw-bold">Lưu lại</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Nhật ký chi tiêu</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Hạng mục</th>
                            <th>Diễn giải</th>
                            <th class="text-end">Số tiền</th>
                            <th class="text-center">Ngày</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($expenses)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa phát sinh chi phí nào.</td></tr>
                        <?php else: ?>
                            <?php foreach($expenses as $e): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?php echo $e['LoaiChiPhi']; ?></td>
                                <td class="small text-muted"><?php echo $e['GhiChu']; ?></td>
                                <td class="text-end fw-bold text-danger">
                                    -<?php echo number_format($e['SoTien']); ?>
                                </td>
                                <td class="text-center small text-muted"><?php echo date('d/m', strtotime($e['NgayChi'])); ?></td>
                                <td class="text-end pe-3">
                                    <a href="<?php echo BASE_URL; ?>/schedule/delete_expense/<?php echo $e['MaChiPhi']; ?>/<?php echo $schedule['MaLichKhoiHanh']; ?>" 
                                       class="btn btn-sm btn-light text-danger border-0" 
                                       onclick="return confirm('Xóa khoản này?')" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </a>
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