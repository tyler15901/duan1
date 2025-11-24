<?php
class BookingController extends Controller {
    
    // HIỂN THỊ FORM ĐIỀN THÔNG TIN
    public function create() {
        // Lấy tham số từ URL
        $lichId = $_GET['lich_id'] ?? null;
        $adultQty = $_GET['adult'] ?? 1;
        $childQty = $_GET['child'] ?? 0;

        if (!$lichId) { header('Location: ' . BASE_URL); exit; }

        $bookingModel = $this->model('BookingModel');
        
        // Lấy thông tin tour để hiển thị lại cho khách check
        $info = $bookingModel->getBookingInfo($lichId);
        
        if (!$info) die("Lịch khởi hành không tồn tại");

        // Tính toán sơ bộ (Hiển thị thôi, khi lưu sẽ tính lại cho an toàn)
        $priceAdult = $info['GiaNguoiLon'];
        $priceChild = $info['GiaNguoiLon'] * 0.8; // Trẻ em 80%
        $totalEstimate = ($priceAdult * $adultQty) + ($priceChild * $childQty);

        $this->view('booking/create', [
            'info' => $info,
            'adult' => $adultQty,
            'child' => $childQty,
            'total' => $totalEstimate,
            'title' => 'Xác nhận đặt tour'
        ]);
    }

    // XỬ LÝ LƯU BOOKING (POST)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL); exit;
        }

        $bookingModel = $this->model('BookingModel');

        // 1. Lấy dữ liệu từ Form
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $note = $_POST['note'];
        
        $lichId = $_POST['lich_id'];
        $adult = $_POST['adult_qty'];
        $child = $_POST['child_qty'];

        // 2. Validate cơ bản
        if (empty($fullname) || empty($phone)) {
            die("Vui lòng điền tên và số điện thoại!");
        }

        // 3. Tính toán lại Tổng tiền (Backend Calculation - Để bảo mật)
        $info = $bookingModel->getBookingInfo($lichId);
        $priceAdult = $info['GiaNguoiLon'];
        $totalAmount = ($priceAdult * $adult) + ($priceAdult * 0.8 * $child);
        $totalPax = $adult + $child;

        // 4. BẮT ĐẦU GIAO DỊCH (Transaction) - Để đảm bảo toàn vẹn dữ liệu
        // (Lưu ý: BaseModel cần hỗ trợ transaction, nếu không thì gọi thẳng DB)
        // Ở đây mình làm đơn giản tuần tự:

        try {
            // A. Lưu khách hàng
            $customerId = $bookingModel->addCustomer([
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            ]);

            // B. Lưu Booking
            $bookingData = [
                'tour_id' => $info['MaTour'],
                'customer_id' => $customerId,
                'start_date' => $info['NgayKhoiHanh'],
                'total_pax' => $totalPax,
                'total_amount' => $totalAmount,
                'note' => $note . " (Người lớn: $adult, Trẻ em: $child)"
            ];
            $bookingId = $bookingModel->createBooking($bookingData);

            // C. Liên kết Lịch (Để trừ chỗ)
            $bookingModel->addBookingSchedule($bookingId, $lichId);

            // D. Thành công -> Chuyển hướng trang cảm ơn
            // (Bạn có thể tạo thêm view booking/success.php)
            echo "<script>alert('Đặt tour thành công! Mã đơn: BK-$bookingId'); window.location.href='".BASE_URL."';</script>";

        } catch (Exception $e) {
            die("Lỗi đặt tour: " . $e->getMessage());
        }
    }
}