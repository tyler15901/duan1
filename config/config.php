<?php
// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Điền mật khẩu của bạn
define('DB_NAME', 'duan1');

// Đường dẫn gốc (Thay đổi theo tên thư mục của bạn)
// Nếu bạn chạy localhost/duan1/public thì để như dưới
define('BASE_URL', 'http://localhost/duan1/public');

// Đường dẫn tuyệt đối đến thư mục app
define('APP_ROOT', dirname(dirname(__FILE__)) . '/app');
?>