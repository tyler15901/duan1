<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - AT Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .auth-logo { text-align: center; margin-bottom: 30px; font-size: 24px; font-weight: bold; color: #0066CC; }
        .auth-title { text-align: center; color: #333; margin-bottom: 20px; font-size: 20px; font-weight: 600; }
        
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        .form-group i { position: absolute; top: 40px; left: 15px; color: #999; }
        .form-control { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: 0.3s; }
        .form-control:focus { border-color: #0066CC; outline: none; }
        
        .btn-submit { width: 100%; padding: 12px; background: #FF6600; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-submit:hover { background: #e55a00; }
        
        .error-msg { background: #ffebee; color: #c62828; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #ffcdd2; }
        .success-msg { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #c8e6c9; }
        
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
        <div class="auth-title">Đăng Nhập Hệ Thống</div>
        
        <?php if(!empty($data['error'])): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?= $data['error'] ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg']=='success'): ?>
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> Đăng ký thành công! Vui lòng đăng nhập.
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control" placeholder="Nhập username" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            
            <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
        </form>
        
        <div class="auth-footer">
            Chưa có tài khoản? <a href="<?= BASE_URL ?>auth/register">Đăng ký ngay</a>
            <br><br>
            <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Quay về trang chủ</a>
        </div>
    </div>

</body>
</html>