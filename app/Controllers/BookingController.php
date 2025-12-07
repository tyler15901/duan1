<?php
class BookingController extends Controller
{
    public function __construct()
    {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    // 1. Danh sách đơn hàng (Có phân trang & lọc)
    public function index()
    {
        $model = $this->model('BookingModel');
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $keyword,
            'status' => $status,
            'payment_status' => $payment_status
        ];

        $bookings = $model->getAllBookings($filters, $limit, $offset);
        $total = $model->countBookings($filters);

        $data = [
            'bookings' => $bookings,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_records' => $total,
                'keyword' => $keyword,
                'status' => $status,
                'payment_status' => $payment_status
            ]
        ];
        $this->view('admin/bookings/index', $data);
    }

    // 2. Xem chi tiết đơn hàng
    public function detail($id)
    {
        $model = $this->model('BookingModel');
        $data = [
            'booking' => $model->getBookingById($id),
            'guests' => $model->getGuestList($id)
        ];
        $this->view('admin/bookings/detail', $data);
    }

    // 3. Cập nhật trạng thái đơn hàng (Tại trang chi tiết)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');
            
            $currentBooking = $model->getBookingById($id);
            $tongTien = (float)$currentBooking['TongTien'];
            $paymentStatus = $_POST['thanh_toan'];
            $tienCoc = (float)$currentBooking['TienCoc']; 

            // Logic tự động cập nhật tiền cọc dựa trên trạng thái
            if ($paymentStatus == 'Đã thanh toán') {
                $tienCoc = $tongTien; 
            } elseif ($paymentStatus == 'Chưa thanh toán') {
                $tienCoc = 0;
            } 
            // Nếu là 'Đã cọc' thì giữ nguyên số tiền cọc cũ (hoặc có thể thêm input sửa cọc ở đây nếu cần)

            $fileName = $currentBooking['FileDanhSachKhach']; 
            if (!empty($_FILES['guest_file']['name'])) {
                $fileName = time() . '_' . $_FILES['guest_file']['name'];
                move_uploaded_file($_FILES['guest_file']['tmp_name'], '../public/assets/uploads/files/' . $fileName);
            }

            $data = [
                'status' => $_POST['trang_thai'],
                'payment_status' => $paymentStatus,
                'file' => $fileName,
                'tien_coc' => $tienCoc
            ];

            if ($model->updateBooking($id, $data)) {
                echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='" . BASE_URL . "/booking/detail/$id';</script>";
            } else {
                echo "Lỗi cập nhật!";
            }
        }
    }

    // 4. Hiển thị Form tạo mới
    public function create()
    {
        $model = $this->model('BookingModel');
        $data = [
            'tours' => $model->getActiveTours()
        ];
        $this->view('admin/bookings/create', $data);
    }

    // 5. [QUAN TRỌNG] API Lấy lịch theo Tour (AJAX)
    public function get_schedules()
    {
        // Tắt báo lỗi PHP để tránh làm hỏng JSON trả về
        error_reporting(0);
        header('Content-Type: application/json');
        
        try {
            if (isset($_GET['tour_id'])) {
                $model = $this->model('BookingModel');
                $schedules = $model->getSchedulesByTour($_GET['tour_id']);
                
                // Trả về JSON sạch
                echo json_encode($schedules);
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            // Nếu lỗi, trả về mảng rỗng
            echo json_encode([]); 
        }
        exit;
    }

    // 6. Xử lý Lưu Đơn Hàng Mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');

            // A. Tạo/Lấy khách hàng đặt tour
            $customerId = $model->getOrCreateCustomer($_POST['ho_ten'], $_POST['so_dien_thoai']);

            // B. Xử lý số liệu
            $sl_nguoi_lon = (int)$_POST['sl_nguoi_lon'];
            $sl_tre_em = (int)$_POST['sl_tre_em'];
            $totalPax = $sl_nguoi_lon + $sl_tre_em;

            // Xử lý tiền (Xóa dấu chấm/phẩy)
            $tongTien = str_replace(['.', ','], '', $_POST['tong_tien_chot']);
            
            $trangThaiTT = $_POST['trang_thai_tt'];
            $tienCoc = 0;

            if ($trangThaiTT == 'Đã thanh toán') {
                $tienCoc = $tongTien;
            } elseif ($trangThaiTT == 'Đã cọc') {
                $tienCoc = str_replace(['.', ','], '', $_POST['tien_coc']);
            }

            // C. Chuẩn bị dữ liệu Booking
            $data = [
                'tour' => $_POST['tour_id'],
                'lich' => $_POST['lich_id'],
                'khach' => $customerId,
                'sl' => $totalPax,
                'tien' => $tongTien,
                'coc' => $tienCoc,
                'tt_thanhtoan' => $trangThaiTT,
                'ghichu' => "Cơ cấu: $sl_nguoi_lon Lớn, $sl_tre_em Trẻ em"
            ];

            // D. Tạo Booking và lấy ID
            $newBookingId = $model->createBookingReturnId($data);

            if ($newBookingId) {
                // E. Lưu danh sách khách đi cùng
                if (isset($_POST['guests']) && is_array($_POST['guests'])) {
                    foreach ($_POST['guests'] as $guest) {
                        if (!empty($guest['name'])) {
                            $guestData = [
                                'ho_ten'     => $guest['name'],
                                'loai_khach' => $guest['type'],
                                'sdt'        => $guest['phone'],
                                'so_giay_to' => $guest['id_card'],
                                'ghi_chu'    => $guest['note']
                            ];
                            $model->addGuestToBooking($newBookingId, $guestData);
                        }
                    }
                }
                header("Location: " . BASE_URL . "/booking/index");
            } else {
                echo "Lỗi tạo đơn hàng!";
            }
        }
    }

    // 7. Thêm khách mới vào đơn hàng (tại trang Detail)
    public function store_guest($bookingId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');
            
            $data = [
                'ho_ten'     => $_POST['ho_ten'],
                'loai_khach' => $_POST['loai_khach'],
                'sdt'        => $_POST['sdt'],
                'so_giay_to' => $_POST['so_giay_to'],
                'ghi_chu'    => $_POST['ghi_chu']
            ];

            $model->addGuestToBooking($bookingId, $data);
            
            header("Location: " . BASE_URL . "/booking/detail/" . $bookingId);
        }
    }

    // 8. Cập nhật thông tin khách (tại trang Detail)
    public function update_guest($bookingId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');
            
            $guestDetailId = $_POST['ma_chi_tiet'];
            
            $data = [
                'ho_ten'     => $_POST['ho_ten'],
                'loai_khach' => $_POST['loai_khach'],
                'sdt'        => $_POST['sdt'],
                'so_giay_to' => $_POST['so_giay_to'],
                'ghi_chu'    => $_POST['ghi_chu']
            ];

            if ($model->updateGuestInfo($guestDetailId, $data)) {
                header("Location: " . BASE_URL . "/booking/detail/" . $bookingId);
            } else {
                echo "<script>alert('Lỗi cập nhật!'); window.history.back();</script>";
            }
        }
    }

    // 9. Xóa khách khỏi đơn hàng
    public function delete_guest($guestId, $bookingId) {
        $model = $this->model('BookingModel');
        $model->removeGuestFromBooking($guestId);
        header("Location: " . BASE_URL . "/booking/detail/" . $bookingId);
    }
}
?>