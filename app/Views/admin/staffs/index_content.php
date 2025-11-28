<div class="d-flex justify-content-between mb-3">
    <h2>Quản lý Hướng Dẫn Viên</h2>
    <a href="<?php echo BASE_URL; ?>/staff/create" class="btn btn-primary">+ Thêm HDV</a>
</div>

<table class="table table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Họ tên / Phân loại</th>
            <th>Liên hệ</th>
            <th>Tài khoản</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($guides as $g): ?>
            <tr>
                <td><?php echo $g['MaNhanSu']; ?></td>
                <td>
                    <?php if ($g['AnhDaiDien']): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $g['AnhDaiDien']; ?>" width="50"
                            class="rounded-circle">
                    <?php else: ?>
                        <span class="text-muted">No img</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="fw-bold"><?php echo $g['HoTen']; ?></div>
                    <span class="badge bg-info text-dark"><?php echo $g['PhanLoai']; ?></span>
                    <small class="d-block text-muted">NS:
                        <?php
                        if (!empty($g['NgaySinh'])) {
                            echo date('d/m/Y', strtotime($g['NgaySinh']));
                        } else {
                            echo '<span class="text-muted">Chưa cập nhật</span>';
                        }
                        ?>
                    </small>
                </td>
                <td>
                    <i class="bi bi-telephone"></i> <?php echo $g['SoDienThoai']; ?> <br>
                    <small><?php echo $g['Email']; ?></small> <br>
                    <small class="text-muted"><?php echo $g['DiaChi']; ?></small>
                </td>
                <td>
                    <strong><?php echo $g['TenDangNhap']; ?></strong> <br>
                    <?php if ($g['TrangThaiTK'] == 'Hoạt động'): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Locked</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo BASE_URL; ?>/staff/edit/<?php echo $g['MaNhanSu']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="<?php echo BASE_URL; ?>/staff/delete/<?php echo $g['MaNhanSu']; ?>"
                        class="btn btn-sm btn-danger" onclick="return confirm('Xóa HDV và Tài khoản này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>