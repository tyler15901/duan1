<?php
require_once __DIR__ . '/../core/Database.php';

class ReportModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get revenue report by time period
     */
    public function getRevenueByTime($year = null, $month = null, $quarter = null) {
        $sql = "SELECT * FROM v_baocaodoanhthu_thoigian WHERE 1=1";
        
        $params = [];
        if ($year) {
            $sql .= " AND Nam = :year";
            $params[':year'] = $year;
        }
        if ($month) {
            $sql .= " AND Thang = :month";
            $params[':month'] = $month;
        }
        if ($quarter) {
            $sql .= " AND Quy = :quarter";
            $params[':quarter'] = $quarter;
        }
        
        $sql .= " ORDER BY Nam DESC, Thang DESC";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get profit report
     */
    public function getProfitReport() {
        $sql = "SELECT * FROM v_baocaoloinhuan ORDER BY LoiNhuan DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(TongTien) as TotalRevenue 
                FROM booking 
                WHERE TrangThai IN ('Đã thanh toán', 'Hoàn tất')";
        
        $params = [];
        if ($startDate) {
            $sql .= " AND NgayDat >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND NgayDat <= :endDate";
            $params[':endDate'] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['TotalRevenue'] ?? 0;
    }

    /**
     * Get total bookings count
     */
    public function getTotalBookings($startDate = null, $endDate = null) {
        $sql = "SELECT COUNT(*) as TotalBookings 
                FROM booking WHERE 1=1";
        
        $params = [];
        if ($startDate) {
            $sql .= " AND NgayDat >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND NgayDat <= :endDate";
            $params[':endDate'] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['TotalBookings'] ?? 0;
    }
}

