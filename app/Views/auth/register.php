<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            /* Dùng chung background với trang Login */
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 500px; /* Rộng hơn login một chút để chứa 2 cột */
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: #2eca6a; /* Đổi sang màu xanh lá để phân biệt với Login */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.3);
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-left: 0;
        }
        .form-control:focus {
            background: #fff;
            border-color: #2eca6a;
            box-shadow: none;
        }
        .input-group-text {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 0;
            color: #64748b;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .input-group:focus-within .input-group-text {
            border-color: #2eca6a;
            background: #fff;
            color: #2eca6a;
        }
        .btn-register {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            background: #2eca6a;
            border: none;
            transition: all 0.3s;
        }
        .btn-register:hover {
            background: #25a055;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-icon">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <h4 class="text-center fw-bold text-dark mb-1">ĐĂNG KÝ MỚI</h4>
        <p class="text-center text-muted mb-4 small">Tham gia cùng Travel Pro ngay hôm nay</p>
        
        <?php if (!empty($data['errors'])): ?>
            <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger mb-4">
                <ul class="mb-0 ps-3">
                    <?php foreach($data['errors'] as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Họ và tên</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên đầy đủ" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Chọn tên đăng nhập" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label small fw-bold text-secondary">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="******" required>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label small fw-bold text-secondary">Nhập lại MK</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="confirm_password" class="form-control" placeholder="******" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-register w-100 mb-3 text-white">Xác nhận Đăng ký</button>
            
            <div class="text-center">
                <span class="text-muted small">Đã có tài khoản?</span>
                <a href="<?php echo BASE_URL; ?>/auth/login" class="text-decoration-none fw-bold text-success ms-1">
                    Đăng nhập ngay
                </a>
            </div>
        </form>
    </div>
</body>
</html>