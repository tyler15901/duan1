<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-muted">Cập nhật hồ sơ</h4>
        <h2 class="text-primary fw-bold"><?php echo $guide['HoTen']; ?></h2>
    </div>
    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/staff/update/<?php echo $guide['MaNhanSu']; ?>" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square"></i> Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Họ và tên</label>
                            <input type="text" name="ho_ten" class="form-control" value="<?php echo $guide['HoTen']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control" value="<?php echo $guide['NgaySinh']; ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Số điện thoại</label>
                            <input type="text" name="sdt" class="form-control" value="<?php echo $guide['SoDienThoai']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $guide['Email']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Địa chỉ</label>
                        <input type="text" name="dia_chi" class="form-control" value="<?php echo $guide['DiaChi']; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Phân loại</label>
                        <select name="phan_loai" class="form-select">
                            <option value="Tour trong nước" <?php echo ($guide['PhanLoai'] == 'Tour trong nước') ? 'selected' : ''; ?>>Tour trong nước</option>
                            <option value="Tour quốc tế" <?php echo ($guide['PhanLoai'] == 'Tour quốc tế') ? 'selected' : ''; ?>>Tour quốc tế</option>
                            <option value="Tour theo yêu cầu" <?php echo ($guide['PhanLoai'] == 'Tour theo yêu cầu') ? 'selected' : ''; ?>>Tour theo yêu cầu</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <h6 class="text-muted small fw-bold mb-3 text-start">Ảnh đại diện hiện tại</h6>
                    <div class="position-relative d-inline-block mb-3">
                        <?php 
                            $imgSrc = $guide['AnhDaiDien'] ? BASE_URL . '/assets/uploads/' . $guide['AnhDaiDien'] : 'https://via.placeholder.com/150?text=No+Img';
                        ?>
                        <img id="avatarPreview" src="<?php echo $imgSrc; ?>" 
                             class="rounded-circle border shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                             onclick="document.getElementById('avatarInput').click();">
                        
                        <div class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle p-2 border border-white" onclick="document.getElementById('avatarInput').click();" style="cursor: pointer;">
                            <i class="bi bi-pencil-fill"></i>
                        </div>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                    <div class="small text-muted">Nhấn vào ảnh để thay đổi</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success rounded-circle p-2 me-2"><i class="bi bi-shield-check"></i></div>
                        <h6 class="mb-0 fw-bold">Tài khoản Hệ thống</h6>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded p-3 mb-2">
                        <small class="d-block text-white-50 text-uppercase" style="font-size: 0.7rem;">Username</small>
                        <span class="fw-bold fs-5 font-monospace"><?php echo $guide['TenDangNhap']; ?></span>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded p-3">
                        <small class="d-block text-white-50 text-uppercase" style="font-size: 0.7rem;">Trạng thái</small>
                        <?php if($guide['TrangThaiTK'] == 'Hoạt động'): ?>
                            <span class="text-success fw-bold"><i class="bi bi-circle-fill small"></i> Đang hoạt động</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill small"></i> Đã bị khóa</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-3 text-end">
                        <a href="#" class="text-white-50 small text-decoration-none" onclick="alert('Vui lòng vào Module Người dùng để đổi mật khẩu!')">
                            <i class="bi bi-key"></i> Đổi mật khẩu?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-warning px-5 fw-bold shadow">
            <i class="bi bi-save"></i> Cập nhật Thông tin
        </button>
    </div>
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>