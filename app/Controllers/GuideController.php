<?php
class GuideController extends Controller {
    
    public function __construct() {
        // 1. Chặn nếu chưa đăng nhập
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // 2. Chặn nếu không phải HDV (Dùng hàm isGuide trong Session đã viết)
        if (!Session::isGuide()) {
            die("Bạn không có quyền truy cập trang này!");
        }
    }

    // Trang Dashboard của HDV
    public function dashboard() {
        // Lấy tên người đang đăng nhập
        $myName = Session::get('user_name');

        // Gọi Model lấy lịch
        $allocModel = $this->model('AllocationModel');
        $mySchedules = $allocModel->getMySchedules($myName);

        $this->view('guide/dashboard', [
            'schedules' => $mySchedules,
            'guideName' => $myName,
            'title' => 'Cổng thông tin Hướng Dẫn Viên'
        ]);
    }
}