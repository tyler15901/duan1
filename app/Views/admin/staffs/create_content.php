<div class="pagetitle">
    <h1>Thêm Hướng Dẫn Viên</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/staff/index">Nhân sự</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<section class="section">
    <form action="<?php echo BASE_URL; ?>/staff/store" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="bi bi-person-vcard me-2"></i> Thông tin cá nhân</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required placeholder="VD: Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngay_sinh" class="form-control">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control" required placeholder="09xxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Địa chỉ thường trú</label>
                            <input type="text" name="dia_chi" class="form-control" placeholder="Số nhà, đường, phường/xã...">
                        </div>

                        <h5 class="card-title text-info pt-0"><i class="bi bi-briefcase me-2"></i> Chuyên môn</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card-radio p-3 border rounded h-100 bg-light">
                                    <input class="form-check-input" type="radio" name="phan_loai" id="pl1" value="Tour trong nước" checked>
                                    <label class="form-check-label fw-bold w-100 cursor-pointer" for="pl1">
                                        🇻🇳 Nội địa
                                        <div class="small text-muted fw-normal mt-1">Chuyên tuyến điểm trong nước</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio p-3 border rounded h-100 bg-light">
                                    <input class="form-check-input" type="radio" name="phan_loai" id="pl2" value="Tour quốc tế">
                                    <label class="form-check-label fw-bold w-100 cursor-pointer" for="pl2">
                                        🌏 Quốc tế
                                        <div class="small text-muted fw-normal mt-1">Inbound / Outbound</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center pt-4">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Ảnh đại diện</h6>
                        
                        <div class="position-relative d-inline-block mb-3">
                            <img id="avatarPreview" src="https://via.placeholder.com/150?text=Upload" 
                                 class="rounded-circle border shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                 onclick="document.getElementById('avatarInput').click();">
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 border border-white" 
                                 style="cursor: pointer; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                 onclick="document.getElementById('avatarInput').click();">
                                <i class="bi bi-camera-fill small"></i>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                        <div class="text-muted small">Nhấn vào ảnh để tải lên</div>
                    </div>
                </div>

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><i class="bi bi-shield-lock-fill me-2"></i> Tài khoản App</h5>
                        <div class="alert alert-info small py-2 d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <span>Cấp tài khoản để đăng nhập.</span>
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
                
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-check-circle me-2"></i> Lưu Hồ Sơ
                    </button>
                    <a href="<?php echo BASE_URL; ?>/staff/index" class="btn btn-link text-decoration-none text-muted mt-2">Hủy bỏ</a>
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