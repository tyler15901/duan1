<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="mb-4">
        <a href="<?= BASE_URL ?>adminSchedule/index/<?= $data['schedule']['MaTour'] ?>" class="btn btn-secondary mb-2">
            <i class="fas fa-arrow-left"></i> Quay lại Lịch trình
        </a>
        <h3 class="text-primary">Phân công Hướng Dẫn Viên (HDV)</h3>
        <div class="alert alert-info">
            <strong>Tour:</strong> <?= $data['schedule']['TenTour'] ?><br>
            <strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($data['schedule']['NgayKhoiHanh'])) ?> (Mã: <?= $data['schedule']['LichCode'] ?>)
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users"></i> Danh sách HDV đang phụ trách
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Họ tên HDV</th>
                                <th>Liên hệ</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['assigned'])): ?>
                                <?php foreach ($data['assigned'] as $item): ?>
                                    <tr>
                                        <td class="fw-bold"><?= $item['HoTen'] ?></td>
                                        <td><?= $item['SoDienThoai'] ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>adminAllocation/delete/<?= $item['MaPhanBo'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Gỡ HDV này khỏi tour?')">
                                                <i class="fas fa-trash"></i> Gỡ bỏ
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">Chưa có HDV nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-plus-circle"></i> Thêm HDV vào Tour này
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>adminAllocation/store" method="POST">
                        <input type="hidden" name="schedule_id" value="<?= $data['schedule']['MaLichKhoiHanh'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn HDV:</label>
                            <select name="staff_id" class="form-select" required>
                                <option value="">-- Chọn HDV --</option>
                                <?php foreach ($data['guides'] as $g): ?>
                                    <?php 
                                        $isAssigned = false;
                                        foreach($data['assigned'] as $as) {
                                            if($as['MaNhanSu'] == $g['MaNhanSu']) $isAssigned = true;
                                        }
                                    ?>
                                    <?php if(!$isAssigned): ?>
                                        <option value="<?= $g['MaNhanSu'] ?>">
                                            <?= $g['HoTen'] ?> (<?= $g['SoDienThoai'] ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">LƯU PHÂN CÔNG</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>