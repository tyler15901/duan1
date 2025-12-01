<div class="pagetitle">
    <h1>Thêm Đối tác mới</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/supplier/index">Nhà cung cấp</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="bi bi-info-circle me-2"></i> Thông tin cơ bản</h5>
                    
                    <form action="<?php echo BASE_URL; ?>/supplier/store" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên Nhà cung cấp <span class="text-danger">*</span></label>
                            <input type="text" name="ten_ncc" class="form-control form-control-lg" required placeholder="VD: Nhà xe Phương Trang, Khách sạn Mường Thanh...">
                            <div class="form-text">Tên doanh nghiệp hoặc cá nhân cung cấp dịch vụ.</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loại hình dịch vụ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                    <select name="loai_cc" class="form-select" id="serviceType">
                                        <option value="Vận chuyển">🚌 Vận chuyển (Xe du lịch)</option>
                                        <option value="Lưu trú">🏨 Lưu trú (Khách sạn/Resort)</option>
                                        <option value="Ăn uống">🍽️ Ăn uống (Nhà hàng)</option>
                                        <option value="Khác">📦 Dịch vụ khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="sdt" class="form-control" placeholder="Hotline hoặc SĐT quản lý" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Địa chỉ văn phòng / Cơ sở</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="dia_chi" class="form-control" placeholder="Số nhà, đường, quận huyện...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="<?php echo BASE_URL; ?>/supplier/index" class="btn btn-light border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Lưu thông tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>