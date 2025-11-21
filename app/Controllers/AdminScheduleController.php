<?php
class AdminScheduleController extends Controller {
    
    public function __construct() {
        if (!Session::isAdmin()) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
    }

    // Xem danh sách lịch của Tour ID nào đó
    public function index($tourId) {
        $tourModel = $this->model('TourModel');
        
        // Lấy thông tin tour để hiện tên
        $tour = $tourModel->find($tourId);
        if (!$tour) die('Tour không tồn tại');

        // Lấy danh sách lịch của tour này
        $schedules = $tourModel->getSchedules($tourId);

        $this->view('admin/schedule/index', [
            'tour' => $tour,
            'schedules' => $schedules,
            'title' => 'Quản lý lịch: ' . $tour['TenTour']
        ]);
    }

    // Lưu lịch mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tourId = $_POST['tour_id'];
            
            $data = [
                'MaTour' => $tourId,
                'NgayKhoiHanh' => $_POST['start_date'],
                'GioTapTrung' => $_POST['time'],
                'DiaDiemTapTrung' => $_POST['location']
            ];

            $tourModel = $this->model('TourModel');
            $tourModel->createSchedule($data);

            // Quay lại trang danh sách lịch của tour đó
            header('Location: ' . BASE_URL . 'adminSchedule/index/' . $tourId . '?msg=success');
        }
    }

    // Xóa lịch
    public function delete($id) {
        $tourModel = $this->model('TourModel');
        
        // Cần lấy ID tour trước khi xóa để redirect về đúng chỗ
        $schedule = $tourModel->getScheduleById($id);
        if ($schedule) {
            $tourModel->deleteSchedule($id);
            header('Location: ' . BASE_URL . 'adminSchedule/index/' . $schedule['MaTour']);
        } else {
            header('Location: ' . BASE_URL . 'adminTour/index'); // Fallback
        }
    }
}