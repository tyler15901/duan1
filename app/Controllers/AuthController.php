<?php
class AuthController extends Controller {
    
    // --- ĐĂNG KÝ ---
    public function register() {
        if (Session::isLoggedIn()) { header('Location: ' . BASE_URL); exit; }

        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $fullname = trim($_POST['fullname']);
            $confirm  = trim($_POST['confirm_password']);

            $userModel = $this->model('UserModel');

            // Validate
            if (empty($username) || empty($password) || empty($fullname)) {
                $data['error'] = "Vui lòng nhập đủ thông tin!";
            } elseif ($password !== $confirm) {
                $data['error'] = "Mật khẩu xác nhận không khớp!";
            } elseif ($userModel->checkUsername($username)) {
                $data['error'] = "Tên đăng nhập đã tồn tại!";
            } else {
                // Gom dữ liệu gửi sang Model
                $userData = [
                    'TenDangNhap' => $username,
                    'MatKhau'     => $password,
                    'HoTen'       => $fullname
                ];

                if ($userModel->register($userData)) {
                    header('Location: ' . BASE_URL . 'auth/login?msg=success');
                    exit;
                } else {
                    $data['error'] = "Lỗi hệ thống, thử lại sau!";
                }
            }
        }
        $this->view('auth/register', $data);
    }

    // --- ĐĂNG NHẬP ---

    public function login() {
        if (Session::isLoggedIn()) {
            // Nếu lỡ vào lại trang login khi đã đăng nhập -> Tự động đẩy về đúng nơi
            if (Session::isAdmin()) header('Location: ' . BASE_URL . 'admin/dashboard');
            elseif (Session::isGuide()) header('Location: ' . BASE_URL . 'guide/dashboard');
            else header('Location: ' . BASE_URL);
            exit;
        }

        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $userModel = $this->model('UserModel');
            $user = $userModel->getUserForLogin($username);

            if ($user) {
                // So sánh mật khẩu (Nếu bạn chưa hash thì dùng ==, nếu đã hash thì dùng password_verify)
                if ($password == $user['MatKhau']) {
                    
                    // Lưu Session (Lấy vai trò trực tiếp từ DB)
                    Session::setLogin($user);

                    // --- PHÂN LUỒNG GIAO DIỆN ---
                    $role = $user['VaiTro']; // Lấy từ cột VaiTro trong DB

                    switch ($role) {
                        case 'ADMIN':
                            // Giao diện Admin: Quản lý tour, nhân sự, thống kê
                            header('Location: ' . BASE_URL . 'admin/dashboard');
                            break;

                        case 'HDV':
                            // Giao diện HDV: Xem lịch dẫn tour, điểm danh khách
                            header('Location: ' . BASE_URL . 'guide/dashboard');
                            break;

                        default: // KhachHang
                            // Giao diện Khách: Trang chủ, đặt tour
                            header('Location: ' . BASE_URL);
                            break;
                    }
                    exit;

                } else {
                    $data['error'] = "Mật khẩu không đúng!";
                }
            } else {
                $data['error'] = "Tài khoản không tồn tại!";
            }
        }
        $this->view('auth/login', $data);
    }

    public function logout() {
        Session::logout();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}