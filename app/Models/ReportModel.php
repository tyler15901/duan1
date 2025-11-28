<?php
require_once '../app/Core/Model.php';

class ReportModel extends Model {

    // Báo cáo lợi nhuận theo từng Lịch khởi hành (Chi tiết)
    public function getProfitBySchedule($month = null, $year = null) {
        $sql = "SELECT * FROM v_baocaoloinhuan WHERE 1=1";
        $params = [];

        if ($month && $year) {
            $sql .= " AND MONTH(NgayKhoiHanh) = :m AND YEAR(NgayKhoiHanh) = :y";
            $params = ['m' => $month, 'y' => $year];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Báo cáo doanh thu theo Tháng (Để vẽ biểu đồ)
    public function getRevenueByTime() {
        // Lấy dữ liệu 12 tháng gần nhất
        $sql = "SELECT * FROM v_baocaodoanhthu_thoigian LIMIT 12"; 
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>