<div class="pagetitle">
    <h1>Hạch toán Chi phí</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/schedule/index">Lịch trình</a></li>
            <li class="breadcrumb-item active"><?php echo $schedule['LichCode']; ?></li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-md-4">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Tổng Doanh Thu <span>| Booking</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo number_format($total_revenue); ?></h6>
                            <span class="text-success small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card info-card customers-card"> 
                <style>
                    .expense-card .card-icon { background: #ffe0e3; color: #dc3545; }
                </style>
                <div class="card-body expense-card">
                    <h5 class="card-title">Tổng Chi Phí <span>| Vận hành</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background: #ffe0e3; color: #dc3545;">
                            <i class="bi bi-cart-dash"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo number_format($total_expense); ?></h6>
                            <span class="text-danger small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Lợi Nhuận Ròng <span>| Tạm tính</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <div class="ps-3">
                            <h6 class="<?php echo ($profit >= 0) ? 'text-primary' : 'text-danger'; ?>">
                                <?php echo number_format($profit); ?>
                            </h6>
                            <span class="text-muted small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thêm khoản chi</h5>
                    <form action="<?php echo BASE_URL; ?>/schedule/store_expense/<?php echo $schedule['MaLichKhoiHanh']; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Loại chi phí</label>
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
                            <label class="form-label">Số tiền (VNĐ)</label>
                            <input type="number" name="so_tien" class="form-control" required placeholder="Nhập số tiền...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diễn giải</label>
                            <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Lưu Chi Phí</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nhật ký chi tiêu</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hạng mục</th>
                                    <th>Diễn giải</th>
                                    <th class="text-end">Số tiền</th>
                                    <th class="text-center">Ngày</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($expenses)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
                                <?php else: ?>
                                    <?php foreach($expenses as $e): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?php echo $e['LoaiChiPhi']; ?></span></td>
                                        <td class="small text-muted"><?php echo $e['GhiChu']; ?></td>
                                        <td class="text-end fw-bold text-danger">-<?php echo number_format($e['SoTien']); ?></td>
                                        <td class="text-center small"><?php echo date('d/m', strtotime($e['NgayChi'])); ?></td>
                                        <td class="text-end">
                                            <a href="<?php echo BASE_URL; ?>/schedule/delete_expense/<?php echo $e['MaChiPhi']; ?>/<?php echo $schedule['MaLichKhoiHanh']; ?>" 
                                               class="text-danger" onclick="return confirm('Xóa khoản này?')"><i class="bi bi-trash"></i></a>
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
    </div>
</section>