<?php
require_once __DIR__ . '/../core/Database.php';

class BookingModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get all bookings
     */
    public function getAll() {
        $sql = "SELECT b.*, t.TenTour, kh.HoTen as TenKhachHang
                FROM booking b
                LEFT JOIN tour t ON b.MaTour = t.MaTour
                LEFT JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                ORDER BY b.NgayDat DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get booking by ID
     */
    public function getById($id) {
        $sql = "SELECT b.*, t.TenTour, kh.HoTen as TenKhachHang
                FROM booking b
                LEFT JOIN tour t ON b.MaTour = t.MaTour
                LEFT JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                WHERE b.MaBooking = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new booking
     */
    public function create($data) {
        $sql = "INSERT INTO booking (MaTour, MaKhachHang, NgayKhoiHanh, SoLuongKhach, TongTien, TienCoc, TrangThai, NguoiTao, GhiChu) 
                VALUES (:maTour, :maKhachHang, :ngayKhoiHanh, :soLuongKhach, :tongTien, :tienCoc, :trangThai, :nguoiTao, :ghiChu)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':maTour', $data['MaTour']);
        $stmt->bindParam(':maKhachHang', $data['MaKhachHang'] ?? null);
        $stmt->bindParam(':ngayKhoiHanh', $data['NgayKhoiHanh']);
        $stmt->bindParam(':soLuongKhach', $data['SoLuongKhach']);
        $stmt->bindParam(':tongTien', $data['TongTien'] ?? null);
        $stmt->bindParam(':tienCoc', $data['TienCoc'] ?? null);
        $stmt->bindParam(':trangThai', $data['TrangThai'] ?? 'Chờ xác nhận');
        $stmt->bindParam(':nguoiTao', $data['NguoiTao'] ?? null);
        $stmt->bindParam(':ghiChu', $data['GhiChu'] ?? null);
        return $stmt->execute();
    }

    /**
     * Update booking
     */
    public function update($id, $data) {
        $sql = "UPDATE booking 
                SET MaTour = :maTour, MaKhachHang = :maKhachHang, NgayKhoiHanh = :ngayKhoiHanh, 
                    SoLuongKhach = :soLuongKhach, TongTien = :tongTien, TienCoc = :tienCoc, 
                    TrangThai = :trangThai, GhiChu = :ghiChu
                WHERE MaBooking = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':maTour', $data['MaTour']);
        $stmt->bindParam(':maKhachHang', $data['MaKhachHang'] ?? null);
        $stmt->bindParam(':ngayKhoiHanh', $data['NgayKhoiHanh']);
        $stmt->bindParam(':soLuongKhach', $data['SoLuongKhach']);
        $stmt->bindParam(':tongTien', $data['TongTien'] ?? null);
        $stmt->bindParam(':tienCoc', $data['TienCoc'] ?? null);
        $stmt->bindParam(':trangThai', $data['TrangThai']);
        $stmt->bindParam(':ghiChu', $data['GhiChu'] ?? null);
        return $stmt->execute();
    }

    /**
     * Delete booking
     */
    public function delete($id) {
        $sql = "DELETE FROM booking WHERE MaBooking = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Get bookings by status
     */
    public function getByStatus($status) {
        $sql = "SELECT b.*, t.TenTour, kh.HoTen as TenKhachHang
                FROM booking b
                LEFT JOIN tour t ON b.MaTour = t.MaTour
                LEFT JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                WHERE b.TrangThai = :status
                ORDER BY b.NgayDat DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

