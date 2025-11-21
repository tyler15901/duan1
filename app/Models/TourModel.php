<?php
// app/Models/TourModel.php

class TourModel extends BaseModel {
    protected $table = 'tour';
    protected $primaryKey = 'MaTour';

    // ==================================================================
    // 1. NHÓM HÀM HIỂN THỊ DANH SÁCH (HOME & LIST)
    // ==================================================================

    /**
     * Lấy tour nổi bật (Ngẫu nhiên) cho mục Ưu Đãi
     */
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

    /**
     * Lấy tour theo loại (Trong nước / Quốc tế)
     */
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

    /**
     * [MỚI] Tìm kiếm tour (Dùng cho thanh search ở Banner)
     */
    public function searchTours($keyword, $date = null) {
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động' 
                AND (t.TenTour LIKE :keyword OR t.MoTa LIKE :keyword)";

        $params = [':keyword' => "%$keyword%"];

        // Nếu có chọn ngày (Tìm trong lịch khởi hành)
        if (!empty($date)) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM lichkhoihanh lkh 
                        WHERE lkh.MaTour = t.MaTour 
                        AND lkh.NgayKhoiHanh = :date
                      )";
            $params[':date'] = $date;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================================================================
    // 2. NHÓM HÀM CHI TIẾT (DETAIL)
    // ==================================================================

    /**
     * Lấy chi tiết tour + TỰ ĐỘNG QUÉT ẢNH TRONG THƯ MỤC
     */
    public function getTourDetail($id) {
        // 1. Lấy thông tin cơ bản
        $tour = $this->find($id);
        if (!$tour) return null;

        // 2. Lấy giá
        $sqlPrice = "SELECT Gia FROM giatour WHERE MaTour = :id AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1";
        $stmtPrice = $this->conn->prepare($sqlPrice);
        $stmtPrice->execute(['id' => $id]);
        $price = $stmtPrice->fetch();
        $tour['GiaHienTai'] = $price ? $price['Gia'] : 0;

        // 3. XỬ LÝ GALLERY (Đã dọn dẹp code thừa)
        $tour['gallery'] = [];
        $dbPath = $tour['HinhAnh']; 

        if (!empty($dbPath)) {
            // Luôn thêm ảnh bìa vào trước
            $tour['gallery'][] = $dbPath;
            
            // Logic đường dẫn: Dùng __DIR__ đi lùi ra folder public/uploads
            $folderRelative = dirname($dbPath);
            $folderAbsolute = __DIR__ . '/../../public/uploads/' . $folderRelative;

            // Quét folder
            if (is_dir($folderAbsolute)) {
                $files = glob($folderAbsolute . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}', GLOB_BRACE);
                if ($files) {
                    foreach ($files as $filePath) {
                        // Chuẩn hóa đường dẫn Windows (\) sang Web (/)
                        $filePath = str_replace('\\', '/', $filePath);
                        
                        // Tách lấy phần đuôi sau chữ 'uploads/'
                        $parts = explode('uploads/', $filePath);
                        if (isset($parts[1])) {
                            $imgName = $parts[1];
                            // Tránh trùng lặp ảnh bìa
                            if (!in_array($imgName, $tour['gallery'])) {
                                $tour['gallery'][] = $imgName;
                            }
                        }
                    }
                }
            } 
        }
        
        return $tour;
    }

    /**
     * [MỚI] Lấy các tour liên quan (Cùng loại) để hiện ở cuối trang chi tiết
     */
    public function getRelatedTours($tourId, $typeId, $limit = 4) {
        $sql = "SELECT t.*, 
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                WHERE t.MaLoaiTour = :typeId 
                AND t.MaTour != :id -- Trừ chính nó ra
                AND t.TrangThai = 'Hoạt động'
                ORDER BY RAND()
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId, 'typeId' => $typeId, 'limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function getItinerary($tourId) {
        $sql = "SELECT * FROM lichtrinhtour WHERE MaTour = :id ORDER BY NgayThu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }

    public function getSchedules($tourId) {
        $sql = "SELECT * FROM lichkhoihanh 
                WHERE MaTour = :id 
                  AND NgayKhoiHanh >= CURDATE() 
                  AND TrangThai = 'Nhận khách' 
                ORDER BY NgayKhoiHanh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $tourId]);
        return $stmt->fetchAll();
    }
    // 1. Lấy danh sách tour có Phân trang & Sắp xếp
    public function getToursPaginated($page = 1, $perPage = 5, $sort = 'newest', $search = '') {
        $offset = ($page - 1) * $perPage;
        
        // Base SQL
        $sql = "SELECT t.*, lt.TenLoai,
                    (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' AND NgayBatDau <= CURDATE() ORDER BY NgayBatDau DESC LIMIT 1) AS GiaHienTai
                FROM tour t
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                WHERE t.TrangThai = 'Hoạt động'";

        // Xử lý Tìm kiếm (Nếu có)
        if (!empty($search)) {
            $sql .= " AND (t.TenTour LIKE :search OR t.MoTa LIKE :search)";
        }

        // Xử lý Sắp xếp
        switch ($sort) {
            case 'price-asc': $sql .= " ORDER BY GiaHienTai ASC"; break;
            case 'price-desc': $sql .= " ORDER BY GiaHienTai DESC"; break;
            case 'duration': $sql .= " ORDER BY t.SoNgay ASC"; break;
            default: $sql .= " ORDER BY t.NgayTao DESC"; // Mặc định mới nhất
        }

        // Phân trang
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 2. Đếm tổng số tour (Để tính số trang)
    public function countTotalTours($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tour WHERE TrangThai = 'Hoạt động'";
        if (!empty($search)) {
            $sql .= " AND (TenTour LIKE :search OR MoTa LIKE :search)";
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}