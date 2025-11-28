<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Chỉnh sửa Nhà Cung Cấp</h2>
    <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-secondary">Quay lại danh sách</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/supplier/update/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Nhà cung cấp (*)</label>
                        <input type="text" name="ten_ncc" class="form-control" required 
                               value="<?php echo $supplier['TenNhaCungCap']; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="Hoạt động" <?php echo ($supplier['TrangThai'] == 'Hoạt động') ? 'selected' : ''; ?>>Hoạt động</option>
                            <option value="Ngừng hợp tác" <?php echo ($supplier['TrangThai'] == 'Ngừng hợp tác') ? 'selected' : ''; ?>>Ngừng hợp tác</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Loại hình cung cấp</label>
                    <select name="loai_cc" class="form-select">
                        <option value="Vận chuyển" <?php echo ($supplier['LoaiCungCap'] == 'Vận chuyển') ? 'selected' : ''; ?>>Vận chuyển (Xe)</option>
                        <option value="Lưu trú" <?php echo ($supplier['LoaiCungCap'] == 'Lưu trú') ? 'selected' : ''; ?>>Lưu trú (Khách sạn)</option>
                        <option value="Ăn uống" <?php echo ($supplier['LoaiCungCap'] == 'Ăn uống') ? 'selected' : ''; ?>>Ăn uống (Nhà hàng)</option>
                        <option value="Khác" <?php echo ($supplier['LoaiCungCap'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Số điện thoại</label>
                    <input type="text" name="sdt" class="form-control" 
                           value="<?php echo $supplier['SoDienThoai']; ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Địa chỉ</label>
                <input type="text" name="dia_chi" class="form-control" 
                       value="<?php echo $supplier['DiaChi']; ?>">
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-light border">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>