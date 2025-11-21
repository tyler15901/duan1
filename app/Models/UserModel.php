<?php
class UserModel extends BaseModel {
    protected $table = 'nguoidung';
    protected $primaryKey = 'MaNguoiDung'; // Khớp với ảnh

    // Kiểm tra trùng tên đăng nhập
    public function checkUsername($username) {
        $sql = "SELECT * FROM nguoidung WHERE TenDangNhap = :u LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['u' => $username]);
        return $stmt->fetch();
    }

    // Đăng ký (Password thô + Đúng cột trong ảnh)
    public function register($data) {
        // Chuẩn bị dữ liệu khớp với bảng
        $insertData = [
            'TenDangNhap' => $data['TenDangNhap'],
            'MatKhau'     => $data['MatKhau'], // Lưu thô
            'HoTen'       => $data['HoTen'],
            'VaiTro'      => 'KhachHang',      // Mặc định là Khách
            'TrangThai'   => 'Hoạt động',
            'MaNhanSu'    => null              // Cột này cho phép NULL nên ta để null
        ];

        return $this->create($insertData);
    }

    // Lấy user để login
    public function getUserForLogin($username) {
        $sql = "SELECT * FROM nguoidung WHERE TenDangNhap = :u AND TrangThai = 'Hoạt động' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['u' => $username]);
        return $stmt->fetch();
    }
}