<?php
class BookingController extends Controller
{
    public function __construct()
    {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    // 1. Danh sách đơn hàng
    public function index()
    {
        $model = $this->model('BookingModel');
        $model->autoCheckPaymentOverdue();
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
        $model->autoCheckPaymentOverdue(); 

        $booking = $model->getBookingById($id);
        
        // [MỚI] Lấy danh sách các lịch khởi hành khác của cùng Tour này (để đổi lịch)
        $otherSchedules = $model->getSchedulesByTour($booking['MaTour']);

        $data = [
            'booking' => $booking,
            'guests' => $model->getGuestList($id),
            'guides' => $model->getAllGuides(),
            'other_schedules' => $otherSchedules // Truyền sang View
        ];
        $this->view('admin/bookings/detail', $data);
    }

    // 3. [QUAN TRỌNG] Cập nhật đơn hàng (Status + Số lượng + Giá)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');
            $currentBooking = $model->getBookingById($id);
            $oldLichId = $currentBooking['MaLichKhoiHanh'];
            $newLichId = !empty($_POST['change_lich_id']) ? $_POST['change_lich_id'] : $oldLichId;
            
            // 1. Xử lý số liệu khách & Giá tiền mới
            $sl_nguoi_lon = isset($_POST['sl_nguoi_lon']) ? (int)$_POST['sl_nguoi_lon'] : $currentBooking['SLNguoiLon'];
            $sl_tre_em = isset($_POST['sl_tre_em']) ? (int)$_POST['sl_tre_em'] : $currentBooking['SLTreEm'];
            
            // Lấy tổng tiền từ form (đã được JS tính toán) hoặc giữ nguyên cũ
            // Lưu ý: Remove dấu phẩy/chấm định dạng tiền tệ
            $tongTienNew = isset($_POST['tong_tien']) ? (float)str_replace(['.', ','], '', $_POST['tong_tien']) : (float)$currentBooking['TongTien'];
            
            // Tính lại tổng Pax
            $totalPax = $sl_nguoi_lon + $sl_tre_em;

            // 2. Xử lý trạng thái thanh toán & Cọc
            $paymentStatus = $_POST['thanh_toan'];
            $tienCoc = (float)$currentBooking['TienCoc']; 

            if ($paymentStatus == 'Đã thanh toán') {
                $tienCoc = $tongTienNew; // Full tiền
            } elseif ($paymentStatus == 'Chưa thanh toán') {
                $tienCoc = 0;
            } 
            // Nếu là 'Đã cọc', giữ nguyên số cũ (hoặc có thể thêm input sửa cọc)

            // 3. Xử lý File đính kèm
            $fileName = $currentBooking['FileDanhSachKhach']; 
            if (!empty($_FILES['guest_file']['name'])) {
                $fileName = time() . '_' . $_FILES['guest_file']['name'];
                move_uploaded_file($_FILES['guest_file']['tmp_name'], '../public/assets/uploads/files/' . $fileName);
            }

            // 4. Chuẩn bị dữ liệu Update
            // Cập nhật lại Ghi chú cơ cấu nếu thay đổi số lượng
            $ghiChuMoi = "Cơ cấu: $sl_nguoi_lon Lớn, $sl_tre_em Trẻ em";
            $maHDV = !empty($_POST['ma_hdv']) ? $_POST['ma_hdv'] : null;
            $data = [
                'status' => $_POST['trang_thai'],
                'payment_status' => $paymentStatus,
                'file' => $fileName,
                'tien_coc' => $tienCoc,
                // Các trường mới thêm vào mảng update
                'sl_nguoi_lon' => $sl_nguoi_lon,
                'sl_tre_em' => $sl_tre_em,
                'tong_tien' => $tongTienNew,
                'so_luong_khach' => $totalPax,
                'ghi_chu' => $ghiChuMoi,
                'ma_hdv' => $maHDV,
                'ma_lich_moi' => $newLichId
            ];

            // Gọi Model update (Lưu ý: Model cần hỗ trợ update các trường mới này)
            if ($model->updateBooking($id, $data)) {
                // 1. Đồng bộ số khách cho Lịch Mới (và Booking hiện tại)
                $model->syncTotalGuests($id); 
                
                // 2. Cập nhật trạng thái & HDV cho Lịch Mới
                $model->autoUpdateScheduleStatus($newLichId);
                $model->distributeGuidesForSchedule($newLichId);

                // 3. [QUAN TRỌNG] Nếu có đổi lịch -> Cập nhật lại cho Lịch Cũ
                if ($oldLichId != $newLichId) {
                    // Trigger SQL đã tự update số khách, nhưng ta cần gọi hàm PHP 
                    // để chạy logic "Hủy HDV nếu khách tụt < Min" cho lịch cũ
                    $model->autoUpdateScheduleStatus($oldLichId);
                }

                echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='" . BASE_URL . "/booking/detail/$id';</script>";
            } else {
                echo "<script>alert('Lỗi cập nhật!'); window.history.back();</script>";
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

    // 5. API Lấy lịch theo Tour (AJAX)
    public function get_schedules()
    {
        error_reporting(0);
        header('Content-Type: application/json');
        try {
            if (isset($_GET['tour_id'])) {
                $model = $this->model('BookingModel');
                $schedules = $model->getSchedulesByTour($_GET['tour_id']);
                echo json_encode($schedules);
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            echo json_encode([]); 
        }
        exit;
    }

    // 6. Xử lý Lưu Đơn Hàng Mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('BookingModel');

            $tourId = isset($_POST['tour_id']) ? $_POST['tour_id'] : null;
            if (!$tourId) {
                echo "<script>alert('Vui lòng chọn Tour!'); history.back();</script>";
                return;
            }

            $lichId = null;
            $optionDate = isset($_POST['option_date']) ? $_POST['option_date'] : 'exist';

            // --- XỬ LÝ LỊCH KHỞI HÀNH ---
            if ($optionDate == 'custom') {
                // CASE 2: TỰ TẠO LỊCH MỚI
                $customDate = $_POST['custom_date'];
                if (empty($customDate)) {
                    echo "<script>alert('Vui lòng chọn ngày khởi hành!'); history.back();</script>";
                    return;
                }

                // Lấy giá từ form nhập vào (loại bỏ dấu phẩy/chấm)
                $priceAdult = str_replace(['.', ','], '', $_POST['price_adult_new']);
                $priceChild = str_replace(['.', ','], '', $_POST['price_child_new']);

                // Gọi Model tạo lịch với giá tùy chỉnh
                $lichId = $model->autoCreateScheduleFromTour($tourId, $customDate, $priceAdult, $priceChild);
                
                if (!$lichId) {
                    echo "<script>alert('Lỗi: Không thể tạo lịch khởi hành tự động!'); history.back();</script>";
                    return;
                }

            } else {
                // CASE 1: CHỌN LỊCH CÓ SẴN
                if (empty($_POST['lich_id'])) {
                    echo "<script>alert('Vui lòng chọn Lịch khởi hành!'); history.back();</script>";
                    return;
                }
                $lichId = $_POST['lich_id'];
            }

            // --- XỬ LÝ THÔNG TIN KHÁCH & TIỀN ---
            $hoTen = $_POST['ho_ten'] ?? 'Khách lẻ';
            $sdt = $_POST['so_dien_thoai'] ?? '';
            $customerId = $model->getOrCreateCustomer($hoTen, $sdt);

            $sl_nguoi_lon = (int)($_POST['sl_nguoi_lon'] ?? 0);
            $sl_tre_em = (int)($_POST['sl_tre_em'] ?? 0);
            $totalPax = $sl_nguoi_lon + $sl_tre_em;
            
            if ($totalPax <= 0) {
                 echo "<script>alert('Vui lòng nhập số lượng khách ít nhất là 1!'); history.back();</script>";
                 return;
            }

            $tongTien = str_replace(['.', ','], '', $_POST['tong_tien_chot'] ?? '0');
            $trangThaiTT = $_POST['trang_thai_tt'] ?? 'Chưa thanh toán';
            
            $tienCoc = 0;
            if ($trangThaiTT == 'Đã thanh toán') {
                $tienCoc = $tongTien;
            } elseif ($trangThaiTT == 'Đã cọc') {
                $tienCoc = str_replace(['.', ','], '', $_POST['tien_coc'] ?? '0');
            }

            // Chuẩn bị dữ liệu Booking
            $data = [
                'tour' => $tourId,
                'lich' => $lichId,
                'khach' => $customerId,
                'sl' => $totalPax,
                'tien' => $tongTien,
                'coc' => $tienCoc,
                'tt_thanhtoan' => $trangThaiTT,
                'ghichu' => "Cơ cấu: $sl_nguoi_lon Lớn, $sl_tre_em Trẻ em. " . ($_POST['ghi_chu_chung'] ?? ''),
                'sl_nguoi_lon' => $sl_nguoi_lon, 
                'sl_tre_em' => $sl_tre_em
            ];

            // Tạo Booking
            $newBookingId = $model->createBookingReturnId($data);

            if ($newBookingId) {
                // Lưu danh sách khách đi cùng
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
                
                // [QUAN TRỌNG] Đồng bộ số khách VÀ Cập nhật trạng thái Lịch
                $model->syncTotalGuests($newBookingId);     // Đếm khách -> Tạo thông báo
                $model->autoUpdateScheduleStatus($lichId);  // Check ngày -> Update trạng thái (Đóng/Mở/Chạy)
                $model->distributeGuidesForSchedule($lichId);

                header("Location: " . BASE_URL . "/booking/detail/" . $newBookingId);
                exit;
            } else {
                echo "<script>alert('Lỗi khi lưu đơn hàng!'); history.back();</script>";
            }
        }
    }

    // 7. Thêm khách mới (Detail)
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
            $model->syncTotalGuests($bookingId);
            header("Location: " . BASE_URL . "/booking/detail/" . $bookingId);
        }
    }

    // 8. Cập nhật khách (Detail)
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

    // 9. Xóa khách
    public function delete_guest($guestId, $bookingId) {
        $model = $this->model('BookingModel');
        $model->removeGuestFromBooking($guestId);
        $model->syncTotalGuests($bookingId);
        header("Location: " . BASE_URL . "/booking/detail/" . $bookingId);
    }

    // 10. Import khách
    public function import_guests($bookingId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_FILES['file_import']) || $_FILES['file_import']['error'] != 0) {
                echo "<script>alert('Vui lòng chọn file hợp lệ!'); window.history.back();</script>";
                return;
            }
            $fileName = $_FILES['file_import']['tmp_name'];
            $model = $this->model('BookingModel');
            $count = 0;
            if (($handle = fopen($fileName, "r")) !== FALSE) {
                fgetcsv($handle, 1000, ","); 
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (!empty($row[0])) {
                        $guestData = [
                            'ho_ten'     => $row[0],
                            'loai_khach' => !empty($row[1]) ? $row[1] : 'Người lớn',
                            'sdt'        => isset($row[2]) ? $row[2] : '',
                            'so_giay_to' => isset($row[3]) ? $row[3] : '',
                            'ghi_chu'    => isset($row[4]) ? $row[4] : ''
                        ];
                        $model->addGuestToBooking($bookingId, $guestData);
                        $count++;
                    }
                }
                fclose($handle);
                $model->syncTotalGuests($bookingId);
            }
            echo "<script>alert('Đã import thành công $count khách hàng!'); window.location.href='" . BASE_URL . "/booking/detail/$bookingId';</script>";
        }
    }
}
?>