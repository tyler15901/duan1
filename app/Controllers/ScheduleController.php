<?php
class ScheduleController extends Controller
{
    public function __construct()
    {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    public function index()
    {
        $model = $this->model('ScheduleModel');

        // [SỬA LẠI TÊN HÀM CHO KHỚP VỚI MODEL]
        // Kiểm tra xem trong ScheduleModel đang dùng tên hàm nào để gọi cho đúng
        if (method_exists($model, 'autoUpdateTourStatus')) {
            $model->autoUpdateTourStatus(); 
        } elseif (method_exists($model, 'autoCheckAndCloseSchedules')) {
            $model->autoCheckAndCloseSchedules();
        }
        // -----------------------------------------------------------------------

        $tour_id = isset($_GET['tour_id']) ? $_GET['tour_id'] : null;
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = ['tour_id' => $tour_id, 'keyword' => $keyword];

        $schedules = $model->getSchedulesFiltered($filters, $limit, $offset);
        $total_records = $model->countSchedules($filters);
        $total_pages = ceil($total_records / $limit);

        $filter_title = "";
        if ($tour_id) {
            $tourName = $model->getTourName($tour_id);
            $filter_title = "Đang hiển thị lịch của tour: " . $tourName;
        }

        $data = [
            'schedules' => $schedules,
            'filter_msg' => $filter_title,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records,
                'tour_id' => $tour_id,
                'keyword' => $keyword
            ]
        ];
        $this->view('admin/schedules/index', $data);
    }

    public function create()
    {
        $model = $this->model('ScheduleModel');
        $data = [
            'tours' => $model->getTours(),
            'staffs' => $model->getStaffs(),
            'resources' => $model->getResources()
        ];
        $this->view('admin/schedules/create', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ScheduleModel');
            $staffs = isset($_POST['staffs']) ? $_POST['staffs'] : [];
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];
            foreach ($staffs as $staffId) {
                if ($model->isGuideBusy($staffId, $start, $end)) {
                    echo "<script>alert('Lỗi: Một trong số các HDV đã chọn đang bận trong khoảng thời gian này!'); window.history.back();</script>";
                    return;
                }
            }
            $mainData = [
                'tour_id' => $_POST['tour_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'meeting_time' => $_POST['meeting_time'],
                'meeting_place' => $_POST['meeting_place'],
                'price_adult' => !empty($_POST['price_adult']) ? str_replace(['.', ','], '', $_POST['price_adult']) : 0,
                'price_child' => !empty($_POST['price_child']) ? str_replace(['.', ','], '', $_POST['price_child']) : 0,
                'max_pax' => !empty($_POST['so_cho_toi_da']) ? $_POST['so_cho_toi_da'] : 30,
                'min_pax' => !empty($_POST['so_cho_min']) ? $_POST['so_cho_min'] : 15
            ];
            $resources = isset($_POST['resources']) ? $_POST['resources'] : [];
            if ($model->createSchedule($mainData, $resources, $staffs)) {
                header("Location: " . BASE_URL . "/schedule/index");
            } else {
                echo "<script>alert('Lỗi tạo lịch! Vui lòng kiểm tra lại.'); window.history.back();</script>";
            }
        }
    }

    public function show($id)
    {
        $model = $this->model('ScheduleModel');
        $bookingModel = $this->model('BookingModel');

        $data = [
            'schedule' => $model->getScheduleById($id),
            'resources' => $model->getAssignedResourcesDetail($id),
            'staffs' => $model->getAssignedStaffsDetail($id),
            'all_guides' => $bookingModel->getAllGuides(),
            // [MỚI] Nhận ID thông báo từ URL (nếu có) để hiển thị nút xác nhận
            'noti_id' => isset($_GET['noti_id']) ? $_GET['noti_id'] : null
        ];
        $this->view('admin/schedules/show', $data);
    }

    public function edit($id)
    {
        $model = $this->model('ScheduleModel');
        $data = [
            'schedule' => $model->getScheduleById($id),
            'tours' => $model->getTours(),
            'all_staffs' => $model->getStaffs(),
            'all_resources' => $model->getResources(),
            'assigned_staffs' => $model->getAssignedStaffIds($id),
            'assigned_resources' => $model->getAssignedResourceIds($id)
        ];
        $this->view('admin/schedules/edit', $data);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ScheduleModel');

            $staffs = isset($_POST['staffs']) ? $_POST['staffs'] : [];
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];

            foreach ($staffs as $staffId) {
                if ($model->isGuideBusy($staffId, $start, $end, $id)) {
                    echo "<script>alert('Lỗi: HDV đang bận lịch khác! Vui lòng kiểm tra lại.'); window.history.back();</script>";
                    return;
                }
            }

            $mainData = [
                'tour_id' => $_POST['tour_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'meeting_time' => $_POST['meeting_time'],
                'meeting_place' => $_POST['meeting_place'],
                'status' => $_POST['status'],
                'price_adult' => !empty($_POST['price_adult']) ? str_replace(['.', ','], '', $_POST['price_adult']) : 0,
                'price_child' => !empty($_POST['price_child']) ? str_replace(['.', ','], '', $_POST['price_child']) : 0,
                'max_pax' => $_POST['so_cho_toi_da'],
                'min_pax' => $_POST['so_cho_min']
            ];

            $resources = isset($_POST['resources']) ? $_POST['resources'] : [];

            if ($model->updateSchedule($id, $mainData, $resources, $staffs)) {

                // [TỰ ĐỘNG CHIA ĐƠN SAU KHI UPDATE]
                if (!empty($staffs)) {
                    $bookingModel = $this->model('BookingModel');
                    $bookingModel->distributeGuidesForSchedule($id);
                    $model->clearScheduleNotifications($id);
                }

                header("Location: " . BASE_URL . "/schedule/show/" . $id);
            } else {
                echo "<script>alert('Lỗi cập nhật!'); window.history.back();</script>";
            }
        }
    }

    public function assign_guides($lichId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $scheduleModel = $this->model('ScheduleModel');

            $selectedGuideIds = isset($_POST['guides']) ? $_POST['guides'] : [];

            if (empty($selectedGuideIds)) {
                echo "<script>alert('Vui lòng chọn ít nhất 1 Hướng dẫn viên!'); history.back();</script>";
                return;
            }

            try {
                $scheduleModel->saveAssignedGuides($lichId, $selectedGuideIds);
                $bookingModel = $this->model('BookingModel');
                $bookingModel->distributeGuidesForSchedule($lichId);
                $scheduleModel->clearScheduleNotifications($lichId);

                echo "<script>alert('Đã lưu danh sách nhân sự và phân chia đơn hàng thành công!'); window.location.href='" . BASE_URL . "/schedule/show/$lichId';</script>";

            } catch (Exception $e) {
                echo "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }

    // Hàm chia đơn nội bộ
    private function autoDistributeBookingsToGuides($lichId, $guideIds)
    {
        $bookingModel = $this->model('BookingModel');
        $bookings = $bookingModel->getConfirmedBookingsBySchedule($lichId);

        if (empty($bookings))
            return;

        $totalGuides = count($guideIds);
        $guideIndex = 0;

        foreach ($bookings as $booking) {
            $currentGuideId = $guideIds[$guideIndex];
            $bookingModel->updateBookingGuide($booking['MaBooking'], $currentGuideId);

            $guideIndex++;
            if ($guideIndex >= $totalGuides) {
                $guideIndex = 0;
            }
        }
    }

    public function delete($id)
    {
        $model = $this->model('ScheduleModel');
        if ($model->deleteSchedule($id)) {
            header("Location: " . BASE_URL . "/schedule/index");
        } else {
            echo "<script>alert('Không thể xóa lịch này vì đã có Booking phát sinh!'); window.location.href='" . BASE_URL . "/schedule/index';</script>";
        }
    }

    public function guests($id)
    {
        $model = $this->model('ScheduleModel');
        $schedule = $model->getScheduleById($id);
        $bookings = $model->getBookingsBySchedule($id);
        $total_guests = 0;
        foreach ($bookings as $b) {
            if ($b['TrangThai'] != 'Đã hủy') {
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

    public function expenses($id)
    {
        $scheduleModel = $this->model('ScheduleModel');
        $expenseModel = $this->model('ExpenseModel');
        $bookingModel = $this->model('BookingModel');
        $schedule = $scheduleModel->getScheduleById($id);
        $expenses = $expenseModel->getExpensesBySchedule($id);
        $total_expense = $expenseModel->getTotalExpense($id);
        $bookings = $bookingModel->getBookingsBySchedule($id);
        $total_revenue = 0;
        foreach ($bookings as $b) {
            if ($b['TrangThai'] != 'Đã hủy') {
                $total_revenue += $b['TongTien'];
            }
        }
        $data = [
            'schedule' => $schedule,
            'expenses' => $expenses,
            'total_expense' => $total_expense,
            'total_revenue' => $total_revenue,
            'profit' => $total_revenue - $total_expense
        ];
        $this->view('admin/schedules/expenses', $data);
    }

    public function store_expense($scheduleId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ExpenseModel');
            $data = [
                'lich_id' => $scheduleId,
                'loai' => $_POST['loai_chi_phi'],
                'tien' => str_replace(['.', ','], '', $_POST['so_tien']),
                'ghichu' => $_POST['ghi_chu']
            ];
            $model->addExpense($data);
            header("Location: " . BASE_URL . "/schedule/expenses/" . $scheduleId);
        }
    }

    public function delete_expense($id, $scheduleId)
    {
        $model = $this->model('ExpenseModel');
        $model->deleteExpense($id);
        header("Location: " . BASE_URL . "/schedule/expenses/" . $scheduleId);
    }

    public function check_guides()
    {
        header('Content-Type: application/json');
        $start = isset($_GET['start']) ? $_GET['start'] : null;
        $end = isset($_GET['end']) ? $_GET['end'] : null;
        $currentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($start && $end) {
            $model = $this->model('ScheduleModel');
            $guides = $model->getGuidesAvailability($start, $end, $currentId);
            echo json_encode($guides);
        } else {
            echo json_encode([]);
        }
        exit;
    }
}
?>