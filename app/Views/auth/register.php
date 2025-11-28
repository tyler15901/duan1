<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 450px; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4 text-success">ĐĂNG KÝ THÀNH VIÊN</h3>
        
        <?php if (!empty($data['errors'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach($data['errors'] as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nhập lại MK</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100 mb-2">Đăng ký</button>
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>/auth/login" class="text-decoration-none">Đã có tài khoản? Đăng nhập</a>
            </div>
        </form>
    </div>
</body>
</html>