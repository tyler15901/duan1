<div class="pagetitle">
    <h1>Cập nhật Hồ Sơ</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/staff/index">Nhân sự</a></li>
            <li class="breadcrumb-item active"><?php echo $guide['HoTen']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/staff/update/<?php echo $guide['MaNhanSu']; ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="bi bi-pencil-square me-2"></i> Thông tin cá nhân</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên</label>
                                <input type="text" name="ho_ten" class="form-control" value="<?php echo $guide['HoTen']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngay_sinh" class="form-control" value="<?php echo $guide['NgaySinh']; ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="sdt" class="form-control" value="<?php echo $guide['SoDienThoai']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $guide['Email']; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <input type="text" name="dia_chi" class="form-control" value="<?php echo $guide['DiaChi']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phân loại chuyên môn</label>
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
                <div class="card mb-3">
                    <div class="card-body text-center pt-4">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Ảnh đại diện</h6>
                        <div class="position-relative d-inline-block mb-3">
                            <?php 
                                $imgSrc = $guide['AnhDaiDien'] ? BASE_URL . '/assets/uploads/' . $guide['AnhDaiDien'] : 'https://via.placeholder.com/150?text=No+Img';
                            ?>
                            <img id="avatarPreview" src="<?php echo $imgSrc; ?>" 
                                 class="rounded-circle border shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                 onclick="document.getElementById('avatarInput').click();">
                            
                            <div class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle p-2 border border-white" 
                                 style="cursor: pointer; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                 onclick="document.getElementById('avatarInput').click();">
                                <i class="bi bi-pencil-fill small"></i>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                </div>

                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2"><i class="bi bi-shield-check"></i></div>
                            <h6 class="mb-0 fw-bold text-white">Tài khoản Hệ thống</h6>
                        </div>
                        
                        <div class="bg-white bg-opacity-10 rounded p-3 mb-2">
                            <small class="d-block text-white-50 text-uppercase" style="font-size: 0.7rem;">Username</small>
                            <span class="fw-bold fs-5 font-monospace"><?php echo $guide['TenDangNhap']; ?></span>
                        </div>

                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <small class="d-block text-white-50 text-uppercase" style="font-size: 0.7rem;">Trạng thái</small>
                            <?php if($guide['TrangThaiTK'] == 'Hoạt động'): ?>
                                <span class="text-light fw-bold"><i class="bi bi-circle-fill small text-success"></i> Đang hoạt động</span>
                            <?php else: ?>
                                <span class="text-light fw-bold"><i class="bi bi-x-circle-fill small text-danger"></i> Đã bị khóa</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-warning btn-lg shadow-sm">
                        <i class="bi bi-save me-2"></i> Cập Nhật
                    </button>
                    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-link text-decoration-none text-muted mt-2">Quay lại</a>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('avatarPreview').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>