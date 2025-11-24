<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 900px;">
        <h2 class="mb-4 text-primary">✏️ Cập nhật Tour</h2>
        
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>adminTour/update/<?= $data['tour']['MaTour'] ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Tour:</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?= htmlspecialchars($data['tour']['TenTour']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại Tour:</label>
                            <select name="type" class="form-select">
                                <option value="1" <?= $data['tour']['MaLoaiTour'] == 1 ? 'selected' : '' ?>>Tour Trong Nước</option>
                                <option value="2" <?= $data['tour']['MaLoaiTour'] == 2 ? 'selected' : '' ?>>Tour Quốc Tế</option>
                                <option value="3" <?= $data['tour']['MaLoaiTour'] == 3 ? 'selected' : '' ?>>Tour Theo Yêu Cầu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Trạng thái:</label>
                            <select name="status" class="form-select">
                                <option value="Hoạt động" <?= $data['tour']['TrangThai'] == 'Hoạt động' ? 'selected' : '' ?>>Hoạt động (Hiện lên web)</option>
                                <option value="Tạm dừng" <?= $data['tour']['TrangThai'] == 'Tạm dừng' ? 'selected' : '' ?>>Tạm dừng (Ẩn đi)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số ngày:</label>
                            <input type="number" name="days" class="form-control" 
                                   value="<?= $data['tour']['SoNgay'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số chỗ tối đa:</label>
                            <input type="number" name="slots" class="form-control" 
                                   value="<?= $data['tour']['SoChoToiDa'] ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cập nhật Giá (Tùy chọn):</label>
                        <input type="number" name="price" class="form-control" 
                               placeholder="Nhập giá mới nếu muốn thay đổi giá hiện tại (<?= number_format($data['tour']['GiaHienTai'] ?? 0) ?> đ)">
                        <div class="form-text text-muted">Nếu để trống sẽ giữ nguyên giá cũ.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh Đại Diện:</label>
                        <div class="mb-2">
                            <img src="<?= BASE_URL . 'uploads/' . $data['tour']['HinhAnh'] ?>" height="100" class="rounded border">
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">Chỉ chọn ảnh nếu muốn thay đổi ảnh đại diện mới.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết:</label>
                        <textarea name="desc" class="form-control" rows="6"><?= htmlspecialchars($data['tour']['MoTa']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASE_URL ?>adminTour/index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="fas fa-save"></i> LƯU CẬP NHẬT
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>