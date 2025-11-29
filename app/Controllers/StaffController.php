<?php
class StaffController extends Controller
{
    public function __construct()
    {
        // Gọi hàm kiểm tra quyền ngay khi khởi tạo Controller
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }
    public function index()
    {
        $model = $this->model('StaffModel');
        
        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // 10 người mỗi trang
        $offset = ($page - 1) * $limit;

        $guides = $model->getAllGuides($limit, $offset);
        $total = $model->countGuides();
        
        $data = [
            'guides' => $guides,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit)
            ]
        ];
        
        $this->view('admin/staffs/index', $data);
    }

    public function create()
    {
        $this->view('admin/staffs/create');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('StaffModel');

            // Xử lý ảnh đại diện
            $avatar = null;
            if (!empty($_FILES['avatar']['name'])) {
                $avatar = time() . '_' . $_FILES['avatar']['name'];
                move_uploaded_file($_FILES['avatar']['tmp_name'], '../public/assets/uploads/' . $avatar);
            }

            // Dữ liệu
            $data = [
                'hoten' => $_POST['ho_ten'],
                'dob' => $_POST['ngay_sinh'],
                'sdt' => $_POST['sdt'],
                'email' => $_POST['email'],
                'diachi' => $_POST['dia_chi'],
                'anh' => $avatar,
                'phanloai' => $_POST['phan_loai'],
                // Tài khoản
                'username' => $_POST['username'],
                'password' => $_POST['password']
            ];

            if ($model->createGuide($data)) {
                header("Location: " . BASE_URL . "/staff/index");
            } else {
                echo "Lỗi: Tên đăng nhập có thể đã tồn tại!";
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model('StaffModel');
        $guide = $model->getGuideById($id);

        // Nếu không tìm thấy HDV thì quay về danh sách
        if (!$guide) {
            header("Location: " . BASE_URL . "/staff/index");
            exit;
        }

        $this->view('admin/staffs/edit', ['guide' => $guide]);
    }

    // --- XỬ LÝ CẬP NHẬT ---
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('StaffModel');

            // 1. Xử lý ảnh đại diện (nếu có upload ảnh mới)
            $avatar = null;
            if (!empty($_FILES['avatar']['name'])) {
                $avatar = time() . '_' . $_FILES['avatar']['name'];
                move_uploaded_file($_FILES['avatar']['tmp_name'], '../public/assets/uploads/' . $avatar);
            }

            // 2. Gom dữ liệu
            $data = [
                'hoten' => $_POST['ho_ten'],
                'dob' => $_POST['ngay_sinh'],
                'sdt' => $_POST['sdt'],
                'email' => $_POST['email'],
                'diachi' => $_POST['dia_chi'],
                'phanloai' => $_POST['phan_loai']
            ];
            if (strlen($_POST['password']) < 6) {
                echo "<script>alert('Mật khẩu phải từ 6 ký tự trở lên!'); window.history.back();</script>";
                return;
            }
            // Nếu có ảnh mới thì thêm vào mảng data, không thì thôi (Model tự hiểu)
            if ($avatar) {
                $data['anh'] = $avatar;
            }

            // 3. Gọi Model cập nhật
            if ($model->updateGuide($id, $data)) {
                // Cập nhật thành công -> Quay về trang danh sách
                header("Location: " . BASE_URL . "/staff/index");
            } else {
                echo "Lỗi cập nhật! Vui lòng thử lại.";
            }
        }
    }
    public function delete($id)
    {
        $model = $this->model('StaffModel');
        $model->deleteGuide($id);
        header("Location: " . BASE_URL . "/staff/index");
    }

    // --- XEM LỊCH LÀM VIỆC CỦA HDV ---
    public function schedule()
    {
        // 1. Kiểm tra ID trên URL (Bắt buộc phải có)
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            // Không có ID -> Về trang danh sách
            header("Location: " . BASE_URL . "/staff/index");
            exit;
        }

        $model = $this->model('StaffModel');

        // 2. Lấy thông tin HDV và Lịch của họ
        $selected_guide = $model->getGuideById($id);
        $schedules = $model->getSchedulesByGuide($id);

        $data = [
            'guide' => $selected_guide,
            'schedules' => $schedules
        ];

        $this->view('admin/staffs/schedule', $data);
    }
}
?>