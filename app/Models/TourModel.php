<?php
require_once '../app/Core/Model.php';

class TourModel extends Model {
    // Lấy danh sách tour kèm tên danh mục
    public function getAllTours() {
        $sql = "SELECT t.*, lt.TenLoai FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour 
                ORDER BY t.MaTour DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm tour mới
    // Sửa lại hàm createTour để trả về ID vừa tạo
    public function createTour($data) {
        $sql = "INSERT INTO tour (TenTour, MaLoaiTour, HinhAnh, SoNgay, SoChoToiDa, MoTa, ChinhSach, TrangThai, NgayTao) 
                VALUES (:TenTour, :MaLoaiTour, :HinhAnh, :SoNgay, :SoChoToiDa, :MoTa, :ChinhSach, :TrangThai, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute($data)) {
            // [BẮT BUỘC] Phải trả về ID vừa insert
            return $this->conn->lastInsertId(); 
        }
        return false;
    }

    // Thêm lịch trình chi tiết
    public function addSchedule($tourId, $dayNum, $title, $content) {
        $sql = "INSERT INTO lichtrinhtour (MaTour, NgayThu, TieuDe, NoiDung) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tourId, $dayNum, $title, $content]);
    }

    // Thêm giá (vào bảng giatour)
    public function addPrice($tourId, $type, $price) {
        // Mặc định ngày bắt đầu là hôm nay, kết thúc sau 1 năm
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 year'));
        
        $sql = "INSERT INTO giatour (MaTour, DoiTuong, Gia, NgayBatDau, NgayKetThuc) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tourId, $type, $price, $startDate, $endDate]);
    }

    // Thêm ảnh Gallery
    public function addGalleryImage($tourId, $path) {
        // Lưu đường dẫn tương đối để dễ gọi
        $fullPath = "assets/uploads/" . $path; 
        $sql = "INSERT INTO hinhanhtour (MaTour, DuongDan) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tourId, $fullPath]);
    }

    // Lấy danh sách loại tour (để hiển thị trong dropdown khi thêm/sửa)
    public function getCategories(): array {
        $stmt = $this->conn->query("SELECT * FROM loaitour");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- LẤY DỮ LIỆU CHI TIẾT ---
    public function getTourById($id): mixed {
        $sql = "SELECT t.*, lt.TenLoai FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour 
                WHERE t.MaTour = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGallery($id) {
        $stmt = $this->conn->prepare("SELECT * FROM hinhanhtour WHERE MaTour = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedule($id) {
        $stmt = $this->conn->prepare("SELECT * FROM lichtrinhtour WHERE MaTour = ? ORDER BY NgayThu ASC");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrices($id) {
        $stmt = $this->conn->prepare("SELECT * FROM giatour WHERE MaTour = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CẬP NHẬT (UPDATE) ---
    public function updateTour($id, $data) {
        $sql = "UPDATE tour SET 
                TenTour=:TenTour, MaLoaiTour=:MaLoaiTour, SoNgay=:SoNgay, 
                SoChoToiDa=:SoChoToiDa, MoTa=:MoTa, ChinhSach=:ChinhSach, TrangThai=:TrangThai 
                WHERE MaTour=:MaTour";
        // Nếu có cập nhật ảnh đại diện thì nối thêm SQL
        if (!empty($data['HinhAnh'])) {
            $sql = str_replace("WHERE", ", HinhAnh=:HinhAnh WHERE", $sql);
        } else {
            unset($data['HinhAnh']); // Bỏ key HinhAnh nếu không update
        }

        $data['MaTour'] = $id; // Thêm ID vào mảng data để bind param
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Xóa sạch lịch trình cũ để lưu cái mới (Cách đơn giản nhất khi sửa lịch trình)
    public function resetSchedule($tourId) {
        $stmt = $this->conn->prepare("DELETE FROM lichtrinhtour WHERE MaTour = ?");
        return $stmt->execute([$tourId]);
    }

    // --- XÓA (DELETE) ---
    public function deleteTour($id) {
        // Do DB đã cài đặt ON DELETE CASCADE ở các bảng con (hinhanhtour, lichtrinhtour...)
        // Nên chỉ cần xóa bảng cha Tour là các bảng con tự bay màu.
        // Tuy nhiên, nếu Tour đã có Lịch Khởi Hành (LichKhoiHanh), lệnh này sẽ lỗi (do ràng buộc Restrict).
        try {
            $stmt = $this->conn->prepare("DELETE FROM tour WHERE MaTour = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false; // Trả về false nếu dính ràng buộc khóa ngoại
        }
    }
   // 1. LẤY DANH SÁCH TOUR (CÓ LỌC: DANH MỤC, TỪ KHÓA, TRẠNG THÁI)
    public function getToursFiltered($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT t.*, lt.TenLoai FROM tour t 
                LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour 
                WHERE 1=1"; 

        $params = [];

        // [MỚI THÊM] Lọc theo trạng thái (VD: Chỉ lấy 'Hoạt động')
        if (!empty($filters['status'])) {
            $sql .= " AND t.TrangThai = :status";
            $params['status'] = $filters['status'];
        }

        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.MaLoaiTour = :cat_id";
            $params['cat_id'] = $filters['category_id'];
        }

        // Tìm kiếm theo tên
        if (!empty($filters['keyword'])) {
            $sql .= " AND t.TenTour LIKE :keyword";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        // Sắp xếp và Phân trang
        $sql .= " ORDER BY t.MaTour DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. ĐẾM TỔNG SỐ TOUR (Cũng phải thêm lọc trạng thái để tính số trang đúng)
    public function countTours($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM tour t WHERE 1=1";
        $params = [];

        // [MỚI THÊM]
        if (!empty($filters['status'])) {
            $sql .= " AND t.TrangThai = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND t.MaLoaiTour = :cat_id";
            $params['cat_id'] = $filters['category_id'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND t.TenTour LIKE :keyword";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>