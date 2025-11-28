<?php
// 1. Định nghĩa nội dung chính là trang home_content (chứa banner, danh sách tour...)
// File home_content.php là file dài mà mình đã gửi ở câu trả lời trước
$content_view = 'client/home_content'; 

// 2. Gọi Layout Client (chứa Header, Footer, Menu)
require_once '../app/Views/layouts/client_layout.php';
?>