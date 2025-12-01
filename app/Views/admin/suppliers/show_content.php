<div class="pagetitle">
    <h1>Chi tiết Nhà cung cấp</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/supplier/index">Nhà cung cấp</a></li>
            <li class="breadcrumb-item active"><?php echo $supplier['TenNhaCungCap']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <div class="mb-3">
                        <?php if ($supplier['LoaiCungCap'] == 'Vận chuyển'): ?>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-bus-front-fill fs-1"></i>
                            </div>
                        <?php elseif ($supplier['LoaiCungCap'] == 'Lưu trú'): ?>
                            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-building-fill fs-1"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-shop fs-1"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h4 class="fw-bold text-center mb-1"><?php echo $supplier['TenNhaCungCap']; ?></h4>
                    <span class="badge bg-light text-dark border mb-4"><?php echo $supplier['LoaiCungCap']; ?></span>

                    <div class="w-100">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Liên hệ:</span>
                            <span class="fw-bold text-dark"><?php echo $supplier['SoDienThoai']; ?></span>
                        </div>
                        <div class="py-2 border-bottom">
                            <span class="text-muted small d-block mb-1">Địa chỉ:</span>
                            <span class="text-dark small"><i class="bi bi-geo-alt me-1"></i> <?php echo $supplier['DiaChi']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 align-items-center">
                            <span class="text-muted small">Trạng thái:</span>
                            <?php if ($supplier['TrangThai'] == 'Hoạt động'): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Dừng hợp tác</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2 w-100 mt-4">
                        <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $supplier['MaNhaCungCap']; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                        </a>
                        <a href="<?php echo BASE_URL; ?>/supplier/delete/<?php echo $supplier['MaNhaCungCap']; ?>" class="btn btn-outline-danger" onclick="return confirm('Xóa NCC này sẽ xóa toàn bộ xe/phòng liên quan?')">
                            <i class="bi bi-trash me-1"></i> Xóa đối tác
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center">
                        <span>Danh mục Tài sản <span class="badge bg-secondary rounded-pill ms-2"><?php echo count($resources); ?></span></span>
                    </h5>

                    <div class="bg-light p-3 rounded mb-4 border">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Thêm tài sản mới (Xe / Phòng)</h6>
                        <form action="<?php echo BASE_URL; ?>/supplier/store_resource/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST" class="row g-2">
                            <div class="col-md-5">
                                <input type="text" name="ten_tai_nguyen" class="form-control form-control-sm" required placeholder="Tên/Biển số (VD: 29B-12345, Phòng VIP)">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="so_luong_cho" class="form-control form-control-sm" placeholder="Sức chứa (Chỗ)">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="ghi_chu" class="form-control form-control-sm" placeholder="Ghi chú (Màu xe, View...)">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success btn-sm w-100" title="Thêm">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Tên Tài sản / Biển số</th>
                                    <th scope="col" class="text-center">Sức chứa</th>
                                    <th scope="col">Mô tả thêm</th>
                                    <th scope="col" class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($resources)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-4 d-block opacity-50 mb-2"></i>
                                            Chưa có tài sản nào được khai báo.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($resources as $res): ?>
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <i class="bi bi-record-circle text-primary small me-2"></i>
                                                <?php echo $res['TenTaiNguyen']; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border rounded-pill px-3">
                                                    <?php echo $res['SoLuongCho']; ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?php echo $res['GhiChu']; ?></td>
                                            <td class="text-end">
                                                <a href="<?php echo BASE_URL; ?>/supplier/delete_resource/<?php echo $res['MaTaiNguyen']; ?>/<?php echo $supplier['MaNhaCungCap']; ?>" 
                                                   class="btn btn-sm btn-light text-danger border-0" 
                                                   onclick="return confirm('Bạn có chắc muốn xóa tài sản này?')" title="Xóa">
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
    </div>
</section>