<?php
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/TourModel.php';

class BookingController {
    private $bookingModel;
    private $tourModel;

    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->tourModel = new TourModel();
    }

    /**
     * List all bookings
     */
    public function index() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $bookings = $this->bookingModel->getAll();
        require __DIR__ . '/../../views/booking/list.php';
    }

    /**
     * Create new booking
     */
    public function create() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'MaTour' => $_POST['MaTour'] ?? null,
                'MaKhachHang' => $_POST['MaKhachHang'] ?? null,
                'NgayKhoiHanh' => $_POST['NgayKhoiHanh'] ?? null,
                'SoLuongKhach' => $_POST['SoLuongKhach'] ?? 1,
                'TongTien' => $_POST['TongTien'] ?? null,
                'TienCoc' => $_POST['TienCoc'] ?? null,
                'TrangThai' => $_POST['TrangThai'] ?? 'Chờ xác nhận',
                'NguoiTao' => Session::get('user_id'),
                'GhiChu' => $_POST['GhiChu'] ?? null
            ];

            if ($this->bookingModel->create($data)) {
                header('Location: ' . BASE_URL . 'booking');
                exit;
            }
        }

        $tours = $this->tourModel->getAll();
        require __DIR__ . '/../../views/booking/create.php';
    }
}

