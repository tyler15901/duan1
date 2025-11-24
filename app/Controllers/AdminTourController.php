<?php
class AdminTourController extends Controller {
    
    public function __construct() {
        if (!Session::isAdmin()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    // Danh sách Tour
    public function index() {
        $tourModel = $this->model('TourModel');
        // Tận dụng hàm getTourList có sẵn để lấy danh sách
        $tours = $tourModel->getTourList(100); // Lấy 100 tour mới nhất
        
        $this->view('admin/tour/list', [
            'tours' => $tours,
            'title' => 'Quản lý Tour du lịch'
        ]);
    }

    // Hiển thị form thêm mới
    public function create() {
        $this->view('admin/tour/create', ['title' => 'Thêm tour mới']);
    }

    // Xử lý lưu Tour (POST)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tourModel = $this->model('TourModel');

            // 1. Xử lý Upload ảnh
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "uploads/anhtour/";
                // Tạo thư mục nếu chưa có (trên Windows có thể cần tạo tay)
                // Tên file: time_tenfile.jpg để tránh trùng
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                // Upload file vào thư mục public/uploads/anhtour/
                if (move_uploaded_file($_FILES["image"]["tmp_name"], "../public/" . $targetFilePath)) {
                    $imagePath = "anhtour/" . $fileName; // Lưu đường dẫn tương đối vào DB
                }
            }

            // 2. Gom dữ liệu
            $data = [
                'MaLoaiTour' => $_POST['type'], // 1: Trong nước, 2: Quốc tế
                'TenTour' => $_POST['name'],
                'HinhAnh' => $imagePath,
                'MoTa' => $_POST['desc'],
                'SoNgay' => $_POST['days'],
                'SoChoToiDa' => $_POST['slots'],
                'TrangThai' => 'Hoạt động'
            ];

            // 3. Lưu vào DB
            $tourId = $tourModel->createTour($data);

            // 4. Lưu giá khởi điểm
            if ($tourId && !empty($_POST['price'])) {
                $tourModel->addInitialPrice($tourId, $_POST['price']);
            }

            header('Location: ' . BASE_URL . 'adminTour/index?msg=success');
        }
    }


    // 5. Hiển thị form sửa tour
    public function edit($id) {
        $tourModel = $this->model('TourModel');
        // Lấy thông tin tour cũ để điền vào form
        $tour = $tourModel->getTourDetail($id);

        if (!$tour) {
            die('Tour không tồn tại');
        }

        $this->view('admin/tour/update', [
            'tour' => $tour,
            'title' => 'Cập nhật Tour: ' . $tour['TenTour']
        ]);
    }

    // 6. Xử lý cập nhật (POST)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tourModel = $this->model('TourModel');

            // 1. Xử lý ảnh (Nếu có chọn ảnh mới thì upload, không thì thôi)
            $imagePath = null; // Mặc định không đổi ảnh
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "uploads/anhtour/";
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                // Upload vào public/uploads/anhtour/
                if (move_uploaded_file($_FILES["image"]["tmp_name"], "../public/" . $targetFilePath)) {
                    $imagePath = "anhtour/" . $fileName;
                }
            }

            // 2. Gom dữ liệu
            $data = [
                'MaLoaiTour' => $_POST['type'],
                'TenTour' => $_POST['name'],
                'MoTa' => $_POST['desc'],
                'SoNgay' => $_POST['days'],
                'SoChoToiDa' => $_POST['slots'],
                'TrangThai' => $_POST['status'],
                'HinhAnh' => $imagePath // Nếu null, Model sẽ giữ nguyên ảnh cũ
            ];

            // 3. Gọi Model cập nhật
            $tourModel->updateTour($id, $data);

            // 4. Cập nhật giá mới (Nếu có nhập)
            // Lưu ý: Logic này thêm dòng giá mới vào bảng giatour
            if (!empty($_POST['price'])) {
                $tourModel->addInitialPrice($id, $_POST['price']);
            }

            // 5. Quay về danh sách
            header('Location: ' . BASE_URL . 'adminTour/index?msg=updated');
        }
    }

    // Xóa tour
    public function delete($id) {
        $tourModel = $this->model('TourModel');
        $tourModel->deleteTour($id);
        header('Location: ' . BASE_URL . 'adminTour/index');
    }
}