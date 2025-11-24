<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2>📋 Danh sách Tour</h2>
            <div>
                <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-secondary">Về Dashboard</a>
                <a href="<?= BASE_URL ?>adminTour/create" class="btn btn-success">+ Thêm Tour Mới</a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên Tour</th>
                            <th>Giá (Hiện tại)</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['tours'] as $t): ?>
                            <tr>
                                <td><?= $t['MaTour'] ?></td>
                                <td>
                                    <img src="<?= BASE_URL . 'uploads/' . $t['HinhAnh'] ?>" width="80" height="50"
                                        style="object-fit:cover; border-radius:4px;">
                                </td>
                                <td style="max-width: 300px;"><?= $t['TenTour'] ?></td>
                                <td class="text-danger fw-bold">
                                    <?= !empty($t['GiaHienTai']) ? number_format($t['GiaHienTai']) . ' đ' : 'Chưa có giá' ?>
                                </td>
                                <td><?= $t['SoNgay'] ?> ngày</td>
                                <td>
                                    <span class="badge bg-<?= $t['TrangThai'] == 'Hoạt động' ? 'primary' : 'secondary' ?>">
                                        <?= $t['TrangThai'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>adminTour/edit/<?= $t['MaTour'] ?>"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="<?= BASE_URL ?>adminSchedule/index/<?= $t['MaTour'] ?>"
                                        class="btn btn-sm btn-info text-white" title="Thêm ngày khởi hành">
                                        <i class="fas fa-calendar-plus"></i> Lịch
                                    </a>
                                    <a href="<?= BASE_URL ?>adminTour/delete/<?= $t['MaTour'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>