<?php
require_once __DIR__ . '/../core/Database.php';

class TourPriceModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Lấy giá hiện tại của tour
     */
    public function getCurrentPrice($tourId) {
        $sql = "SELECT * FROM giatour 
                WHERE MaTour = :tourId 
                AND NgayApDung <= CURDATE()
                ORDER BY NgayApDung DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy giá theo ngày
     */
    public function getPriceByDate($tourId, $date) {
        $sql = "SELECT * FROM giatour 
                WHERE MaTour = :tourId 
                AND NgayApDung <= :date
                ORDER BY NgayApDung DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch();
    }
}

