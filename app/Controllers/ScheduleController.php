<?php
class ScheduleController extends Controller {

    public function index() {
        $model = $this->model('ScheduleModel');
        
        // 1. Lấy tour_id từ URL (nếu có)
        $tour_id = isset($_GET['tour_id']) ? $_GET['tour_id'] : null;
        
        // 2. Gọi Model lấy danh sách (đã lọc hoặc lấy hết)
        $schedules = $model->getAllSchedules($tour_id);
        
        // 3. Lấy tên tour để hiển thị thông báo (nếu đang lọc)
        $filter_title = "";
        if($tour_id) {
            $tourName = $model->getTourName($tour_id);
            $filter_title = "Đang hiển thị lịch của tour: " . $tourName;
        }

        // 4. Truyền dữ liệu sang View
        $data = [
            'schedules' => $schedules,
            'filter_msg' => $filter_title, // Biến thông báo
            'current_tour_id' => $tour_id  // Để giữ trạng thái form tìm kiếm nếu cần
        ];

        $this->view('admin/schedules/index', $data);
    }

    public function create() {
        $model = $this->model('ScheduleModel');
        
        // Lấy dữ liệu cần thiết để hiển thị Form
        $data = [
            'tours' => $model->getTours(),
            'staffs' => $model->getStaffs(),
            'resources' => $model->getResources()
        ];
        
        $this->view('admin/schedules/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ScheduleModel');

            // Gom dữ liệu từ Form
            $mainData = [
                'tour_id' => $_POST['tour_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'meeting_time' => $_POST['meeting_time'],
                'meeting_place' => $_POST['meeting_place']
            ];

            // Mảng ID tài nguyên (Xe, KS)
            $resources = isset($_POST['resources']) ? $_POST['resources'] : [];
            
            // Mảng ID nhân sự (HDV)
            $staffs = isset($_POST['staffs']) ? $_POST['staffs'] : [];

            if ($model->createSchedule($mainData, $resources, $staffs)) {
                header("Location: " . BASE_URL . "/schedule/index");
            } else {
                echo "<script>alert('Lỗi tạo lịch! Vui lòng kiểm tra lại.'); window.history.back();</script>";
            }
        }
    }
    // --- XEM CHI TIẾT ---
    public function show($id) {
        $model = $this->model('ScheduleModel');
        $data = [
            'schedule' => $model->getScheduleById($id),
            'resources' => $model->getAssignedResourcesDetail($id),
            'staffs' => $model->getAssignedStaffsDetail($id)
        ];
        $this->view('admin/schedules/show', $data);
    }

    // --- HIỂN THỊ FORM SỬA ---
    public function edit($id) {
        $model = $this->model('ScheduleModel');
        
        $data = [
            'schedule' => $model->getScheduleById($id),
            'tours' => $model->getTours(),
            'all_staffs' => $model->getStaffs(),      // Tất cả nhân viên để chọn
            'all_resources' => $model->getResources(), // Tất cả tài nguyên để chọn
            'assigned_staffs' => $model->getAssignedStaffIds($id),     // Mảng ID đã chọn [1, 2]
            'assigned_resources' => $model->getAssignedResourceIds($id) // Mảng ID đã chọn [5, 6]
        ];
        
        $this->view('admin/schedules/edit', $data);
    }

    // --- XỬ LÝ CẬP NHẬT ---
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ScheduleModel');

            $mainData = [
                'tour_id' => $_POST['tour_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'meeting_time' => $_POST['meeting_time'],
                'meeting_place' => $_POST['meeting_place'],
                'status' => $_POST['status']
            ];

            $resources = isset($_POST['resources']) ? $_POST['resources'] : [];
            $staffs = isset($_POST['staffs']) ? $_POST['staffs'] : [];

            if ($model->updateSchedule($id, $mainData, $resources, $staffs)) {
                header("Location: " . BASE_URL . "/schedule/show/" . $id);
            } else {
                echo "Lỗi cập nhật!";
            }
        }
    }

    // --- XÓA LỊCH ---
    public function delete($id) {
        $model = $this->model('ScheduleModel');
        if($model->deleteSchedule($id)) {
            header("Location: " . BASE_URL . "/schedule/index");
        } else {
            echo "<script>alert('Không thể xóa lịch này vì đã có Booking phát sinh!'); window.location.href='".BASE_URL."/schedule/index';</script>";
        }
    }

    // --- XEM DANH SÁCH KHÁCH CỦA LỊCH ---
    public function guests($id) {
        $model = $this->model('ScheduleModel');
        
        // 1. Lấy thông tin lịch (để hiện tiêu đề: Danh sách khách của lịch ABC...)
        $schedule = $model->getScheduleById($id);
        
        // 2. Lấy danh sách booking của lịch này
        $bookings = $model->getBookingsBySchedule($id);
        
        // 3. Tính tổng số khách thực tế
        $total_guests = 0;
        foreach($bookings as $b) {
            // Chỉ tính những đơn đã xác nhận/thanh toán (tránh đơn hủy)
            if($b['TrangThai'] != 'Đã hủy') {
                $total_guests += $b['SoLuongKhach'];
            }
        }

        $data = [
            'schedule' => $schedule,
            'bookings' => $bookings,
            'total_guests' => $total_guests
        ];

        $this->view('admin/schedules/guests', $data);
    }
}
?>