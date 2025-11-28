<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo $tour['TenTour']; ?></h2>
    <div>
        <a href="<?php echo BASE_URL; ?>/tour/edit/<?php echo $tour['MaTour']; ?>" class="btn btn-warning">Sửa Tour</a>
        <a href="<?php echo BASE_URL; ?>/tour/delete/<?php echo $tour['MaTour']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa tour này?')">Xóa Tour</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" class="card-img-top" style="height: 300px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title text-primary">Giới thiệu</h5>
                <p class="card-text"><?php echo nl2br($tour['MoTa']); ?></p>
                
                <h5 class="mt-4 text-primary">Chính sách</h5>
                <div class="bg-light p-3 border rounded">
                    <?php echo nl2br($tour['ChinhSach']); ?>
                </div>
            </div>
        </div>

        <h4 class="mb-3">Lịch trình chi tiết</h4>
        <div class="list-group">
            <?php foreach($schedule as $day): ?>
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1 text-success">Ngày <?php echo $day['NgayThu']; ?>: <?php echo $day['TieuDe']; ?></h5>
                </div>
                <p class="mb-1"><?php echo nl2br($day['NoiDung']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-info text-white fw-bold">Thông tin tóm tắt</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Mã Tour:</strong> <?php echo $tour['MaTour']; ?></li>
                <li class="list-group-item"><strong>Loại:</strong> <?php echo $tour['TenLoai']; ?></li>
                <li class="list-group-item"><strong>Thời gian:</strong> <?php echo $tour['SoNgay']; ?> ngày</li>
                <li class="list-group-item"><strong>Số chỗ:</strong> <?php echo $tour['SoChoToiDa']; ?> khách</li>
                <li class="list-group-item"><strong>Trạng thái:</strong> 
                    <span class="badge bg-<?php echo ($tour['TrangThai']=='Hoạt động')?'success':'secondary'; ?>">
                        <?php echo $tour['TrangThai']; ?>
                    </span>
                </li>
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-warning fw-bold">Giá tham khảo</div>
            <ul class="list-group list-group-flush">
                <?php foreach($prices as $p): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?php echo $p['DoiTuong']; ?>:</span>
                        <strong class="text-danger"><?php echo number_format($p['Gia']); ?> đ</strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Thư viện ảnh</div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach($gallery as $img): ?>
                    <div class="col-4">
                        <img src="<?php echo BASE_URL . '/' . $img['DuongDan']; ?>" class="img-fluid rounded border">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>