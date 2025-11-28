<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 400px; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4 text-primary">TOUR BOOKING</h3>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="alert alert-success">Đăng ký thành công! Vui lòng đăng nhập.</div>
        <?php endif; ?>

        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger"><?php echo $data['error']; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Đăng nhập</button>
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>/auth/register" class="text-decoration-none">Chưa có tài khoản? Đăng ký ngay</a>
            </div>
            <div class="text-center mt-2">
                <a href="<?php echo BASE_URL; ?>" class="text-muted small">Quay về trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>