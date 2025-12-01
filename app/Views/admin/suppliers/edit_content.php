<div class="pagetitle">
    <h1>Cập nhật Đối tác</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/supplier/index">Nhà cung cấp</a></li>
            <li class="breadcrumb-item active"><?php echo $supplier['TenNhaCungCap']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="bi bi-pencil-square me-2"></i> Chỉnh sửa thông tin</h5>
                    
                    <form action="<?php echo BASE_URL; ?>/supplier/update/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Tên đơn vị <span class="text-danger">*</span></label>
                                <input type="text" name="ten_ncc" class="form-control fw-bold" required value="<?php echo $supplier['TenNhaCungCap']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trạng thái hợp tác</label>
                                <select name="trang_thai" class="form-select fw-bold <?php echo ($supplier['TrangThai'] == 'Hoạt động') ? 'text-success border-success' : 'text-danger border-danger'; ?>">
                                    <option value="Hoạt động" <?php echo ($supplier['TrangThai'] == 'Hoạt động') ? 'selected' : ''; ?>>✅ Đang hoạt động</option>
                                    <option value="Ngừng hợp tác" <?php echo ($supplier['TrangThai'] == 'Ngừng hợp tác') ? 'selected' : ''; ?>>❌ Ngừng hợp tác</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mảng kinh doanh</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                    <select name="loai_cc" class="form-select">
                                        <option value="Vận chuyển" <?php echo ($supplier['LoaiCungCap'] == 'Vận chuyển') ? 'selected' : ''; ?>>🚌 Vận chuyển</option>
                                        <option value="Lưu trú" <?php echo ($supplier['LoaiCungCap'] == 'Lưu trú') ? 'selected' : ''; ?>>🏨 Lưu trú</option>
                                        <option value="Ăn uống" <?php echo ($supplier['LoaiCungCap'] == 'Ăn uống') ? 'selected' : ''; ?>>🍽️ Ăn uống</option>
                                        <option value="Khác" <?php echo ($supplier['LoaiCungCap'] == 'Khác') ? 'selected' : ''; ?>>📦 Khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Hotline</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="sdt" class="form-control" value="<?php echo $supplier['SoDienThoai']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="dia_chi" class="form-control" value="<?php echo $supplier['DiaChi']; ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-light border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>