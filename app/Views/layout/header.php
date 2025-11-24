<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'Du Lịch Việt'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>">
                        <i class="fas fa-paper-plane"></i> AT Travel
                    </a>
                </div>
                <nav class="menu">
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
                        <li><a href="<?php echo BASE_URL; ?>tour/list?type=1">Trong nước</a></li>
                        <li><a href="<?php echo BASE_URL; ?>tour/list?type=2">Nước ngoài</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
    <?php if(Session::isLoggedIn()): // Dùng Class Session chuẩn ?>
        
        <a href="#" class="btn-login">
            <i class="fas fa-user"></i> <?= Session::get('user_name') ?>
        </a>
        <a href="<?= BASE_URL . 'auth/logout' ?>" class="btn-register" style="background: #666;">Đăng xuất</a>

    <?php else: ?>
        
        <a href="<?= BASE_URL . 'auth/login' ?>" class="btn-login">Đăng nhập</a>
        <a href="<?= BASE_URL . 'auth/register' ?>" class="btn-register">Đăng ký</a>

    <?php endif; ?>
</div>
            </div>
        </div>
    </header>
    
    <main>