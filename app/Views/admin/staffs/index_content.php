<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold border-start border-4 border-primary ps-3 mb-0">Đội ngũ Hướng Dẫn Viên</h2>
        <p class="text-muted small ps-3 mb-0 mt-1">Quản lý hồ sơ và tài khoản truy cập của nhân sự.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/staff/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill"></i> Thêm HDV Mới
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0"
                        placeholder="Tìm theo tên, SĐT hoặc Email...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select text-secondary">
                    <option value="">-- Lọc theo loại hình --</option>
                    <option value="Tour trong nước">Tour trong nước</option>
                    <option value="Tour quốc tế">Tour quốc tế</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-dark w-100">Tìm kiếm</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr class="text-uppercase small fw-bold">
                        <th class="ps-4">Hồ sơ nhân sự</th>
                        <th>Liên hệ</th>
                        <th>Phân loại</th>
                        <th>Trạng thái TK</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guides)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 opacity-50"></i>
                                <p class="mt-2">Chưa có hướng dẫn viên nào.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($guides as $g): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <?php if ($g['AnhDaiDien']): ?>
                                                <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $g['AnhDaiDien']; ?>"
                                                    class="rounded-circle border"
                                                    style="width: 48px; height: 48px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted border"
                                                    style="width: 48px; height: 48px; font-size: 20px;">
                                                    <?php echo strtoupper(substr($g['HoTen'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>

                                            <span
                                                class="position-absolute bottom-0 end-0 p-1 bg-<?php echo ($g['TrangThaiTK'] == 'Hoạt động') ? 'success' : 'danger'; ?> border border-white rounded-circle"></span>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold text-dark"><?php echo $g['HoTen']; ?></div>
                                            <div class="small text-muted">ID: #<?php echo $g['MaNhanSu']; ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="mb-1"><i class="bi bi-telephone text-primary w-20"></i>
                                            <?php echo $g['SoDienThoai']; ?></span>
                                        <span class="text-muted"><i class="bi bi-envelope w-20"></i>
                                            <?php echo $g['Email']; ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?php
                                    $badgeColor = 'info text-dark'; // Mặc định
                                    if ($g['PhanLoai'] == 'Tour quốc tế')
                                        $badgeColor = 'primary';
                                    elseif ($g['PhanLoai'] == 'Tour theo yêu cầu')
                                        $badgeColor = 'warning text-dark';
                                    ?>
                                    <span
                                        class="badge bg-<?php echo $badgeColor; ?> bg-opacity-10 border border-<?php echo $badgeColor; ?> text-<?php echo str_replace(' text-dark', '', $badgeColor); ?>">
                                        <?php echo $g['PhanLoai']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($g['TrangThaiTK'] == 'Hoạt động'): ?>
                                        <div class="d-flex align-items-center text-success small fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> <?php echo $g['TenDangNhap']; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center text-danger small fw-bold">
                                            <i class="bi bi-lock-fill me-1"></i> Đã khóa
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="<?php echo BASE_URL; ?>/staff/schedule?id=<?php echo $g['MaNhanSu']; ?>"
                                        class="btn btn-sm btn-info text-white" title="Xem lịch làm việc">
                                        <i class="bi bi-calendar-range"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>/staff/edit/<?php echo $g['MaNhanSu']; ?>"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    <a href="<?php echo BASE_URL; ?>/staff/delete/<?php echo $g['MaNhanSu']; ?>" ...>Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <?php include '../app/Views/layouts/pagination.php'; ?>
        </div>
    </div>
</div>