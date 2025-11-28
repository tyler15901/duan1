<?php
require_once '../app/Core/Model.php';

class StaffModel extends Model {

    // 1. Lấy danh sách HDV
    public function getAllGuides() {
        // Kết nối bảng nhansu và nguoidung để lấy tên đăng nhập (nếu có)
        $sql = "SELECT ns.*, nd.TenDangNhap, nd.TrangThai as TrangThaiTK 
                FROM nhansu ns 
                LEFT JOIN nguoidung nd ON ns.MaNhanSu = nd.MaNhanSu 
                WHERE ns.LoaiNhanSu = 'HDV' 
                ORDER BY ns.MaNhanSu DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy chi tiết 1 HDV
    public function getGuideById($id) {
        $sql = "SELECT ns.*, nd.TenDangNhap, nd.MaNguoiDung 
                FROM nhansu ns 
                LEFT JOIN nguoidung nd ON ns.MaNhanSu = nd.MaNhanSu 
                WHERE ns.MaNhanSu = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. THÊM HDV MỚI (Transaction: Tạo Nhân sự + Tạo Tài khoản)
    public function createGuide($data) {
        try {
            $this->conn->beginTransaction();

            // A. Insert vào bảng Nhân sự
            $sqlNS = "INSERT INTO nhansu (HoTen, NgaySinh, SoDienThoai, Email, DiaChi, AnhDaiDien, PhanLoai, LoaiNhanSu, TrangThai) 
                      VALUES (:hoten, :dob, :sdt, :email, :diachi, :anh, :phanloai, 'HDV', 'Hoạt động')";
            
            $stmtNS = $this->conn->prepare($sqlNS);
            $stmtNS->execute([
                'hoten' => $data['hoten'],
                'dob' => $data['dob'],
                'sdt' => $data['sdt'],
                'email' => $data['email'],
                'diachi' => $data['diachi'],
                'anh' => $data['anh'],
                'phanloai' => $data['phanloai']
            ]);
            
            $newStaffId = $this->conn->lastInsertId();

            // B. Insert vào bảng Người dùng (Tài khoản)
            $sqlUser = "INSERT INTO nguoidung (TenDangNhap, MatKhau, HoTen, VaiTro, MaNhanSu, TrangThai) 
                        VALUES (:username, :password, :hoten, 'HDV', :staff_id, 'Hoạt động')";
            
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([
                'username' => $data['username'],
                'password' => $data['password'], // Password đã hash ở Controller
                'hoten' => $data['hoten'],
                'staff_id' => $newStaffId
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 4. Cập nhật HDV
    public function updateGuide($id, $data) {
        // Cập nhật thông tin cá nhân
        $sql = "UPDATE nhansu SET HoTen=:hoten, NgaySinh=:dob, SoDienThoai=:sdt, Email=:email, 
                DiaChi=:diachi, PhanLoai=:phanloai WHERE MaNhanSu=:id";
        
        // Nếu có ảnh mới thì update thêm
        if (!empty($data['anh'])) {
            $sql = str_replace("WHERE", ", AnhDaiDien=:anh WHERE", $sql);
        } else {
            unset($data['anh']);
        }
        
        $data['id'] = $id;
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute($data);

        // Nếu có đổi mật khẩu (Logic xử lý ở Controller truyền xuống nếu cần, 
        // nhưng thường trang quản lý HDV chỉ sửa thông tin cá nhân, mật khẩu đổi riêng).
        return $result;
    }

    // 5. Xóa HDV
    public function deleteGuide($id) {
        // Xóa User trước (do khóa ngoại) hoặc xóa Nhân sự (nếu có ON DELETE CASCADE)
        // Ở đây giả định xóa nhân sự
        $stmt = $this->conn->prepare("DELETE FROM nhansu WHERE MaNhanSu = ?");
        return $stmt->execute([$id]);
    }
}
?>