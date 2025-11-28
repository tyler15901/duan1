<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Thông tin Nhà cung cấp</div>
            <div class="card-body">
                <h4><?php echo $supplier['TenNhaCungCap']; ?></h4>
                <p><strong>Loại:</strong> <?php echo $supplier['LoaiCungCap']; ?></p>
                <p><strong>SĐT:</strong> <?php echo $supplier['SoDienThoai']; ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo $supplier['DiaChi']; ?></p>
                <hr>
                <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $supplier['MaNhaCungCap']; ?>" class="btn btn-warning w-100">Chỉnh sửa thông tin</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span>Danh sách Tài nguyên (<?php echo count($resources); ?>)</span>
            </div>
            <div class="card-body">
                
                <form action="<?php echo BASE_URL; ?>/supplier/store_resource/<?php echo $supplier['MaNhaCungCap']; ?>" method="POST" class="row g-2 mb-4 align-items-end border-bottom pb-3">
                    <div class="col-md-5">
                        <label class="small fw-bold">Tên tài nguyên (Biển số/Số phòng)</label>
                        <input type="text" name="ten_tai_nguyen" class="form-control form-control-sm" required placeholder="VD: 29B-12345">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Sức chứa (Chỗ/Người)</label>
                        <input type="number" name="so_luong_cho" class="form-control form-control-sm" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Ghi chú</label>
                        <input type="text" name="ghi_chu" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-success w-100">Thêm</button>
                    </div>
                </form>

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tên tài nguyên</th>
                            <th>Sức chứa</th>
                            <th>Ghi chú</th>
                            <th width="50">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($resources)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Chưa có tài nguyên nào.</td></tr>
                        <?php else: ?>
                            <?php foreach($resources as $res): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $res['TenTaiNguyen']; ?></td>
                                <td><?php echo $res['SoLuongCho']; ?></td>
                                <td><?php echo $res['GhiChu']; ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/supplier/delete_resource/<?php echo $res['MaTaiNguyen']; ?>/<?php echo $supplier['MaNhaCungCap']; ?>" 
                                       class="text-danger" onclick="return confirm('Xóa tài nguyên này?')">
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