<?php
class TourController extends Controller {
    
    public function detail($id = null) {
        // 1. Kiểm tra tham số ID
        if (!$id) {
            // Nếu không có ID thì đá về trang chủ
            header('Location: ' . BASE_URL);
            exit;
        }

        // 2. Gọi Model
        $tourModel = $this->model('TourModel');

        // 3. Lấy dữ liệu
        $tour = $tourModel->getById($id);
        
        // Nếu ID không tồn tại trong DB -> Báo lỗi
        if (!$tour) {
            die("Tour không tồn tại!"); 
        }

        // Lấy thêm dữ liệu phụ
        $lichTrinh = $tourModel->getItinerary($id);
        $lichKhoiHanh = $tourModel->getSchedules($id);

        // 4. Gọi View
        $this->view('tour/detail', [
            'tour' => $tour,
            'itinerary' => $lichTrinh,
            'schedules' => $lichKhoiHanh,
            'title' => $tour['TenTour'] . ' - Chi tiết'
        ]);
    }
}