<?php
require_once __DIR__ . '/../core/Database.php';

class TourModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get all tours
     */
    public function getAll($status = null) {
        $sql = "SELECT t.*, lt.TenLoaiTour 
                FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour";
        
        if ($status) {
            $sql .= " WHERE t.TrangThai = :status";
        }
        
        $sql .= " ORDER BY t.NgayTao DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get tour by ID
     */
    public function getById($id) {
        $sql = "SELECT t.*, lt.TenLoaiTour 
                FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.MaTour = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new tour
     */
    public function create($data) {
        $sql = "INSERT INTO tour (MaLoaiTour, TenTour, MoTa, SoNgay, SoChoToiDa, TrangThai) 
                VALUES (:maLoaiTour, :tenTour, :moTa, :soNgay, :soChoToiDa, :trangThai)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':maLoaiTour', $data['MaLoaiTour']);
        $stmt->bindParam(':tenTour', $data['TenTour']);
        $stmt->bindParam(':moTa', $data['MoTa']);
        $stmt->bindParam(':soNgay', $data['SoNgay']);
        $stmt->bindParam(':soChoToiDa', $data['SoChoToiDa'] ?? 40);
        $stmt->bindParam(':trangThai', $data['TrangThai'] ?? 'Hoạt động');
        return $stmt->execute();
    }

    /**
     * Update tour
     */
    public function update($id, $data) {
        $sql = "UPDATE tour 
                SET MaLoaiTour = :maLoaiTour, TenTour = :tenTour, MoTa = :moTa, 
                    SoNgay = :soNgay, SoChoToiDa = :soChoToiDa, TrangThai = :trangThai
                WHERE MaTour = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':maLoaiTour', $data['MaLoaiTour']);
        $stmt->bindParam(':tenTour', $data['TenTour']);
        $stmt->bindParam(':moTa', $data['MoTa']);
        $stmt->bindParam(':soNgay', $data['SoNgay']);
        $stmt->bindParam(':soChoToiDa', $data['SoChoToiDa']);
        $stmt->bindParam(':trangThai', $data['TrangThai']);
        return $stmt->execute();
    }

    /**
     * Delete tour
     */
    public function delete($id) {
        $sql = "DELETE FROM tour WHERE MaTour = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Get tour types
     */
    public function getTourTypes() {
        $sql = "SELECT * FROM loaitour ORDER BY TenLoaiTour";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get featured tours (ưu đãi)
     */
    public function getFeaturedTours($type = 'sale', $limit = 6) {
        $sql = "SELECT t.*, lt.TenLoaiTour, 
                (SELECT MIN(gt.GiaNguoiLon) FROM giatour gt WHERE gt.MaTour = t.MaTour) as MinPrice
                FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động'
                ORDER BY t.NgayTao DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get tours by type
     */
    public function getToursByType($typeId, $limit = null) {
        $sql = "SELECT t.*, lt.TenLoaiTour 
                FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.MaLoaiTour = :typeId AND t.TrangThai = 'Hoạt động'
                ORDER BY t.NgayTao DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':typeId', $typeId);
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get tour itinerary
     */
    public function getItinerary($tourId) {
        $sql = "SELECT * FROM lichtrinhtour 
                WHERE MaTour = :tourId 
                ORDER BY NgayThu ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get tour schedules
     */
    public function getSchedules($tourId) {
        $sql = "SELECT * FROM lichkhoihanh 
                WHERE MaTour = :tourId 
                AND NgayKhoiHanh >= CURDATE()
                AND TrangThai != 'Đã hủy'
                ORDER BY NgayKhoiHanh ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get related tours
     */
    public function getRelatedTours($tourId, $typeId, $limit = 4) {
        $sql = "SELECT t.*, lt.TenLoaiTour 
                FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.MaLoaiTour = :typeId 
                AND t.MaTour != :tourId 
                AND t.TrangThai = 'Hoạt động'
                ORDER BY t.NgayTao DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':typeId', $typeId);
        $stmt->bindParam(':tourId', $tourId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

