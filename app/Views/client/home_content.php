<div class="p-4 mb-4 bg-light rounded-3 shadow-sm text-center" style="background: url('https://source.unsplash.com/1600x400/?travel,nature') center/cover no-repeat;">
    <div class="bg-white p-4 rounded d-inline-block shadow" style="opacity: 0.95; max-width: 800px; width: 100%;">
        <h2 class="mb-3">Bạn muốn đi đâu hôm nay?</h2>
        <form action="" method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control" placeholder="Nhập tên địa điểm..." value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <div class="col-md-4">
                <select name="cat" class="form-select">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['MaLoaiTour']; ?>" <?php echo ($cat_id == $cat['MaLoaiTour']) ? 'selected' : ''; ?>>
                            <?php echo $cat['TenLoai']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Tìm Tour</button>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if(empty($tours)): ?>
        <div class="col-12 text-center">
            <h4 class="text-muted">Không tìm thấy tour nào phù hợp!</h4>
        </div>
    <?php else: ?>
        <?php foreach($tours as $tour): ?>
        <div class="col">
            <div class="card h-100 tour-card shadow-sm border-0">
                <div class="position-relative">
                    <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $tour['HinhAnh']; ?>" class="card-img-top" alt="<?php echo $tour['TenTour']; ?>">
                    <span class="position-absolute top-0 end-0 bg-warning text-dark px-2 py-1 m-2 rounded fw-bold small">
                        <?php echo $tour['SoNgay']; ?> Ngày
                    </span>
                </div>
                <div class="card-body d-flex flex-column">
                    <small class="text-muted text-uppercase fw-bold"><?php echo $tour['TenLoai']; ?></small>
                    <h5 class="card-title mt-1 mb-3">
                        <a href="<?php echo BASE_URL; ?>/home/detail/<?php echo $tour['MaTour']; ?>" class="text-decoration-none text-dark hover-primary">
                            <?php echo $tour['TenTour']; ?>
                        </a>
                    </h5>
                    
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="tour-price">Liên hệ</span> 
                            <a href="<?php echo BASE_URL; ?>/home/detail/<?php echo $tour['MaTour']; ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if($pagination['total_pages'] > 1): ?>
<nav class="mt-5">
    <ul class="pagination justify-content-center">
        <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&cat=<?php echo $cat_id; ?>&q=<?php echo $keyword; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>