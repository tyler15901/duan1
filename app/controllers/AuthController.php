<?php
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Login page and process
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($username, $password);

            if ($user) {
                Session::setUser($user);
                header('Location: ' . BASE_URL);
                exit;
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }
        }

        require __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Logout
     */
    public function logout() {
        Session::logout();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

