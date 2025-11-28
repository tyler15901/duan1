<h2 class="mb-3">Danh sách Tour du lịch</h2>

<div class="card mb-4 bg-light">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Tìm tên tour..."
                    value="<?php echo htmlspecialchars($pagination['keyword']); ?>">
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($pagination['cat_id'] == $cat['MaLoaiTour']) ? 'selected' : ''; ?>>
                            <?php echo $cat['TenLoai']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Tìm</button>
            </div>
            <div class="col-md-3 text-end">
                <a href="<?php echo BASE_URL; ?>/tour/create" class="btn btn-success"><i class="bi bi-plus-lg"></i> Thêm
                    Tour</a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tên Tour</th>
            <th>Loại</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tours)): ?>
            <tr>
                <td colspan="7" class="text-center">Không tìm thấy tour nào!</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?php echo $tour['MaTour']; ?></td>
                    <td>
                        <?php if ($tour['HinhAnh']): ?>
                            <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" width="60"
                                class="rounded">
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold"><?php echo $tour['TenTour']; ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo $tour['TenLoai']; ?></span></td>
                    <td><?php echo $tour['SoNgay']; ?> ngày</td>
                    <td>
                        <?php if ($tour['TrangThai'] == 'Hoạt động'): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tạm dừng</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/schedule/index?tour_id=<?php echo $tour['MaTour']; ?>"
                            class="btn btn-sm btn-primary" title="Xem các lịch khởi hành của tour này">
                            <i class="bi bi-calendar-event"></i> Lịch
                        </a>

                        <a href="<?php echo BASE_URL; ?>/tour/show/<?php echo $tour['MaTour']; ?>"
                            class="btn btn-sm btn-info text-white">Xem</a>
                        <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>"
                            class="btn btn-sm btn-warning">Sửa</a>
                        <a href="<?php echo BASE_URL; ?>/tour/delete/<?php echo $tour['MaTour']; ?>"
                            class="btn btn-sm btn-danger" onclick="return confirm('Xóa tour này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($pagination['total_pages'] > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link"
                    href="?page=<?php echo $pagination['current_page'] - 1; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">Trước</a>
            </li>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li
                class="page-item <?php echo ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
                <a class="page-link"
                    href="?page=<?php echo $pagination['current_page'] + 1; ?>&cat=<?php echo $pagination['cat_id']; ?>&q=<?php echo $pagination['keyword']; ?>">Sau</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>