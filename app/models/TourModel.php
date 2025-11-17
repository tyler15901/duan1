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
}

