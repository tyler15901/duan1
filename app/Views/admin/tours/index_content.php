<div class="pagetitle">
    <h1>Quản lý Tour</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Danh sách Tour</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h5 class="card-title p-0 m-0">Dữ liệu Tour</h5>
                        <a href="<?php echo BASE_URL; ?>/tour/create" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Tour Mới
                        </a>
                    </div>

                    <form action="" method="GET" class="row g-3 mb-4 p-3 bg-light rounded-2 mx-1">
                        <div class="col-md-5">
                            <input type="text" name="q" class="form-control" placeholder="Nhập tên tour hoặc mã tour..." value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="cat" class="form-select">
                                <option value="0">-- Tất cả danh mục --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo (isset($pagination['cat_id']) && $pagination['cat_id'] == $cat['MaLoaiTour']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['TenLoai']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">-- Trạng thái --</option>
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Tạm dừng">Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Lọc</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Hình ảnh</th>
                                    <th scope="col">Tên Tour / Mã</th>
                                    <th scope="col">Danh mục</th>
                                    <th scope="col">Thời lượng</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tours)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Không tìm thấy dữ liệu phù hợp.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/tour/show/<?php echo $tour['MaTour']; ?>">
                                                <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh'] ? $tour['HinhAnh'] : 'default.jpg'; ?>" 
                                                     class="rounded" width="60" height="45" style="object-fit: cover;">
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary"><?php echo $tour['TenTour']; ?></div>
                                            <small class="text-muted">#<?php echo $tour['MaTour']; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo $tour['TenLoai']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $tour['SoNgay']; ?> ngày</td>
                                        <td>
                                            <?php if($tour['TrangThai'] == 'Hoạt động'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i> Locked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo BASE_URL; ?>/tour/show/<?php echo $tour['MaTour']; ?>" class="btn btn-sm btn-light text-primary" title="Chi tiết"><i class="bi bi-eye"></i></a>
                                                <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>" class="btn btn-sm btn-light text-info" title="Lịch khởi hành"><i class="bi bi-calendar-week"></i></a>
                                                <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" class="btn btn-sm btn-light text-warning" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                                <a href="<?php echo BASE_URL; ?>/tour/delete/<?php echo $tour['MaTour']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');" title="Xóa"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php include '../app/Views/layouts/pagination.php'; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>