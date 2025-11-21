<?php
class TourModel extends BaseModel {
    protected $table = 'tour';
    protected $primaryKey = 'MaTour';

    // ==================================================================
    // PHẦN 1: HIỂN THỊ CHO KHÁCH (FRONTEND)
    // ==================================================================

    /**
     * Lấy danh sách tour chung (Dùng cho Admin hoặc trang chủ)
     * Fix lỗi: Call to undefined method TourModel::getTourList()
     */
    
    public function getTourList($limit = 100) {
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                ORDER BY t.NgayTao DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy tour ưu đãi (Ngẫu nhiên)
    public function getFeaturedTours($limit = 6) {
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động'
                ORDER BY RAND() 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy tour theo loại
    public function getToursByType($typeId, $limit = 8) {
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động' AND t.MaLoaiTour = :typeId
                ORDER BY t.NgayTao DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':typeId', $typeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy chi tiết tour + Quét ảnh
    public function getTourDetail($id) {
        $tour = $this->find($id);
        if (!$tour) return null;

        // Lấy giá
        $sqlPrice = "SELECT Gia FROM giatour WHERE MaTour = :id AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1";
        $stmtPrice = $this->conn->prepare($sqlPrice);
        $stmtPrice->execute(['id' => $id]);
        $price = $stmtPrice->fetch();
        $tour['GiaHienTai'] = $price ? $price['Gia'] : 0;

        // Lấy Gallery
        $tour['gallery'] = [];
        $dbPath = $tour['HinhAnh']; 
        if (!empty($dbPath)) {
            $tour['gallery'][] = $dbPath;
            // Quét folder lấy thêm ảnh
            $folderRelative = dirname($dbPath);
            $folderAbsolute = __DIR__ . '/../../public/uploads/' . $folderRelative;
            if (is_dir($folderAbsolute)) {
                $files = glob($folderAbsolute . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}', GLOB_BRACE);
                if ($files) {
                    foreach ($files as $filePath) {
                        $filePath = str_replace('\\', '/', $filePath);
                        $parts = explode('uploads/', $filePath);
                        if (isset($parts[1])) {
                            $imgName = $parts[1];
                            if (!in_array($imgName, $tour['gallery'])) $tour['gallery'][] = $imgName;
                        }
                    }
                }
            } 
        }
        return $tour;
    }

    // Lấy lịch trình
    public function getItinerary($tourId) {
        $sql = "SELECT * FROM lichtrinhtour WHERE MaTour = :id ORDER BY NgayThu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }

    // Lấy lịch khởi hành
    public function getSchedules($tourId) {
        $sql = "SELECT * FROM lichkhoihanh WHERE MaTour = :id AND NgayKhoiHanh >= CURDATE() AND TrangThai = 'Nhận khách' ORDER BY NgayKhoiHanh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }

    // Lấy tour liên quan
    public function getRelatedTours($tourId, $typeId, $limit = 4) {
        $sql = "SELECT t.*, (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                WHERE t.MaLoaiTour = :typeId AND t.MaTour != :id AND t.TrangThai = 'Hoạt động'
                ORDER BY RAND() LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId, 'typeId' => $typeId, 'limit' => $limit]);
        return $stmt->fetchAll();
    }

    // Phân trang (Cho trang danh sách)
    public function getToursPaginated($page = 1, $perPage = 5, $sort = 'newest', $search = '') {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour WHERE t.TrangThai = 'Hoạt động'";
        if (!empty($search)) $sql .= " AND (t.TenTour LIKE :search OR t.MoTa LIKE :search)";
        
        switch ($sort) {
            case 'price-asc': $sql .= " ORDER BY GiaHienTai ASC"; break;
            case 'price-desc': $sql .= " ORDER BY GiaHienTai DESC"; break;
            default: $sql .= " ORDER BY t.NgayTao DESC";
        }
        $sql .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        if (!empty($search)) $stmt->bindValue(':search', "%$search%");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countTotalTours($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tour WHERE TrangThai = 'Hoạt động'";
        if (!empty($search)) $sql .= " AND (TenTour LIKE :search OR MoTa LIKE :search)";
        $stmt = $this->conn->prepare($sql);
        if (!empty($search)) $stmt->bindValue(':search', "%$search%");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // ==================================================================
    // PHẦN 2: QUẢN TRỊ (ADMIN CRUD)
    // ==================================================================

    // 1. Thêm Tour mới
    public function createTour($data) {
        $sql = "INSERT INTO tour (MaLoaiTour, TenTour, HinhAnh, MoTa, SoNgay, SoChoToiDa, TrangThai) 
                VALUES (:loai, :ten, :anh, :mota, :songay, :socho, :trangthai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'loai' => $data['MaLoaiTour'],
            'ten' => $data['TenTour'],
            'anh' => $data['HinhAnh'],
            'mota' => $data['MoTa'],
            'songay' => $data['SoNgay'],
            'socho' => $data['SoChoToiDa'],
            'trangthai' => $data['TrangThai']
        ]);
        return $this->conn->lastInsertId();
    }

    // 2. Cập nhật Tour
    public function updateTour($id, $data) {
        $sql = "UPDATE tour SET MaLoaiTour=:loai, TenTour=:ten, MoTa=:mota, SoNgay=:songay, SoChoToiDa=:socho, TrangThai=:trangthai WHERE MaTour=:id";
        if (!empty($data['HinhAnh'])) {
            $sql = "UPDATE tour SET MaLoaiTour=:loai, TenTour=:ten, HinhAnh=:anh, MoTa=:mota, SoNgay=:songay, SoChoToiDa=:socho, TrangThai=:trangthai WHERE MaTour=:id";
        }
        $stmt = $this->conn->prepare($sql);
        $params = [
            'loai' => $data['MaLoaiTour'],
            'ten' => $data['TenTour'],
            'mota' => $data['MoTa'],
            'songay' => $data['SoNgay'],
            'socho' => $data['SoChoToiDa'],
            'trangthai' => $data['TrangThai'],
            'id' => $id
        ];
        if (!empty($data['HinhAnh'])) $params['anh'] = $data['HinhAnh'];
        return $stmt->execute($params);
    }

    // 3. Xóa Tour
    public function deleteTour($id) {
        $sql = "DELETE FROM tour WHERE MaTour = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // 4. Thêm giá khởi điểm
    public function addInitialPrice($tourId, $price) {
        $sql = "INSERT INTO giatour (MaTour, DoiTuong, Gia, NgayBatDau) VALUES (:id, 'Người lớn', :gia, CURDATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId, 'gia' => $price]);
    }
}