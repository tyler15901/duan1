<?php
// 1. Định nghĩa phần nội dung chính (là file chứa Form nhập liệu)
$content_view = 'admin/tours/create_content'; 

// 2. Gọi Layout chung của Admin (Header, Sidebar, Footer)
// Layout này sẽ nhúng biến $content_view ở trên vào giữa trang
require_once '../app/Views/layouts/admin_layout.php';
?>