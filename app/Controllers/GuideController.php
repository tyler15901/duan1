<?php
class GuideController extends Controller {
    public function __construct() {
        // Chặn ngay từ cửa: Chỉ HDV mới được vào
        if (!Session::isGuide()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function dashboard() {
        // Gọi View giao diện dành riêng cho HDV (Lịch trình của tôi)
        $this->view('guide/dashboard', ['title' => 'Cổng thông tin HDV']);
    }
}