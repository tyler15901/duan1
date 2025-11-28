<?php 
// 1. Định nghĩa phần nội dung chính là danh sách nhà cung cấp
// (File index_content.php là file chứa bảng HTML mà mình đã gửi ở bước trước)
$content_view = 'admin/suppliers/show_content'; 

// 2. Gọi Layout Admin chung (Header, Sidebar)
require_once '../app/Views/layouts/admin_layout.php';
?>