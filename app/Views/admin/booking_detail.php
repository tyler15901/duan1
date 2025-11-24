<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-secondary mb-3">← Quay lại danh sách</a>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">Cập nhật trạng thái thành công!</div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Thông tin Booking: <?= $data['booking']['MaBookingCode'] ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tour:</strong> <?= $data['booking']['TenTour'] ?></p>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($data['booking']['NgayKhoiHanh'])) ?></p>
                        <p><strong>Số lượng:</strong> <?= $data['booking']['SoLuongKhach'] ?> khách</p>
                        <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold fs-5"><?= number_format($data['booking']['TongTien']) ?> đ</span></p>
                        <p><strong>Ghi chú:</strong> <?= $data['booking']['GhiChu'] ?></p>
                        <p><strong>Ngày đặt:</strong> <?= $data['booking']['NgayDat'] ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Thông tin khách hàng</div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> <?= $data['booking']['HoTen'] ?></p>
                        <p><strong>SĐT:</strong> <?= $data['booking']['SoDienThoai'] ?></p>
                        <p><strong>Email:</strong> <?= $data['booking']['Email'] ?></p>
                        <p><strong>Địa chỉ:</strong> <?= $data['booking']['DiaChi'] ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">Xử lý đơn hàng</div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>admin/update_status" method="POST">
                            <input type="hidden" name="booking_id" value="<?= $data['booking']['MaBooking'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                                <select name="status" class="form-select">
                                    <?php 
                                        $current = $data['booking']['TrangThai'];
                                        $statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đã cọc', 'Hoàn tất', 'Hủy'];
                                        foreach($statuses as $stt): 
                                    ?>
                                        <option value="<?= $stt ?>" <?= $current==$stt ? 'selected' : '' ?>>
                                            <?= $stt ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Cập nhật trạng thái</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>