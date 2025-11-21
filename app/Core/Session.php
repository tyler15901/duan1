<?php
class Session {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setLogin($user) {
        self::init();
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['MaNguoiDung'];
        $_SESSION['user_name'] = $user['HoTen'];
        $_SESSION['user_role'] = $user['VaiTro']; // Giá trị: 'ADMIN', 'HDV', 'KhachHang'
        $_SESSION['user_avatar'] = $user['Avatar'] ?? null;
    }

    public static function logout() {
        self::init();
        session_destroy();
        // Xóa sạch biến
        $_SESSION = [];
    }

    public static function isLoggedIn() {
        self::init();
        return !empty($_SESSION['user_logged_in']);
    }

    // --- CÁC HÀM KIỂM TRA VAI TRÒ (Role Check) ---
    
    public static function isAdmin() {
        self::init();
        return self::isLoggedIn() && $_SESSION['user_role'] === 'ADMIN';
    }

    public static function isGuide() { // Kiểm tra HDV
        self::init();
        // Trong DB bạn lưu là 'HDV' hay 'Nhân viên' thì sửa lại cho khớp nhé
        // Theo ảnh cũ của bạn là 'HDV'
        return self::isLoggedIn() && $_SESSION['user_role'] === 'HDV';
    }

    public static function isCustomer() {
        self::init();
        return self::isLoggedIn() && $_SESSION['user_role'] === 'KhachHang';
    }

    public static function get($key) {
        self::init();
        return $_SESSION[$key] ?? null;
    }
}