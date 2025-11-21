<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên - AT Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        .auth-logo { text-align: center; margin-bottom: 20px; font-size: 24px; font-weight: bold; color: #0066CC; }
        .auth-title { text-align: center; color: #333; margin-bottom: 25px; font-size: 20px; font-weight: 600; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: 0.3s; }
        .form-control:focus { border-color: #0066CC; outline: none; }
        
        .btn-submit { width: 100%; padding: 12px; background: #FF6600; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #e55a00; }
        
        .error-msg { background: #ffebee; color: #c62828; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #ffcdd2; }
        
        .auth-footer { text-align: center; margin-top: 25px; font-size: 14px; color: #666; }
        .auth-footer a { color: #0066CC; font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="auth-box">
        <div class="auth-logo">
            <i class="fas fa-paper-plane"></i> AT Travel
        </div>
        <div class="auth-title">Đăng Ký Tài Khoản</div>
        
        <?php if(!empty($data['error'])): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?= $data['error'] ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên của bạn" required>
            </div>
            
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" placeholder="Viết liền không dấu (vd: user123)" required>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required>
            </div>
            
            <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
        </form>
        
        <div class="auth-footer">
            Đã có tài khoản? <a href="<?= BASE_URL ?>auth/login">Đăng nhập ngay</a>
            <br><br>
            <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Quay về trang chủ</a>
        </div>
    </div>

</body>
</html>