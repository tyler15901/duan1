<?php
// app/Models/TourModel.php
class TourModel extends BaseModel {
    protected $table = 'tour';
    protected $primaryKey = 'MaTour';

    /**
     * Lấy danh sách tour cho trang chủ
     * Logic: Lấy tour hoạt động + Loại tour + Giá người lớn hiện hành
     */
    public function getTourList($limit = 10) {
        $sql = "SELECT 
                    t.*, 
                    lt.TenLoai,
                    -- Subquery lấy giá Người lớn, còn hạn sử dụng (NgayKetThuc chưa đến hoặc NULL)
                    (SELECT Gia FROM giatour 
                     WHERE MaTour = t.MaTour 
                       AND DoiTuong = 'Người lớn' 
                       AND NgayBatDau <= CURDATE() 
                       AND (NgayKetThuc IS NULL OR NgayKetThuc >= CURDATE())
                     ORDER BY NgayBatDau DESC 
                     LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động'
                ORDER BY t.NgayTao DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết 1 tour (Dùng cho trang detail sau này)
     */
    public function getById($id) {
        return $this->find($id);
    }
    /**
     * Lấy danh sách lịch trình (Ngày 1, Ngày 2...)
     */
    public function getItinerary($tourId) {
        $sql = "SELECT * FROM lichtrinhtour 
                WHERE MaTour = :id 
                ORDER BY NgayThu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch khởi hành sắp tới (Chỉ lấy ngày tương lai)
     */
    public function getSchedules($tourId) {
        $sql = "SELECT * FROM lichkhoihanh 
                WHERE MaTour = :id 
                  AND NgayKhoiHanh >= CURDATE() -- Chỉ lấy ngày chưa đi
                  AND TrangThai = 'Nhận khách'   -- Chỉ lấy lịch đang mở
                ORDER BY NgayKhoiHanh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }

    public function getTourDetail($id) {
        // 1. Lấy thông tin tour chính
        $tour = $this->find($id);
        if (!$tour) return null;

        // 2. Lấy giá (như cũ)
        $sqlPrice = "SELECT Gia FROM giatour WHERE MaTour = :id AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1";
        $stmtPrice = $this->conn->prepare($sqlPrice);
        $stmtPrice->execute(['id' => $id]);
        $price = $stmtPrice->fetch();
        $tour['GiaHienTai'] = $price ? $price['Gia'] : 0;

        // 3. LẤY THƯ VIỆN ẢNH (Đoạn này quan trọng)
        // Lấy tất cả ảnh trong bảng hinhanhtour của tour này
        $sqlImg = "SELECT DuongDan FROM hinhanhtour WHERE MaTour = :id";
        $stmtImg = $this->conn->prepare($sqlImg);
        $stmtImg->execute(['id' => $id]);
        
        // Trả về mảng các đường dẫn: ['anhtour/.../seoul2.jpg', '...']
        $tour['gallery'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN); 

        return $tour;
    }
}

