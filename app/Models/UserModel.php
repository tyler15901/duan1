<?php
require_once '../app/Core/Model.php';

class UserModel extends Model {

    // Lấy thông tin người dùng qua Username
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM nguoidung WHERE TenDangNhap = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra Username đã tồn tại chưa
    public function isUsernameExists($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM nguoidung WHERE TenDangNhap = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    // Đăng ký tài khoản Khách hàng mới
    public function registerCustomer($data) {
        // Mặc định VaiTro là KhachHang
        $sql = "INSERT INTO nguoidung (TenDangNhap, MatKhau, HoTen, VaiTro, TrangThai, Avatar) 
                VALUES (:user, :pass, :name, 'KhachHang', 'Hoạt động', NULL)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'user' => $data['username'],
            'pass' => $data['password'], // Password đã hash
            'name' => $data['fullname']
        ]);
    }
}
?>