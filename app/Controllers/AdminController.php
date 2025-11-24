<?php
class AdminController extends Controller {
    
    public function __construct() {
        // Kiểm tra quyền Admin (Chặn ngay từ cửa)
        // Lưu ý: Cần đảm bảo bạn đã login bằng tài khoản có VaiTro='ADMIN' trong DB
        if (!Session::isAdmin()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    // Trang Dashboard chính (Hiện danh sách đơn hàng)
    public function dashboard() {
        $bookingModel = $this->model('BookingModel');
        $bookings = $bookingModel->getAllBookings();

        $this->view('admin/dashboard', [
            'bookings' => $bookings,
            'title' => 'Quản trị viên - Quản lý đặt tour'
        ]);
    }

    // Xem chi tiết đơn hàng
    public function booking_detail($id) {
        $bookingModel = $this->model('BookingModel');
        $booking = $bookingModel->getBookingDetail($id);

        if (!$booking) die("Đơn hàng không tồn tại");

        $this->view('admin/booking_detail', [
            'booking' => $booking,
            'title' => 'Chi tiết đơn hàng #' . $booking['MaBookingCode']
        ]);
    }

    // Xử lý cập nhật trạng thái (Gửi từ Form chi tiết)
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $status = $_POST['status']; // Chờ xác nhận, Đã cọc, Hoàn tất, Hủy...

            $bookingModel = $this->model('BookingModel');
            $bookingModel->updateStatus($id, $status);

            // Quay lại trang chi tiết và báo thành công
            header('Location: ' . BASE_URL . 'admin/booking_detail/' . $id . '?msg=updated');
        }
    }
}