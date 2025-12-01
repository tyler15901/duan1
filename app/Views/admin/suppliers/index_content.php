<div class="pagetitle">
    <h1>Quản lý Đối tác & NCC</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Nhà cung cấp</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h5 class="card-title p-0 m-0">Mạng lưới Đối tác</h5>
                        <a href="<?php echo BASE_URL; ?>/supplier/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Đối tác
                        </a>
                    </div>

                    <form class="row g-3 mb-4 bg-light p-3 rounded mx-1" method="GET">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 ps-0" 
                                    placeholder="Tìm tên nhà xe, khách sạn..." 
                                    value="<?php echo htmlspecialchars($pagination['keyword'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">-- Tất cả dịch vụ --</option>
                                <option value="Vận chuyển">🚌 Vận chuyển</option>
                                <option value="Lưu trú">🏨 Lưu trú</option>
                                <option value="Ăn uống">🍽️ Ăn uống</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">-- Trạng thái --</option>
                                <option value="Hoạt động">Đang hợp tác</option>
                                <option value="Ngừng hợp tác">Dừng hợp tác</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Tìm kiếm</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">Thông tin Đối tác</th>
                                    <th scope="col">Loại hình</th>
                                    <th scope="col">Liên hệ</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($suppliers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-shop fs-1 opacity-25"></i>
                                            <p class="mt-2">Chưa có nhà cung cấp nào.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($suppliers as $s): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded p-2 me-3 bg-light text-secondary border d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                    <?php if($s['LoaiCungCap'] == 'Vận chuyển'): ?>
                                                        <i class="bi bi-bus-front-fill fs-4 text-warning"></i>
                                                    <?php elseif($s['LoaiCungCap'] == 'Lưu trú'): ?>
                                                        <i class="bi bi-building-fill fs-4 text-info"></i>
                                                    <?php elseif($s['LoaiCungCap'] == 'Ăn uống'): ?>
                                                        <i class="bi bi-cup-hot-fill fs-4 text-danger"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-box-seam-fill fs-4 text-success"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo $s['TenNhaCungCap']; ?></div>
                                                    <div class="small text-muted">ID: #<?php echo $s['MaNhaCungCap']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo $s['LoaiCungCap']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="mb-1"><i class="bi bi-telephone text-primary me-2"></i> <?php echo $s['SoDienThoai']; ?></div>
                                                <div class="text-muted text-truncate" style="max-width: 200px;" title="<?php echo $s['DiaChi']; ?>">
                                                    <i class="bi bi-geo-alt me-2"></i> <?php echo $s['DiaChi']; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($s['TrangThai'] == 'Hoạt động'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i> Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo BASE_URL; ?>/supplier/show/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-light text-primary" title="Quản lý Tài nguyên">
                                                    <i class="bi bi-list-check"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-light text-warning" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/supplier/delete/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');" title="Xóa">
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