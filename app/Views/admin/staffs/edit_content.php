<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cập nhật Hướng Dẫn Viên</h2>
    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-secondary">Quay lại</a>
</div>

<form action="<?php echo BASE_URL; ?>/staff/update/<?php echo $guide['MaNhanSu']; ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark fw-bold">Thông tin cá nhân</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Họ và tên (*)</label>
                            <input type="text" name="ho_ten" class="form-control" value="<?php echo $guide['HoTen']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control" value="<?php echo $guide['NgaySinh']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold">Phân loại HDV</label>
                        <select name="phan_loai" class="form-select">
                            <option value="Tour trong nước" <?php echo ($guide['PhanLoai'] == 'Tour trong nước') ? 'selected' : ''; ?>>Tour trong nước</option>
                            <option value="Tour quốc tế" <?php echo ($guide['PhanLoai'] == 'Tour quốc tế') ? 'selected' : ''; ?>>Tour quốc tế</option>
                            <option value="Tour theo yêu cầu" <?php echo ($guide['PhanLoai'] == 'Tour theo yêu cầu') ? 'selected' : ''; ?>>Tour theo yêu cầu</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Số điện thoại (*)</label>
                            <input type="text" name="sdt" class="form-control" value="<?php echo $guide['SoDienThoai']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $guide['Email']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Địa chỉ</label>
                        <input type="text" name="dia_chi" class="form-control" value="<?php echo $guide['DiaChi']; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white fw-bold">Ảnh đại diện</div>
                <div class="card-body text-center">
                    <?php if($guide['AnhDaiDien']): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $guide['AnhDaiDien']; ?>" class="img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="text-muted mb-3 border p-4">Chưa có ảnh</div>
                    <?php endif; ?>
                    
                    <input type="file" name="avatar" class="form-control">
                    <small class="text-muted">Chọn ảnh mới để thay thế (nếu muốn)</small>
                </div>
            </div>

            <div class="card mb-3 bg-light">
                <div class="card-header">Tài khoản liên kết</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Username:</strong> <?php echo $guide['TenDangNhap']; ?></p>
                    <p class="mb-0 text-muted small">ID Người dùng: #<?php echo $guide['MaNguoiDung']; ?></p>
                    <hr>
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-info-circle"></i> Để đổi mật khẩu hoặc khóa tài khoản, vui lòng sử dụng module <strong>Quản lý Người dùng</strong> (Tính năng nâng cao).
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-warning px-5 fw-bold">Lưu thay đổi</button>
</form>