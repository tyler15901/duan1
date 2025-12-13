<?php
class GuideController extends Controller {
    
    public function __construct() {
        require_once '../app/Core/Auth.php';
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'HDV') {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    public function index() {
        $model = $this->model('GuideModel');
        $staffId = $_SESSION['staff_id'] ?? 0;
        $data = ['schedules' => $model->getMySchedules($staffId), 'content_view' => 'guide/index'];
        $this->view('layouts/guide_layout', $data);
    }

    // --- [MỚI] TRANG 1: XEM TIMELINE (DANH SÁCH CÁC NGÀY) ---
    // Thay thế cho hàm guests() cũ, giờ bấm vào tour sẽ ra trang này trước
    public function detail($id) {
        $model = $this->model('GuideModel');
        $data = $model->getScheduleWithTimeline($id);
        
        if (!$data) { echo "Không tìm thấy lịch trình."; return; }

        $viewData = [
            'schedule' => $data['info'],
            'timeline' => $data['timeline'],
            'content_view' => 'guide/schedule_detail' // View mới: Danh sách ngày
        ];
        $this->view('layouts/guide_layout', $viewData);
    }

    // --- [MỚI] TRANG 2: CHI TIẾT NGÀY (CHECK-IN, NHẬT KÝ, ẢNH) ---
    public function day($scheduleId, $dayNum) {
        $model = $this->model('GuideModel');
        $schedule = $model->getScheduleWithTimeline($scheduleId)['info'];
        $dayData = $model->getDayDetail($scheduleId, $dayNum); 

        $viewData = [
            'schedule' => $schedule,
            'dayNum'   => $dayNum,
            'realDate' => $dayData['realDate'],
            'guests'   => $dayData['guests'],
            'diary'    => $dayData['diary'],
            'photos'   => $dayData['photos'],
            'dayPlan'  => $dayData['dayPlan'], // [MỚI] Thông tin lịch trình gốc
            'content_view' => 'guide/day_detail'
        ];
        $this->view('layouts/guide_layout', $viewData);
    }

    // API Check-in theo ngày
    public function ajax_checkin_day() {
        header('Content-Type: application/json');
        $model = $this->model('GuideModel');
        
        // Nhận thêm $_POST['type'] (AM hoặc PM)
        $res = $model->toggleCheckInDay(
            $_POST['schedule_id'], 
            $_POST['guest_id'], 
            $_POST['status'], 
            $_POST['date'],
            $_POST['type']
        );
        
        echo json_encode(['success' => $res]);
    }

    // Lưu nhật ký ngày
    public function save_day_diary() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('GuideModel');
            $data = [
                'lid' => $_POST['schedule_id'],
                'uid' => $_SESSION['staff_id'],
                'day' => $_POST['day_num'],
                'title' => $_POST['tieu_de'],
                'content' => $_POST['noi_dung']
            ];
            $model->saveDailyDiary($data);
            // Quay lại đúng tab nhật ký
            header("Location: " . BASE_URL . "/guide/day/" . $_POST['schedule_id'] . "/" . $_POST['day_num'] . "?tab=diary");
        }
    }

    // Upload Ảnh
    public function upload_photo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['photo']['name'])) {
            $model = $this->model('GuideModel');
            $fileName = time() . '_' . $_FILES['photo']['name'];
            $target = '../public/assets/uploads/tours/' . $fileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $model->addPhoto($_POST['schedule_id'], $_POST['day_num'], $fileName);
            }
            // Quay lại đúng tab ảnh
            header("Location: " . BASE_URL . "/guide/day/" . $_POST['schedule_id'] . "/" . $_POST['day_num'] . "?tab=photos");
        }
    }
    
    public function delete_photo($id, $sid, $day) {
        $this->model('GuideModel')->deletePhoto($id);
        header("Location: " . BASE_URL . "/guide/day/" . $sid . "/" . $day . "?tab=photos");
    }

    public function guests($scheduleId) {
        $model = $this->model('GuideModel');
        
        // Lấy danh sách khách
        $guests = $model->getGuestsBySchedule($scheduleId);
        
        // Lấy thông tin lịch (để hiển thị tiêu đề)
        $schedule = $model->getScheduleDetail($scheduleId);
        
        $data = [
            'guests' => $guests, 
            'scheduleId' => $scheduleId,
            'schedule' => $schedule,
            
            // [QUAN TRỌNG] Phải có dòng này để Layout biết cần load file nào
            'content_view' => 'guide/guests' 
        ];
        
        $this->view('layouts/guide_layout', $data);
    }
}
?>