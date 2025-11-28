<div class="d-flex justify-content-between mb-3">
    <h2>Quản lý Nhà cung cấp</h2>
    <a href="<?php echo BASE_URL; ?>/supplier/create" class="btn btn-primary">+ Thêm mới</a>
</div>

<table class="table table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Tên Nhà cung cấp</th>
            <th>Loại hình</th>
            <th>Liên hệ</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($suppliers as $s): ?>
        <tr>
            <td><?php echo $s['MaNhaCungCap']; ?></td>
            <td class="fw-bold text-primary"><?php echo $s['TenNhaCungCap']; ?></td>
            <td>
                <?php if($s['LoaiCungCap'] == 'Vận chuyển'): ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-truck"></i> Vận chuyển</span>
                <?php else: ?>
                    <span class="badge bg-info text-dark"><i class="bi bi-building"></i> Lưu trú</span>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $s['SoDienThoai']; ?> <br>
                <small class="text-muted"><?php echo $s['DiaChi']; ?></small>
            </td>
            <td><?php echo $s['TrangThai']; ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/supplier/show/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-info text-white">Chi tiết & Tài sản</a>
                <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-secondary">Sửa</a>
                <a href="<?php echo BASE_URL; ?>/supplier/delete/<?php echo $s['MaNhaCungCap']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa NCC này sẽ xóa hết xe/phòng của họ?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>