<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold mb-0">Sản phẩm Tour</h2>
        <p class="text-muted small mb-0">Quản lý các gói tour và thông tin chi tiết.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/tour/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Thêm Tour Mới
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Tìm tên tour..." value="<?php echo htmlspecialchars($pagination['keyword']); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($pagination['cat_id'] == $cat['MaLoaiTour']) ? 'selected' : ''; ?>>
                            <?php echo $cat['TenLoai']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Trạng thái --</option>
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Tạm dừng">Tạm dừng</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button type="submit" class="btn btn-light text-primary fw-bold w-100">Lọc dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Thông tin Tour</th>
                        <th>Danh mục</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tours)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Không tìm thấy dữ liệu tour.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh'] ? $tour['HinhAnh'] : 'default.jpg'; ?>" 
                                         class="rounded-3 shadow-sm me-3" width="60" height="60" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo $tour['TenTour']; ?></div>
                                        <small class="text-muted">ID: #<?php echo $tour['MaTour']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <?php echo $tour['TenLoai']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock me-1"></i> <?php echo $tour['SoNgay']; ?> ngày
                                </div>
                            </td>
                            <td>
                                <?php if($tour['TrangThai'] == 'Hoạt động'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">
                                        <i class="bi bi-pause-circle-fill me-1"></i> Tạm dừng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>" 
                                       class="btn btn-sm btn-light text-primary" title="Xem lịch khởi hành">
                                       <i class="bi bi-calendar-week"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" 
                                       class="btn btn-sm btn-light text-warning" title="Sửa">
                                       <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/tour/delete/<?php echo $tour['MaTour']; ?>" 
                                       class="btn btn-sm btn-light text-danger" 
                                       onclick="return confirm('Bạn có chắc muốn xóa tour này?')" title="Xóa">
                                       <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white border-0 py-3">
            <?php include '../app/Views/layouts/pagination.php'; ?>
        </div>
    <?php endif; ?>
</div>