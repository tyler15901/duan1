<div class="pagetitle">
    <h1>Quản lý Hướng Dẫn Viên</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Nhân sự</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h5 class="card-title p-0 m-0">Đội ngũ Nhân sự</h5>
                        <a href="<?php echo BASE_URL; ?>/staff/create" class="btn btn-primary">
                            <i class="bi bi-person-plus-fill me-1"></i> Thêm HDV Mới
                        </a>
                    </div>

                    <form class="row g-3 mb-4 bg-light p-3 rounded mx-1" method="GET">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 ps-0" 
                                    placeholder="Tìm theo tên, SĐT hoặc Email..." 
                                    value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">-- Tất cả loại hình --</option>
                                <option value="Tour trong nước">Tour trong nước</option>
                                <option value="Tour quốc tế">Tour quốc tế</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-secondary w-100">Tìm kiếm</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">Hồ sơ nhân sự</th>
                                    <th scope="col">Liên hệ</th>
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">Tài khoản</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($guides)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-1 opacity-25"></i>
                                            <p class="mt-2">Chưa có hướng dẫn viên nào.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($guides as $g): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="position-relative">
                                                    <?php 
                                                        $avatarUrl = $g['AnhDaiDien'] ? BASE_URL . '/assets/uploads/' . $g['AnhDaiDien'] : 'https://ui-avatars.com/api/?name=' . $g['HoTen'];
                                                    ?>
                                                    <img src="<?php echo $avatarUrl; ?>" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                                                    <span class="position-absolute bottom-0 end-0 p-1 bg-<?php echo ($g['TrangThaiTK'] == 'Hoạt động') ? 'success' : 'danger'; ?> border border-white rounded-circle"></span>
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-bold text-dark"><?php echo $g['HoTen']; ?></div>
                                                    <div class="small text-muted">ID: #<?php echo $g['MaNhanSu']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="mb-1"><i class="bi bi-telephone text-primary me-2"></i> <?php echo $g['SoDienThoai']; ?></div>
                                                <div class="text-muted"><i class="bi bi-envelope me-2"></i> <?php echo $g['Email']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match ($g['PhanLoai']) {
                                                'Tour quốc tế' => 'bg-primary',
                                                'Tour theo yêu cầu' => 'bg-warning text-dark',
                                                default => 'bg-info text-dark bg-opacity-25 border border-info'
                                            };
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo $g['PhanLoai']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($g['TrangThaiTK'] == 'Hoạt động'): ?>
                                                <div class="small text-success fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i> <?php echo $g['TenDangNhap']; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="small text-danger fw-bold">
                                                    <i class="bi bi-lock-fill me-1"></i> Đã khóa
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo BASE_URL; ?>/staff/schedule?id=<?php echo $g['MaNhanSu']; ?>" class="btn btn-sm btn-light text-info" title="Xem lịch làm việc">
                                                    <i class="bi bi-calendar-week"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/staff/edit/<?php echo $g['MaNhanSu']; ?>" class="btn btn-sm btn-light text-warning" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/staff/delete/<?php echo $g['MaNhanSu']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');" title="Xóa">
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

                    <?php if (isset($pagination)): ?>
                        <div class="mt-4 d-flex justify-content-center">
                            <?php include '../app/Views/layouts/pagination.php'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>