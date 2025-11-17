<?php
require_once __DIR__ . '/../core/Database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $sql = "SELECT * FROM nguoidung WHERE TenDangNhap = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM nguoidung WHERE MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Login user
     */
    public function login($username, $password) {
        $user = $this->getByUsername($username);
        
        if ($user && $this->verifyPassword($password, $user['MatKhau'])) {
            if ($user['TrangThai'] === 'Hoạt động') {
                return $user;
            }
        }
        
        return false;
    }

    /**
     * Get all users
     */
    public function getAll() {
        $sql = "SELECT * FROM nguoidung ORDER BY HoTen";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Create new user
     */
    public function create($data) {
        $sql = "INSERT INTO nguoidung (TenDangNhap, MatKhau, HoTen, VaiTro, MaNhanSu, TrangThai) 
                VALUES (:tenDangNhap, :matKhau, :hoTen, :vaiTro, :maNhanSu, :trangThai)";
        $stmt = $this->db->prepare($sql);
        $hashedPassword = password_hash($data['MatKhau'], PASSWORD_DEFAULT);
        $stmt->bindParam(':tenDangNhap', $data['TenDangNhap']);
        $stmt->bindParam(':matKhau', $hashedPassword);
        $stmt->bindParam(':hoTen', $data['HoTen']);
        $stmt->bindParam(':vaiTro', $data['VaiTro']);
        $stmt->bindParam(':maNhanSu', $data['MaNhanSu'] ?? null);
        $stmt->bindParam(':trangThai', $data['TrangThai'] ?? 'Hoạt động');
        return $stmt->execute();
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $sql = "UPDATE nguoidung 
                SET HoTen = :hoTen, VaiTro = :vaiTro, MaNhanSu = :maNhanSu, TrangThai = :trangThai";
        
        if (!empty($data['MatKhau'])) {
            $sql .= ", MatKhau = :matKhau";
        }
        
        $sql .= " WHERE MaNguoiDung = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':hoTen', $data['HoTen']);
        $stmt->bindParam(':vaiTro', $data['VaiTro']);
        $stmt->bindParam(':maNhanSu', $data['MaNhanSu'] ?? null);
        $stmt->bindParam(':trangThai', $data['TrangThai']);
        
        if (!empty($data['MatKhau'])) {
            $hashedPassword = password_hash($data['MatKhau'], PASSWORD_DEFAULT);
            $stmt->bindParam(':matKhau', $hashedPassword);
        }
        
        return $stmt->execute();
    }
}

