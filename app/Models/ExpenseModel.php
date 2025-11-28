<?php
require_once '../app/Core/Model.php';

class ExpenseModel extends Model {

    // Lấy danh sách chi phí của 1 lịch
    public function getExpensesBySchedule($scheduleId) {
        $stmt = $this->conn->prepare("SELECT * FROM chiphi WHERE MaLichKhoiHanh = ?");
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm chi phí mới
    public function addExpense($data) {
        $sql = "INSERT INTO chiphi (MaLichKhoiHanh, LoaiChiPhi, SoTien, GhiChu, NgayChi) 
                VALUES (:lich_id, :loai, :tien, :ghichu, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'lich_id' => $data['lich_id'],
            'loai' => $data['loai'],
            'tien' => $data['tien'],
            'ghichu' => $data['ghichu']
        ]);
    }

    // Xóa chi phí
    public function deleteExpense($id) {
        $stmt = $this->conn->prepare("DELETE FROM chiphi WHERE MaChiPhi = ?");
        return $stmt->execute([$id]);
    }

    // Lấy tổng chi phí của 1 lịch
    public function getTotalExpense($scheduleId) {
        $stmt = $this->conn->prepare("SELECT SUM(SoTien) FROM chiphi WHERE MaLichKhoiHanh = ?");
        $stmt->execute([$scheduleId]);
        return $stmt->fetchColumn() ?: 0;
    }
}
?>