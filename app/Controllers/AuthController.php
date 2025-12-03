<?php
class AuthController extends Controller
{

    // --- ĐĂNG NHẬP ---
    public function login() {
        // Nếu đã đăng nhập rồi thì tự động chuyển trang luôn
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
        }

        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $data['error'] = "Vui lòng nhập đầy đủ thông tin!";
            } else {
                $userModel = $this->model('UserModel');
                $user = $userModel->getUserByUsername($username);

                if ($user) {
                    // 1. So sánh mật khẩu thường (Không mã hóa)
                    if ($password == $user['MatKhau']) { 
                        
                        if ($user['TrangThai'] != 'Hoạt động') {
                            $data['error'] = "Tài khoản đã bị khóa!";
                        } else {
                            // 2. Lưu Session
                            $_SESSION['user_id'] = $user['MaNguoiDung'];
                            $_SESSION['username'] = $user['TenDangNhap'];
                            $_SESSION['fullname'] = $user['HoTen'];
                            $_SESSION['role'] = $user['VaiTro']; // Quan trọng: Lưu vai trò (ADMIN/KhachHang)
                            $_SESSION['avatar'] = $user['Avatar'];
                            $_SESSION['staff_id'] = $user['MaNhanSu'];

                            // 3. Gọi hàm chuyển hướng
                            $this->redirectBasedOnRole();
                        }
                    } else {
                        $data['error'] = "Mật khẩu không chính xác!";
                    }
                } else {
                    $data['error'] = "Tài khoản không tồn tại!";
                }
            }
        }

        require_once '../app/Views/auth/login.php';
    }

    // --- HÀM ĐIỀU HƯỚNG ---
    private function redirectBasedOnRole() {
        // Kiểm tra Vai trò trong Session
        $role = isset($_SESSION['role']) ? strtoupper($_SESSION['role']) : '';

        if ($role == 'ADMIN') {
            // 1. Nếu là Admin -> Vào trang Thống kê quản trị
            header("Location: " . BASE_URL . "/dashboard/index");
        } elseif ($role == 'HDV') {
            // 2. Nếu là HDV -> Vào trang Lịch làm việc cá nhân (Đây là chỗ cần sửa)
            header("Location: " . BASE_URL . "/guide/index");
        } else {
            // 3. Khách hàng hoặc khác -> Về trang chủ
            header("Location: " . BASE_URL . "/");
        }
        exit;
    }

    // --- ĐĂNG KÝ (Cho khách hàng) ---
    public function register()
    {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('UserModel');

            // 1. Lấy dữ liệu & Validate
            $fullname = trim($_POST['fullname']);
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirm_pass = $_POST['confirm_password'];

            $errors = [];

            if (strlen($fullname) < 2)
                $errors[] = "Họ tên quá ngắn.";
            if (strlen($username) < 4)
                $errors[] = "Tên đăng nhập phải từ 4 ký tự.";
            if (strlen($password) < 6)
                $errors[] = "Mật khẩu phải từ 6 ký tự.";
            if ($password !== $confirm_pass)
                $errors[] = "Mật khẩu nhập lại không khớp.";
            if ($userModel->isUsernameExists($username))
                $errors[] = "Tên đăng nhập đã tồn tại.";

            if (empty($errors)) {
                // 2. Hash mật khẩu
                $hashed_pass = $password;

                $registerData = [
                    'username' => $username,
                    'password' => $hashed_pass, // Lưu password thường
                    'fullname' => $fullname
                ];

                if ($userModel->registerCustomer($registerData)) {
                    // Đăng ký xong chuyển qua login
                    header("Location: " . BASE_URL . "/auth/login?msg=registered");
                    exit;
                } else {
                    $errors[] = "Lỗi hệ thống, vui lòng thử lại.";
                }
            }
            $data['errors'] = $errors;
        }
        require_once '../app/Views/auth/register.php';
    }

    // --- ĐĂNG XUẤT ---
    public function logout()
    {
        session_destroy();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // Hàm phụ: Điều hướng sau khi login
    
}
?>