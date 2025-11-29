<?php
class HomeController extends Controller {
    
    public function index() {
        $tourModel = $this->model('TourModel');

        // 1. Nhận tham số tìm kiếm từ URL (nếu khách tìm)
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
        
        // 2. Cấu hình bộ lọc
        $filters = [
            'keyword' => $keyword,
            'category_id' => $cat_id,
            'status' => 'Hoạt động' // Quan trọng: Chỉ hiện tour đang hoạt động
        ];

        // 3. Phân trang (mặc định 6 tour/trang)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // 4. Lấy dữ liệu
        // Lưu ý: Bạn cần vào TourModel sửa hàm getToursFiltered để nhận thêm tham số 'status' nhé!
        // (Xem hướng dẫn sửa Model bên dưới)
        $tours = $tourModel->getToursFiltered($filters, $limit, $offset);
        $categories = $tourModel->getCategories();
        
        // Tính toán phân trang
        $total_records = $tourModel->countTours($filters);
        $total_pages = ceil($total_records / $limit);

        $data = [
            'tours' => $tours,
            'categories' => $categories,
            'keyword' => $keyword,
            'cat_id' => $cat_id,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages
            ]
        ];

        // Gọi View Client
        $this->view('client/home', $data);
    }

    // Trang chi tiết Tour (Xem thông tin + Lịch khởi hành)
    public function detail($id) {
        $tourModel = $this->model('TourModel');
        $scheduleModel = $this->model('ScheduleModel'); // Cần dùng để hiện lịch chạy

        $data = [
            'tour' => $tourModel->getTourById($id),
            'gallery' => $tourModel->getGallery($id),
            'schedules' => $scheduleModel->getSchedulesFiltered(['tour_id' => $id], 100, 0), // Lấy 100 lịch sắp tới
            'prices' => $tourModel->getPrices($id),
            'schedule_detail' => $tourModel->getSchedule($id) // Lịch trình chi tiết (ngày 1, ngày 2)
        ];

        $this->view('client/detail', $data);
    }
}
?>