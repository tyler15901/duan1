<?php
session_start();

// 1. Nạp file cấu hình
require_once '../config/config.php';

// 2. Nạp các file Core (Thủ công để dễ hiểu, sau này có thể dùng Composer Autoload)
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/App.php'; // App phải load cuối cùng sau khi có đủ thư viện

// 3. Khởi chạy ứng dụng
$app = new App();
?>