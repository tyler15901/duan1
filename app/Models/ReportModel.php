<?php
require_once '../app/Core/Model.php';

class ReportModel extends Model {

    // 1. Lấy dữ liệu để vẽ biểu đồ (12 tháng gần nhất)
    public function getRevenueByTime() {
        // Lấy View SQL đã tạo trong DB
        $sql = "SELECT * FROM v_baocaodoanhthu_thoigian ORDER BY Nam DESC, Thang DESC LIMIT 12"; 
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy bảng chi tiết lợi nhuận từng Tour (Có lọc tháng/năm)
    public function getProfitBySchedule($month = null, $year = null) {
        $sql = "SELECT * FROM v_baocaoloinhuan WHERE 1=1";
        $params = [];

        if ($month && $year) {
            $sql .= " AND MONTH(NgayKhoiHanh) = :m AND YEAR(NgayKhoiHanh) = :y";
            $params = ['m' => $month, 'y' => $year];
        }

        // Sắp xếp ngày mới nhất lên đầu
        $sql .= " ORDER BY NgayKhoiHanh DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>