<?php 
    // SỬ DỤNG ĐƯỜNG DẪN TUYỆT ĐỐI ĐỂ TRÁNH LỖI INCLUDE
    require_once dirname(__DIR__) . '/layout/header.php'; 
?>

<style>
    /* CSS RIÊNG CHO TRANG BOOKING */
    .booking-page { padding: 50px 0; background: #f9f9f9; min-height: 80vh; }
    .booking-wrapper { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
    
    /* Card Box Style */
    .card-box { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .box-title { font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #0066CC; }
    
    /* Form Elements */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 15px; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0066CC; outline: none; }
    
    /* Radio Payment */
    .payment-option { display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 10px; padding: 10px; border: 1px solid #eee; border-radius: 5px; }
    .payment-option:hover { background: #f0f8ff; border-color: #0066CC; }
    
    /* Summary Box (Cột phải) */
    .tour-summary img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
    .tour-summary h4 { font-size: 16px; font-weight: bold; line-height: 1.4; margin-bottom: 15px; color: #333; }
    
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #666; border-bottom: 1px dashed #eee; padding-bottom: 12px; }
    .summary-row strong { color: #333; }
    
    .summary-total { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center; }
    .summary-total span:first-child { display: block; font-size: 14px; color: #666; margin-bottom: 5px; }
    .summary-total span:last-child { font-size: 22px; font-weight: 800; color: #FF6600; }
    
    .btn-confirm { width: 100%; padding: 15px; background: #FF6600; color: white; font-weight: bold; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s; margin-top: 20px; }
    .btn-confirm:hover { background: #e55a00; box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3); }

    /* Responsive */
    @media (max-width: 768px) { .booking-wrapper { grid-template-columns: 1fr; } }
</style>

<div class="booking-page">
    <div class="container">
        <form action="<?= BASE_URL ?>booking/store" method="POST" class="booking-wrapper">
            
            <input type="hidden" name="lich_id" value="<?= $data['info']['MaLichKhoiHanh'] ?>">
            <input type="hidden" name="adult_qty" value="<?= $data['adult'] ?>">
            <input type="hidden" name="child_qty" value="<?= $data['child'] ?>">

            <div class="left-col">
                <div class="card-box">
                    <h3 class="box-title"><i class="far fa-user"></i> 1. Thông tin liên hệ</h3>
                    
                    <div class="form-group">
                        <label>Họ và tên <span style="color:red">*</span></label>
                        <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Số điện thoại <span style="color:red">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="09xxxx" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" name="address" class="form-control" placeholder="Số nhà, đường, phường/xã...">
                    </div>
                    
                    <div class="form-group">
                        <label>Ghi chú thêm</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Có người ăn chay, cần xuất hóa đơn..."></textarea>
                    </div>
                </div>

                <div class="card-box">
                    <h3 class="box-title"><i class="far fa-credit-card"></i> 2. Phương thức thanh toán</h3>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="tien_mat" checked>
                        <span>Thanh toán tiền mặt tại văn phòng</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="chuyen_khoan">
                        <span>Chuyển khoản ngân hàng</span>
                    </label>
                </div>
            </div>

            <div class="right-col">
                <div class="card-box" style="position: sticky; top: 90px;">
                    <h3 class="box-title">Tóm tắt chuyến đi</h3>
                    
                    <div class="tour-summary">
                        <img src="<?= BASE_URL . 'uploads/' . ($data['info']['HinhAnh'] ?? '') ?>" 
                             alt="Tour Image" 
                             onerror="this.src='https://placehold.co/400x250?text=No+Image'">
                        
                        <h4><?= htmlspecialchars($data['info']['TenTour']) ?></h4>
                        
                        <div class="summary-row">
                            <span><i class="far fa-calendar-alt"></i> Khởi hành:</span>
                            <strong><?= date('d/m/Y', strtotime($data['info']['NgayKhoiHanh'])) ?></strong>
                        </div>
                        <div class="summary-row">
                            <span><i class="far fa-clock"></i> Thời gian:</span>
                            <strong><?= $data['info']['SoNgay'] ?> ngày</strong>
                        </div>
                        <div class="summary-row">
                            <span><i class="fas fa-users"></i> Khách:</span>
                            <strong><?= $data['adult'] ?> Lớn, <?= $data['child'] ?> Trẻ em</strong>
                        </div>
                        
                        <div class="summary-total">
                            <span>TỔNG CỘNG</span>
                            <span><?= number_format($data['total'], 0, ',', '.') ?> ₫</span>
                        </div>
                        
                        <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT TOUR</button>
                        
                        <p style="text-align: center; margin-top: 15px; font-size: 13px; color: #888;">
                            Nhấn xác nhận đồng nghĩa bạn đồng ý với điều khoản của chúng tôi.
                        </p>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>