<?php
class BookingController extends Controller {

    // Danh sách đơn hàng
    public function index() {
        $model = $this->model('BookingModel');
        
        // Phân trang & Tìm kiếm
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $bookings = $model->getAllBookings(['keyword' => $keyword], $limit, $offset);
        $total = $model->countBookings(['keyword' => $keyword]);

        $data = [
            'bookings' => $bookings,
            'pagination' => [
                'current' => $page,
                'total_pages' => ceil($total / $limit),
                'keyword' => $keyword
            ]
        ];
        $this->view('admin/bookings/index', $data);
    }

    // Xem chi tiết đơn hàng
    public function detail($id) {
        $model = $this->model('BookingModel');
        $data = [
            'booking' => $model->getBookingById($id),
            'guests' => $model->getGuestList($id)
        ];
        $this->view('admin/bookings/detail', $data);
    }

    // Xử lý cập nhật (Trạng thái + File danh sách)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');
            
            // Xử lý upload file danh sách khách
            $fileName = null;
            if (!empty($_FILES['guest_file']['name'])) {
                // Chỉ cho phép file excel/doc/pdf
                $fileName = time() . '_' . $_FILES['guest_file']['name'];
                move_uploaded_file($_FILES['guest_file']['tmp_name'], '../public/assets/uploads/files/' . $fileName);
            }

            $data = [
                'status' => $_POST['trang_thai'], // Xác nhận/Hủy
                'payment_status' => $_POST['thanh_toan'], // Đã cọc/Đã TT
                'file' => $fileName
            ];

            if ($model->updateBooking($id, $data)) {
                echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='".BASE_URL."/booking/detail/$id';</script>";
            } else {
                echo "Lỗi cập nhật!";
            }
        }
    }

    // 1. Hiển thị Form tạo đơn (URL: /booking/create)
    public function create() {
        $model = $this->model('BookingModel');
        $data = [
            'tours' => $model->getActiveTours()
        ];
        $this->view('admin/bookings/create', $data);
    }

    // 2. API lấy lịch theo tour (AJAX gọi vào đây)
    // File: app/Controllers/BookingController.php

    public function get_schedules() {
        if (isset($_GET['tour_id'])) {
            $model = $this->model('BookingModel');
            $schedules = $model->getSchedulesByTour($_GET['tour_id']);
            
            // QUAN TRỌNG: Xóa sạch bộ nhớ đệm đầu ra để đảm bảo JSON sạch
            if (ob_get_length()) ob_clean(); 
            
            header('Content-Type: application/json');
            echo json_encode($schedules);
            exit;
        }
    }

    // 3. Xử lý Lưu Đơn Hàng
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');

            // A. Xử lý khách hàng (Tự động tìm hoặc thêm mới)
            $customerId = $model->getOrCreateCustomer($_POST['ho_ten'], $_POST['so_dien_thoai']);

            // B. Chuẩn bị dữ liệu Booking
            $data = [
                'tour' => $_POST['tour_id'],
                'lich' => $_POST['lich_id'],
                'khach' => $customerId,
                'sl' => $_POST['so_luong'],
                'tien' => $_POST['tong_tien'],
                'coc' => ($_POST['trang_thai_tt'] == 'Đã cọc') ? $_POST['tong_tien'] * 0.5 : (($_POST['trang_thai_tt'] == 'Đã thanh toán') ? $_POST['tong_tien'] : 0),
                'tt_thanhtoan' => $_POST['trang_thai_tt'],
                'ghichu' => "Đơn tạo mới từ Admin"
            ];

            // C. Lưu vào DB
            if ($model->createBooking($data)) {
                // Thành công -> Chuyển hướng về trang danh sách
                header("Location: " . BASE_URL . "/booking/index");
            } else {
                echo "Lỗi tạo đơn hàng!";
            }
        }
    }
}
?>