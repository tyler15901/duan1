<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold border-start border-4 border-primary ps-3 mb-0">Đối tác & Nhà cung cấp</h2>
        <p class="text-muted small ps-3 mb-0 mt-1">Quản lý mạng lưới xe, khách sạn, nhà hàng đối tác.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/supplier/create" class="btn btn-primary shadow-sm">
        <i class="bi bi-building-add"></i> Thêm Đối tác mới
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm theo tên nhà xe, khách sạn...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select text-secondary">
                    <option value="">-- Tất cả loại hình --</option>
                    <option value="Vận chuyển">🚌 Vận chuyển</option>
                    <option value="Lưu trú">🏨 Lưu trú</option>
                    <option value="Ăn uống">🍽️ Ăn uống</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select text-secondary">
                    <option value="">-- Trạng thái --</option>
                    <option value="Hoạt động">Đang hợp tác</option>
                    <option value="Ngừng hợp tác">Dừng hợp tác</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-dark w-100">Lọc</button>
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
                        <th class="ps-4">Thông tin Đối tác</th>
                        <th>Loại hình</th>
                        <th>Liên hệ</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($suppliers)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có dữ liệu nhà cung cấp.</td></tr>
                    <?php else: ?>
                        <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded p-2 me-3 bg-light text-secondary border">
                                        <?php if($s['LoaiCungCap'] == 'Vận chuyển'): ?>
                                            <i class="bi bi-bus-front fs-4"></i>
                                        <?php elseif($s['LoaiCungCap'] == 'Lưu trú'): ?>
                                            <i class="bi bi-building fs-4"></i>
                                        <?php elseif($s['LoaiCungCap'] == 'Ăn uống'): ?>
                                            <i class="bi bi-cup-hot fs-4"></i>
                                        <?php else: ?>
                                            <i class="bi bi-box-seam fs-4"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo $s['TenNhaCungCap']; ?></div>
                                        <div class="small text-muted">ID: #<?php echo $s['MaNhaCungCap']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $typeClass = 'secondary text-white'; // Mặc định
                                    if ($s['LoaiCungCap'] == 'Vận chuyển') $typeClass = 'warning text-dark';
                                    elseif ($s['LoaiCungCap'] == 'Lưu trú') $typeClass = 'info text-dark';
                                    elseif ($s['LoaiCungCap'] == 'Ăn uống') $typeClass = 'success text-white';
                                ?>
                                <span class="badge bg-<?php echo $typeClass; ?> bg-opacity-75 border border-white shadow-sm">
                                    <?php echo $s['LoaiCungCap']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="mb-1"><i class="bi bi-telephone w-20 text-primary"></i> <?php echo $s['SoDienThoai']; ?></span>
                                    <span class="text-muted text-truncate" style="max-width: 200px;" title="<?php echo $s['DiaChi']; ?>">
                                        <i class="bi bi-geo-alt w-20"></i> <?php echo $s['DiaChi']; ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if($s['TrangThai'] == 'Hoạt động'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                                        <i class="bi bi-check-circle-fill"></i> Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                                        <i class="bi bi-pause-circle-fill"></i> Dừng hợp tác
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo BASE_URL; ?>/supplier/show/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Quản lý Tài nguyên">
                                    <i class="bi bi-list-check"></i> Tài sản
                                </a>
                                <div class="btn-group">
                                    <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-light border" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/supplier/delete/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Xóa NCC này?')" title="Xóa">
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
</div>