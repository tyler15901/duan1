<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .form-control:focus {
            background: #fff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn-login {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            background: #2563eb;
            border: none;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-icon">
            <i class="bi bi-airplane-engines-fill"></i>
        </div>
        <h4 class="text-center fw-bold text-dark mb-1">TRAVEL PRO</h4>
        <p class="text-center text-muted mb-4 small">Đăng nhập để quản lý hệ thống</p>
        
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger py-2 text-center small border-0 bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-exclamation-circle me-1"></i> <?php echo $data['error']; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Username" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">Đăng nhập</button>
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>" class="text-decoration-none small text-muted hover-primary">
                    <i class="bi bi-arrow-left"></i> Quay về trang chủ
                </a>
            </div>
        </form>
    </div>
</body>
</html>