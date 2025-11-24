<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-user-shield"></i> ADMIN PANEL</a>
            <div class="d-flex">
                <span class="text-white me-3">Xin chào, <?= Session::get('user_name') ?></span>
                <a href="<?= BASE_URL ?>" class="btn btn-sm btn-outline-light">Về trang chủ</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4"><i class="fas fa-list-alt"></i> Quản Lý Đặt Tour</h2>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Mã BK</th>
                            <th>Khách Hàng</th>
                            <th>Tour Đặt</th>
                            <th>Ngày Đi</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['bookings'])): ?>
                            <?php foreach ($data['bookings'] as $bk): ?>
                                <tr>
                                    <td><strong><?= $bk['MaBookingCode'] ?></strong></td>
                                    <td>
                                        <?= $bk['TenKhach'] ?><br>
                                        <small class="text-muted"><?= $bk['SoDienThoai'] ?></small>
                                    </td>
                                    <td style="max-width: 250px;"><?= $bk['TenTour'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($bk['NgayKhoiHanh'])) ?></td>
                                    <td class="text-danger fw-bold"><?= number_format($bk['TongTien'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <?php 
                                            $stt = $bk['TrangThai'];
                                            $badge = 'bg-secondary';
                                            if($stt == 'Chờ xác nhận') $badge = 'bg-warning text-dark';
                                            if($stt == 'Đã cọc') $badge = 'bg-info text-dark';
                                            if($stt == 'Hoàn tất') $badge = 'bg-success';
                                            if($stt == 'Hủy') $badge = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= $stt ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>admin/booking_detail/<?= $bk['MaBooking'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Chưa có đơn hàng nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>