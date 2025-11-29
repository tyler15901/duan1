<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold border-start border-4 border-primary ps-3 mb-0">Danh sách Tour</h2>
        <p class="text-muted small ps-3 mb-0 mt-1">Quản lý các gói tour và thông tin hành trình.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/tour/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tạo Tour Mới
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0" 
                           placeholder="Nhập tên tour cần tìm..." 
                           value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($pagination['cat_id'] == $cat['MaLoaiTour']) ? 'selected' : ''; ?>>
                            <?php echo $cat['TenLoai']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Tìm kiếm</button>
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
                        <th class="ps-4">Tour</th>
                        <th>Phân loại</th>
                        <th>Thời lượng</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tours)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="64" class="mb-3 opacity-50">
                                <p>Chưa có dữ liệu tour nào.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tours as $tour): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 position-relative">
                                            <?php if ($tour['HinhAnh']): ?>
                                                <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" 
                                                     class="rounded border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="<?php echo $tour['TenTour']; ?>">
                                                <?php echo $tour['TenTour']; ?>
                                            </div>
                                            <div class="small text-muted">ID: #<?php echo $tour['MaTour']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill">
                                        <?php echo $tour['TenLoai']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold"><i class="bi bi-clock"></i> <?php echo $tour['SoNgay']; ?> ngày</div>
                                    <div class="small text-muted">Max: <?php echo $tour['SoChoToiDa']; ?> khách</div>
                                </td>
                                <td>
                                    <?php if ($tour['TrangThai'] == 'Hoạt động'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i> Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-pause-circle"></i> Tạm dừng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>" 
                                           class="btn btn-sm btn-light border" title="Xem lịch khởi hành">
                                            <i class="bi bi-calendar-event text-primary"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/tour/show/<?php echo $tour['MaTour']; ?>" 
                                           class="btn btn-sm btn-light border" title="Chi tiết">
                                            <i class="bi bi-eye text-info"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" 
                                           class="btn btn-sm btn-light border" title="Chỉnh sửa">
                                            <i class="bi bi-pencil text-warning"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/tour/delete/<?php echo $tour['MaTour']; ?>" 
                                           class="btn btn-sm btn-light border text-danger" 
                                           onclick="return confirm('Cảnh báo: Xóa tour này sẽ xóa cả lịch trình liên quan!\nBạn có chắc chắn không?')" title="Xóa">
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
    <div class="card-footer bg-white py-3 border-0">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?php echo ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link border-0" href="?page=<?php echo $pagination['current_page'] - 1; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                        <a class="page-link border-0 rounded-circle mx-1" href="?page=<?php echo $i; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
                    <a class="page-link border-0" href="?page=<?php echo $pagination['current_page'] + 1; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>