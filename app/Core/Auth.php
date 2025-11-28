<?php
class Auth {
    // Kiểm tra xem đã đăng nhập chưa
    public static function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    // Kiểm tra có phải Admin hoặc HDV không
   public static function checkAdmin() {
        self::checkLogin(); // Phải login trước đã
        
        // Lấy vai trò (nếu chưa có thì gán rỗng để không bị lỗi Undefined array key)
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

        // Kiểm tra quyền
        if ($role !== 'ADMIN' && $role !== 'HDV') {
            echo "Bạn không có quyền truy cập trang này! (Vai trò của bạn: $role)";
            echo "<br><a href='".BASE_URL."/auth/logout'>Đăng xuất</a>"; // Gợi ý đăng xuất để đăng nhập lại
            exit;
        }
    }
}
?>