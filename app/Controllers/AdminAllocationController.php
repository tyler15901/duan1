<?php
class AdminAllocationController extends Controller {
    
    public function __construct() {
        if (!Session::isAdmin()) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
    }

    // Trang phân công cho 1 Lịch cụ thể (scheduleId)
    public function index($scheduleId) {
        $allocModel = $this->model('AllocationModel');
        $tourModel = $this->model('TourModel'); // Để lấy thông tin lịch & tour
        $staffModel = $this->model('StaffModel'); // Để lấy danh sách HDV

        // 1. Lấy thông tin Lịch để hiển thị tiêu đề
        // (Tái sử dụng hàm getBookingInfo của BookingModel hoặc viết query mới cũng được)
        // Ở đây ta query nhanh để lấy ngày và tên tour
        $db = Database::connect();
        $sql = "SELECT lkh.*, t.TenTour FROM lichkhoihanh lkh 
                JOIN tour t ON lkh.MaTour = t.MaTour 
                WHERE lkh.MaLichKhoiHanh = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $scheduleId]);
        $schedule = $stmt->fetch();

        if (!$schedule) die("Lịch khởi hành không tồn tại");

        // 2. Lấy danh sách HDV ĐÃ phân công
        $assigned = $allocModel->getAssignedStaff($scheduleId);

        // 3. Lấy danh sách HDV CHƯA phân công (để hiện trong dropdown)
        // Ta lấy toàn bộ HDV, loại trừ những người đã có trong mảng $assigned
        // Hoặc đơn giản nhất: Lấy tất cả HDV hoạt động
        $allGuides = $staffModel->getAllStaff('HDV');

        $this->view('admin/allocation/index', [
            'schedule' => $schedule,
            'assigned' => $assigned,
            'guides'   => $allGuides,
            'title'    => 'Phân công HDV'
        ]);
    }

    // Xử lý Lưu
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $scheduleId = $_POST['schedule_id'];
            $staffId = $_POST['staff_id'];

            $allocModel = $this->model('AllocationModel');
            $allocModel->assignStaff($scheduleId, $staffId);

            header('Location: ' . BASE_URL . 'adminAllocation/index/' . $scheduleId);
        }
    }

    // Xử lý Xóa
    public function delete($id) {
        // Cần lấy scheduleId trước để redirect về
        $allocModel = $this->model('AllocationModel');
        
        // Lấy ID lịch
        $db = Database::connect();
        $stmt = $db->prepare("SELECT MaLichKhoiHanh FROM phanbonhansu WHERE MaPhanBo = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $allocModel->removeAssignment($id);
            header('Location: ' . BASE_URL . 'adminAllocation/index/' . $row['MaLichKhoiHanh']);
        } else {
            header('Location: ' . BASE_URL . 'adminTour/index');
        }
    }
}