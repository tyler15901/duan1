<?php
// app/Controllers/HomeController.php
class HomeController extends Controller {
    
    public function index() {
        // 1. Gọi Model
        $tourModel = $this->model('TourModel');

        // 2. Lấy dữ liệu (Lấy 8 tour mới nhất)
        $danhSachTour = $tourModel->getTourList(8);

        // 3. Gửi sang View
        $this->view('home/index', [
            'tours' => $danhSachTour,
            'title' => 'Trang chủ - Du Lịch Việt',
        ]);
    }
}