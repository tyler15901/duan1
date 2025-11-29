<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary fw-bold">Thêm Hướng Dẫn Viên</h3>
    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/staff/store" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-vcard text-primary"></i> 1. Thông tin cá nhân</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" class="form-control" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="sdt" class="form-control" required placeholder="09xxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Địa chỉ thường trú</label>
                        <input type="text" name="dia_chi" class="form-control" placeholder="Số nhà, đường, phường/xã...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Chuyên môn / Phân loại</label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-3 border rounded w-100">
                                <input class="form-check-input" type="radio" name="phan_loai" id="pl1" value="Tour trong nước" checked>
                                <label class="form-check-label fw-bold w-100" for="pl1">
                                    🇻🇳 Nội địa
                                    <div class="small text-muted fw-normal">Chuyên tuyến trong nước</div>
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded w-100">
                                <input class="form-check-input" type="radio" name="phan_loai" id="pl2" value="Tour quốc tế">
                                <label class="form-check-label fw-bold w-100" for="pl2">
                                    🌏 Quốc tế
                                    <div class="small text-muted fw-normal">Inbound / Outbound</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <label class="form-label fw-bold small text-muted d-block text-start mb-3">Ảnh đại diện</label>
                    
                    <div class="position-relative d-inline-block mb-3">
                        <img id="avatarPreview" src="https://via.placeholder.com/150?text=Upload" 
                             class="rounded-circle border shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                             onclick="document.getElementById('avatarInput').click();">
                        <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 border border-white" style="cursor: pointer;" onclick="document.getElementById('avatarInput').click();">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                    <div class="text-muted small">Nhấn vào ảnh để tải lên</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock text-success"></i> 2. Tài khoản Hệ thống</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning small py-2 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <span>Cấp tài khoản để HDV đăng nhập App.</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-bottom bg-white border-top py-3 shadow" style="padding-left: 270px; z-index: 99;">
        <div class="container-fluid px-4 text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                <i class="bi bi-check-lg"></i> Lưu Hướng Dẫn Viên
            </button>
        </div>
    </div>
</form>
<br><br><br>

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