<?php
// public/index.php
session_start();

// 1. Nạp cấu hình
require_once '../config/config.php';

// 2. Nạp các file Core (Lõi)
require_once '../app/Core/Database.php';
require_once '../app/Core/BaseModel.php';
require_once '../app/Core/Controller.php'; // Tạo file này ở bước sau
require_once '../app/Core/App.php';        // Tạo file này ở bước sau

// 3. Khởi chạy ứng dụng
$app = new App();