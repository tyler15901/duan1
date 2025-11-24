<?php
class AllocationModel extends BaseModel {
    protected $table = 'phanbonhansu';
    protected $primaryKey = 'MaPhanBo';

    // 1. Lấy danh sách HDV đã được phân công cho lịch này
    public function getAssignedStaff($scheduleId) {
        $sql = "SELECT pb.*, ns.HoTen, ns.SoDienThoai, ns.Email 
                FROM phanbonhansu pb
                JOIN nhansu ns ON pb.MaNhanSu = ns.MaNhanSu
                WHERE pb.MaLichKhoiHanh = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $scheduleId]);
        return $stmt->fetchAll();
    }

    // 2. Phân công HDV vào lịch
    public function assignStaff($scheduleId, $staffId) {
        // Kiểm tra xem đã phân công chưa để tránh trùng
        $check = "SELECT COUNT(*) FROM phanbonhansu WHERE MaLichKhoiHanh=:lkh AND MaNhanSu=:ns";
        $stmtCheck = $this->conn->prepare($check);
        $stmtCheck->execute(['lkh' => $scheduleId, 'ns' => $staffId]);
        
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Đã tồn tại
        }

        // Nếu chưa thì thêm mới (VaiTro mặc định là HDV theo yêu cầu của bạn)
        $sql = "INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (:lkh, :ns, 'HDV')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['lkh' => $scheduleId, 'ns' => $staffId]);
    }

    // 3. Xóa phân công (Hủy lịch dẫn của HDV)
    public function removeAssignment($id) {
        $sql = "DELETE FROM phanbonhansu WHERE MaPhanBo = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    // --- DÀNH CHO GIAO DIỆN HDV ---
    
    // 4. Lấy lịch được phân công cho HDV (Dựa theo tên)
    public function getMySchedules($guideName) {
        $sql = "SELECT lkh.*, t.TenTour, t.SoNgay, t.HinhAnh
                FROM phanbonhansu pb
                JOIN lichkhoihanh lkh ON pb.MaLichKhoiHanh = lkh.MaLichKhoiHanh
                JOIN tour t ON lkh.MaTour = t.MaTour
                JOIN nhansu ns ON pb.MaNhanSu = ns.MaNhanSu
                WHERE ns.HoTen = :name 
                  AND lkh.NgayKhoiHanh >= CURDATE() -- Chỉ hiện lịch tương lai
                ORDER BY lkh.NgayKhoiHanh ASC";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['name' => $guideName]);
        return $stmt->fetchAll();
    }
}