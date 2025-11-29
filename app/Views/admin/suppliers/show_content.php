<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>/supplier/index" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-top border-4 border-primary h-100">
            <div class="card-body text-center pt-5 pb-4">
                <div class="mb-3">
                    <?php if($supplier['LoaiCungCap'] == 'Vận chuyển'): ?>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex p-3">
                            <i class="bi bi-bus-front-fill fs-1"></i>
                        </div>
                    <?php elseif($supplier['LoaiCungCap'] == 'Lưu trú'): ?>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex p-3">
                            <i class="bi bi-building-fill fs-1"></i>
                        </div>
                    <?php else: ?>
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex p-3">
                            <i class="bi bi-shop fs-1"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="fw-bold text-dark mb-1"><?php echo $supplier['TenNhaCungCap']; ?></h4>
                <span class="badge bg-light text-dark border mb-3"><?php echo $supplier['LoaiCungCap']; ?></span>

                <ul class="list-group list-group-flush text-start mt-3">
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted small">Liên hệ:</span>
                        <span class="fw-bold"><?php echo $supplier['SoDienThoai']; ?></span>
                    </li>
                    <li class="list-group-item px-0 border-0">
                        <span class="text-muted small d-block mb-1">Địa chỉ:</span>
                        <span><?php echo $supplier['DiaChi']; ?></span>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted small">Trạng thái:</span>
                        <?php if($supplier['TrangThai'] == 'Hoạt động'): ?>
                            <span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Hoạt động</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Dừng</span>
                        <?php endif; ?>
                    </li>
                </ul>

                <div class="d-grid mt-4">
                    <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $supplier['MaNhaCungCap']; ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa thông tin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-collection text-success"></i> Danh mục Tài sản
                    <span class="badge bg-secondary rounded-pill ms-2"><?php echo count($resources); ?></span>
                </h5>
            </div>
            <div class="card-body">
                
                <div class="bg-light p-3 rounded mb-4 border">
                    <h6 class="fw-bold small text-uppercase text-muted mb-3">Thêm tài sản mới (Xe / Phòng)</h6>
                    <form action="<?php echo BASE_URL; ?>/supplier/store_resource/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="ten_tai_nguyen" class="form-control form-control-sm" required 
                                   placeholder="Tên/Biển số (VD: 29B-12345, Phòng VIP)">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="so_luong_cho" class="form-control form-control-sm" value="" 
                                   placeholder="Sức chứa (Chỗ)">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="ghi_chu" class="form-control form-control-sm" 
                                   placeholder="Ghi chú (Màu xe, View...)">
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
                        <thead class="bg-white text-secondary border-bottom">
                            <tr>
                                <th>Tên Tài sản / Biển số</th>
                                <th class="text-center">Sức chứa</th>
                                <th>Mô tả thêm</th>
                                <th class="text-end">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resources)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 opacity-25"></i>
                                        <p class="mt-2 small">Chưa có tài sản nào được khai báo.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($resources as $res): ?>
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
                                           class="btn btn-sm btn-light text-danger border-0 hover-danger" 
                                           onclick="return confirm('Bạn có chắc muốn xóa tài sản [<?php echo $res['TenTaiNguyen']; ?>] không?')" title="Xóa bỏ">
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