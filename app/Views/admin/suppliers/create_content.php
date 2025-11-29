<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary fw-bold">Thêm Đối tác mới</h3>
    <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle text-primary"></i> Thông tin cơ bản</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASE_URL; ?>/supplier/store" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tên Nhà cung cấp <span class="text-danger">*</span></label>
                        <input type="text" name="ten_ncc" class="form-control form-control-lg" required placeholder="VD: Nhà xe Phương Trang, Khách sạn Mường Thanh...">
                        <div class="form-text">Tên doanh nghiệp hoặc cá nhân cung cấp dịch vụ.</div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại hình dịch vụ <span class="text-danger">*</span></label>
                            <select name="loai_cc" class="form-select" id="serviceType">
                                <option value="Vận chuyển">🚌 Vận chuyển (Xe du lịch)</option>
                                <option value="Lưu trú">🏨 Lưu trú (Khách sạn/Resort)</option>
                                <option value="Ăn uống">🍽️ Ăn uống (Nhà hàng)</option>
                                <option value="Khác">📦 Dịch vụ khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                            <input type="text" name="sdt" class="form-control" placeholder="Hotline hoặc SĐT người quản lý">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Địa chỉ văn phòng / Cơ sở</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="dia_chi" class="form-control" placeholder="Số nhà, đường, quận huyện...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light border px-4">Làm lại</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                            <i class="bi bi-check-lg"></i> Lưu thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>