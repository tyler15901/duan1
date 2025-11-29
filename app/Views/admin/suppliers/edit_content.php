<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Cập nhật thông tin</h4>
        <h2 class="text-primary fw-bold"><?php echo $supplier['TenNhaCungCap']; ?></h2>
    </div>
    <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="<?php echo BASE_URL; ?>/supplier/update/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Tên đơn vị</label>
                            <input type="text" name="ten_ncc" class="form-control fw-bold" required value="<?php echo $supplier['TenNhaCungCap']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Trạng thái hợp tác</label>
                            <select name="trang_thai" class="form-select fw-bold <?php echo ($supplier['TrangThai'] == 'Hoạt động') ? 'text-success border-success' : 'text-danger border-danger'; ?>">
                                <option value="Hoạt động" <?php echo ($supplier['TrangThai'] == 'Hoạt động') ? 'selected' : ''; ?>>✅ Đang hoạt động</option>
                                <option value="Ngừng hợp tác" <?php echo ($supplier['TrangThai'] == 'Ngừng hợp tác') ? 'selected' : ''; ?>>❌ Ngừng hợp tác</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Mảng kinh doanh</label>
                            <select name="loai_cc" class="form-select">
                                <option value="Vận chuyển" <?php echo ($supplier['LoaiCungCap'] == 'Vận chuyển') ? 'selected' : ''; ?>>🚌 Vận chuyển</option>
                                <option value="Lưu trú" <?php echo ($supplier['LoaiCungCap'] == 'Lưu trú') ? 'selected' : ''; ?>>🏨 Lưu trú</option>
                                <option value="Ăn uống" <?php echo ($supplier['LoaiCungCap'] == 'Ăn uống') ? 'selected' : ''; ?>>🍽️ Ăn uống</option>
                                <option value="Khác" <?php echo ($supplier['LoaiCungCap'] == 'Khác') ? 'selected' : ''; ?>>📦 Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Hotline</label>
                            <input type="text" name="sdt" class="form-control" value="<?php echo $supplier['SoDienThoai']; ?>">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold small text-muted">Địa chỉ</label>
                        <input type="text" name="dia_chi" class="form-control" value="<?php echo $supplier['DiaChi']; ?>">
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-end">
                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                        <i class="bi bi-save"></i> Cập nhật thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>