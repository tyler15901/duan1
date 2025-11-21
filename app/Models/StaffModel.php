<?php
class StaffModel extends BaseModel {
    protected $table = 'nhansu';
    protected $primaryKey = 'MaNhanSu';

    /**
     * Lấy danh sách nhân sự (Có thể lọc theo loại: HDV, Tài xế...)
     */
    public function getAllStaff($type = null) {
        $sql = "SELECT * FROM nhansu";
        if ($type) {
            $sql .= " WHERE LoaiNhanSu = :type";
        }
        $sql .= " ORDER BY MaNhanSu DESC";
        
        $stmt = $this->conn->prepare($sql);
        if ($type) {
            $stmt->execute(['type' => $type]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Thêm nhân sự mới
     */
    public function createStaff($data) {
        $sql = "INSERT INTO nhansu (HoTen, SoDienThoai, Email, LoaiNhanSu, TrangThai) 
                VALUES (:ten, :sdt, :email, :loai, 'Hoạt động')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'ten' => $data['name'],
            'sdt' => $data['phone'],
            'email' => $data['email'],
            'loai' => $data['role']
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * Xóa nhân sự
     */
    public function deleteStaff($id) {
        $sql = "DELETE FROM nhansu WHERE MaNhanSu = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}