<h2 class="mb-4">Thêm Hướng Dẫn Viên Mới</h2>

<form action="<?php echo BASE_URL; ?>/staff/store" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">1. Thông tin cá nhân</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Họ và tên (*)</label>
                            <input type="text" name="ho_ten" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold">Phân loại HDV</label>
                        <select name="phan_loai" class="form-select">
                            <option value="Tour trong nước">Tour trong nước (Nội địa)</option>
                            <option value="Tour quốc tế">Tour quốc tế (Inbound/Outbound)</option>
                            <option value="Tour theo yêu cầu">Tour theo yêu cầu (VIP/Private)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Số điện thoại (*)</label>
                            <input type="text" name="sdt" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Địa chỉ</label>
                        <input type="text" name="dia_chi" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Ảnh đại diện</label>
                        <input type="file" name="avatar" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-3 bg-light">
                <div class="card-header bg-warning text-dark">2. Tài khoản Hệ thống</div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        Tài khoản này dùng để HDV đăng nhập vào App/Web để xem lịch trình được phân công.
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Tên đăng nhập (*)</label>
                        <input type="text" name="username" class="form-control" required placeholder="VD: hdv_tuananh">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Mật khẩu (*)</label>
                        <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-success btn-lg px-5">Lưu thông tin</button>
</form>