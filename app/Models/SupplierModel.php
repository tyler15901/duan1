<?php
require_once '../app/Core/Model.php';

class SupplierModel extends Model {
    
    // 1. Lấy tất cả nhà cung cấp
    public function getAllSuppliers() {
        $stmt = $this->conn->query("SELECT * FROM nha_cung_cap ORDER BY MaNhaCungCap DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy chi tiết nhà cung cấp
    public function getSupplierById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM nha_cung_cap WHERE MaNhaCungCap = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Thêm NCC mới
    public function createSupplier($data) {
        $sql = "INSERT INTO nha_cung_cap (TenNhaCungCap, LoaiCungCap, DiaChi, SoDienThoai, TrangThai) 
                VALUES (:ten, :loai, :diachi, :sdt, 'Hoạt động')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // 4. Cập nhật NCC
    public function updateSupplier($id, $data) {
        $sql = "UPDATE nha_cung_cap SET TenNhaCungCap=:ten, LoaiCungCap=:loai, DiaChi=:diachi, SoDienThoai=:sdt, TrangThai=:trangthai 
                WHERE MaNhaCungCap=:id";
        $data['id'] = $id;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // 5. Xóa NCC
    public function deleteSupplier($id) {
        // Xóa NCC sẽ tự động xóa tài nguyên nhờ khóa ngoại CASCADE trong DB
        $stmt = $this->conn->prepare("DELETE FROM nha_cung_cap WHERE MaNhaCungCap = ?");
        return $stmt->execute([$id]);
    }

    // --- QUẢN LÝ TÀI NGUYÊN (XE, PHÒNG) CỦA NCC ---
    
    // Lấy danh sách tài nguyên của 1 NCC
    public function getResourcesBySupplier($supplierId) {
        $stmt = $this->conn->prepare("SELECT * FROM tai_nguyen_ncc WHERE MaNhaCungCap = ?");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm tài nguyên mới (VD: Thêm xe 29B-12345)
    public function addResource($supplierId, $name, $capacity, $note) {
        $sql = "INSERT INTO tai_nguyen_ncc (MaNhaCungCap, TenTaiNguyen, SoLuongCho, GhiChu) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$supplierId, $name, $capacity, $note]);
    }

    // Xóa tài nguyên
    public function deleteResource($resourceId) {
        $stmt = $this->conn->prepare("DELETE FROM tai_nguyen_ncc WHERE MaTaiNguyen = ?");
        return $stmt->execute([$resourceId]);
    }
}
?>