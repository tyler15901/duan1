<?php
class AdminController extends Controller {
    public function __construct() {
        // Chặn ngay từ cửa: Chỉ ADMIN mới được vào
        if (!Session::isAdmin()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function dashboard() {
        // Gọi View giao diện Admin (Thường là Dashboard thống kê)
        $this->view('admin/dashboard', ['title' => 'Trang Quản Trị Admin']);
    }
    
    // Các hàm khác: manageTours, manageUsers...
}