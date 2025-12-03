<?php
class GuideController extends Controller {
    
    public function __construct() {
        require_once '../app/Core/Auth.php';
        // Kiểm tra quyền: Phải đăng nhập VÀ phải là HDV
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'HDV') {
            // Chặn cứng nếu không phải HDV (hoặc cho Admin xem ké nếu muốn)
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    // 1. Dashboard: Danh sách Tour
    public function index() {
        $model = $this->model('GuideModel');
        $staffId = $_SESSION['staff_id'] ?? 0; 

        if ($staffId == 0) {
            echo "Lỗi: Tài khoản chưa liên kết nhân sự.";
            exit;
        }

        $schedules = $model->getMySchedules($staffId);
        
        // [SỬA LẠI ĐOẠN NÀY]
        $data = [
            'schedules' => $schedules,
            'content_view' => 'guide/index' // Báo cho layout biết cần load file nào
        ];
        // Gọi Layout thay vì gọi view con trực tiếp
        $this->view('layouts/guide_layout', $data);
    }

    // 2. Danh sách khách
    public function guests($scheduleId) {
        $model = $this->model('GuideModel');
        $guests = $model->getGuestsBySchedule($scheduleId);
        
        // [SỬA LẠI ĐOẠN NÀY]
        $data = [
            'guests' => $guests, 
            'scheduleId' => $scheduleId,
            'content_view' => 'guide/guests' // Load view danh sách khách
        ];
        $this->view('layouts/guide_layout', $data);
    }

    // API: Xử lý Check-in (AJAX gọi vào đây)
    public function ajax_checkin() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('GuideModel');
            
            $status = $_POST['status']; // 'check' hoặc 'uncheck'
            $res = $model->toggleCheckIn($_POST['schedule_id'], $_POST['guest_id'], $status);
            
            echo json_encode(['success' => $res]);
            exit;
        }
    }

    // Xử lý Form: Cập nhật ghi chú/yêu cầu đặc biệt
    public function update_request() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('GuideModel');
            $userId = $_SESSION['user_id']; // Người xử lý là User đang đăng nhập
            
            $model->updateSpecialRequest($_POST['guest_id'], $_POST['content'], $userId);
            
            header("Location: " . BASE_URL . "/guide/guests/" . $_POST['schedule_id']);
        }
    }

    // 3. Nhật ký Tour
    public function diary($scheduleId) {
        $model = $this->model('GuideModel');
        
        // Xử lý thêm mới nhật ký
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'lid' => $scheduleId,
                'uid' => $_SESSION['staff_id'] ?? 0,
                'title' => $_POST['tieu_de'],
                'content' => $_POST['noi_dung']
            ];
            $model->addDiary($data);
            header("Location: " . BASE_URL . "/guide/diary/" . $scheduleId);
            exit;
        }

        $logs = $model->getDiaries($scheduleId);
        $data = [
            'logs' => $logs, 
            'scheduleId' => $scheduleId,
            'content_view' => 'guide/diary' // Load view nhật ký
        ];
        $this->view('layouts/guide_layout', $data);
    }
    
    public function delete_diary($id, $scheduleId) {
        $model = $this->model('GuideModel');
        $model->deleteDiary($id);
        header("Location: " . BASE_URL . "/guide/diary/" . $scheduleId);
    }
}
?>