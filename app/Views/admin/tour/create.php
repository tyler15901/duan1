<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Tour Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 800px;">
        <h2 class="mb-4">➕ Thêm Tour Du Lịch Mới</h2>
        
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>adminTour/store" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Tour:</label>
                        <input type="text" name="name" class="form-control" required placeholder="VD: Hà Nội - Sapa 3N2Đ">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại Tour:</label>
                            <select name="type" class="form-select">
                                <option value="1">Tour Trong Nước</option>
                                <option value="2">Tour Quốc Tế</option>
                                <option value="3">Tour Theo Yêu Cầu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá Người Lớn (VNĐ):</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số ngày:</label>
                            <input type="number" name="days" class="form-control" value="3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số chỗ tối đa:</label>
                            <input type="number" name="slots" class="form-control" value="20">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh Đại Diện (Banner):</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết:</label>
                        <textarea name="desc" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>adminTour/index" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-5">Lưu Tour</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>