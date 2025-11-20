<?php
require_once __DIR__ . '/../core/Database.php';

class TourImageModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Lấy tất cả ảnh của tour
     */
    public function getByTourId($tourId) {
        $sql = "SELECT * FROM hinhanhtour 
                WHERE MaTour = :tourId 
                ORDER BY LaAnhChinh DESC, ThuTu ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy ảnh chính của tour
     */
    public function getMainImage($tourId) {
        $sql = "SELECT * FROM hinhanhtour 
                WHERE MaTour = :tourId AND LaAnhChinh = 1 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->execute();
        $result = $stmt->fetch();
        
        // Nếu không có ảnh chính, lấy ảnh đầu tiên
        if (!$result) {
            $sql = "SELECT * FROM hinhanhtour 
                    WHERE MaTour = :tourId 
                    ORDER BY ThuTu ASC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tourId', $tourId);
            $stmt->execute();
            $result = $stmt->fetch();
        }
        
        return $result;
    }

    /**
     * Tạo đường dẫn ảnh đầy đủ
     */
    public function getImageUrl($imagePath) {
        if (empty($imagePath)) {
            return BASE_URL . 'public/assets/uploads/anhtour/default.jpg';
        }
        
        // Nếu đã là URL đầy đủ
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Nếu là đường dẫn tương đối
        if (strpos($imagePath, '/') === 0 || strpos($imagePath, 'assets') === 0) {
            return BASE_URL . $imagePath;
        }
        
        return BASE_URL . 'public/assets/uploads/anhtour/' . $imagePath;
    }
}

