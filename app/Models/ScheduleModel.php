<?php
require_once '../app/Core/Model.php';

class ScheduleModel extends Model {

    // Lấy danh sách Tour để đổ vào select box
    public function getTours() {
        return $this->conn->query("SELECT MaTour, TenTour, SoNgay FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Nhân sự (HDV)
    public function getStaffs() {
        return $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Tài nguyên theo nhóm Nhà cung cấp
    public function getResources() {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap, ncc.LoaiCungCap 
                FROM tai_nguyen_ncc tn 
                JOIN nha_cung_cap ncc ON tn.MaNhaCungCap = ncc.MaNhaCungCap";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- HÀM TẠO LỊCH (QUAN TRỌNG) ---
    public function createSchedule($data, $resources = [], $staffs = []) {
        try {
            // 1. Bắt đầu Transaction
            $this->conn->beginTransaction();

            // 2. Tạo mã lịch tự động (VD: LKH + Timestamp)
            $lichCode = 'LKH' . time();

            // 3. Insert vào bảng Lịch Khởi Hành
            $sql = "INSERT INTO lichkhoihanh (MaTour, LichCode, NgayKhoiHanh, NgayKetThuc, GioTapTrung, DiaDiemTapTrung, SoChoHienTai, TrangThai) 
                    VALUES (:tour_id, :code, :start, :end, :time, :place, :seats, 'Nhận khách')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'tour_id' => $data['tour_id'],
                'code'    => $lichCode,
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'seats'   => 0 // Mới tạo chưa có khách
            ]);
            
            $lichId = $this->conn->lastInsertId();

            // 4. Insert Phân bổ Tài nguyên (Xe, Khách sạn)
            if (!empty($resources)) {
                $sqlRes = "INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)";
                $stmtRes = $this->conn->prepare($sqlRes);
                foreach ($resources as $resId) {
                    $stmtRes->execute([$lichId, $resId]);
                }
            }

            // 5. Insert Phân bổ Nhân sự (HDV)
            if (!empty($staffs)) {
                $sqlStaff = "INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')";
                $stmtStaff = $this->conn->prepare($sqlStaff);
                foreach ($staffs as $staffId) {
                    $stmtStaff->execute([$lichId, $staffId]);
                }
            }

            // 6. Hoàn tất Transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Nếu lỗi thì hoàn tác tất cả
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Hàm lấy danh sách lịch để hiển thị trang Index (Giai đoạn sau)
    public function getAllSchedules($tour_id = null) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour";
        
        $params = [];

        // Nếu có truyền tour_id thì thêm điều kiện WHERE
        if ($tour_id) {
            $sql .= " WHERE l.MaTour = :id";
            $params['id'] = $tour_id;
        }

        $sql .= " ORDER BY l.NgayKhoiHanh DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm hàm phụ để lấy tên Tour (dùng hiển thị tiêu đề cho đẹp)
    public function getTourName($id) {
        $stmt = $this->conn->prepare("SELECT TenTour FROM tour WHERE MaTour = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['TenTour'] : '';
    }
    // --- LẤY CHI TIẾT LỊCH ---
    public function getScheduleById($id) {
        // Bổ sung thêm t.SoChoToiDa vào dòng SELECT
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.MaTour, t.SoChoToiDa
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE l.MaLichKhoiHanh = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách ID tài nguyên đã gán (để check vào checkbox khi sửa)
    public function getAssignedResourceIds($scheduleId) {
        $sql = "SELECT MaTaiNguyen FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy mảng [1, 5, 8...]
    }

    // Lấy danh sách ID nhân sự đã gán
    public function getAssignedStaffIds($scheduleId) {
        $sql = "SELECT MaNhanSu FROM phanbonhansu WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy chi tiết tài nguyên (để hiển thị trang Show)
    public function getAssignedResourcesDetail($scheduleId) {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap 
                FROM phan_bo_tai_nguyen p 
                JOIN tai_nguyen_ncc tn ON p.MaTaiNguyen = tn.MaTaiNguyen
                JOIN nha_cung_cap ncc ON tn.MaNhaCungCap = ncc.MaNhaCungCap
                WHERE p.MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignedStaffsDetail($scheduleId) {
        $sql = "SELECT ns.* FROM phanbonhansu p 
                JOIN nhansu ns ON p.MaNhanSu = ns.MaNhanSu
                WHERE p.MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CẬP NHẬT LỊCH (Update) ---
    public function updateSchedule($id, $data, $resources = [], $staffs = []) {
        try {
            $this->conn->beginTransaction();

            // 1. Update thông tin chính
            $sql = "UPDATE lichkhoihanh SET 
                    MaTour = :tour_id, NgayKhoiHanh = :start, NgayKetThuc = :end, 
                    GioTapTrung = :time, DiaDiemTapTrung = :place, TrangThai = :status 
                    WHERE MaLichKhoiHanh = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'tour_id' => $data['tour_id'],
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'status'  => $data['status'],
                'id'      => $id
            ]);

            // 2. Xóa phân bổ cũ -> Thêm phân bổ mới (Cách an toàn nhất để update quan hệ nhiều-nhiều)
            
            // a. Tài nguyên
            $this->conn->prepare("DELETE FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($resources)) {
                $stmtRes = $this->conn->prepare("INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)");
                foreach ($resources as $resId) $stmtRes->execute([$id, $resId]);
            }

            // b. Nhân sự
            $this->conn->prepare("DELETE FROM phanbonhansu WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($staffs)) {
                $stmtStaff = $this->conn->prepare("INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')");
                foreach ($staffs as $staffId) $stmtStaff->execute([$id, $staffId]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- XÓA LỊCH (Delete) ---
    public function deleteSchedule($id) {
        // Do đã cài đặt ON DELETE CASCADE trong DB (nếu chưa thì phải xóa bảng con trước)
        // Lệnh xóa này sẽ tự động xóa các dòng bên phan_bo_tai_nguyen và phanbonhansu
        $stmt = $this->conn->prepare("DELETE FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
        return $stmt->execute([$id]);
    }

    // --- LẤY DANH SÁCH KHÁCH (BOOKING) CỦA 1 LỊCH ---
    public function getBookingsBySchedule($scheduleId) {
        // Kết nối bảng Booking với bảng KhachHang để lấy tên và sđt
        $sql = "SELECT b.*, kh.HoTen, kh.SoDienThoai, kh.Email 
                FROM booking b 
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang 
                WHERE b.MaLichKhoiHanh = :id 
                ORDER BY b.NgayDat DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // --- LẤY DANH SÁCH LỊCH (CÓ LỌC & PHÂN TRANG) ---
    public function getSchedulesFiltered($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.SoChoToiDa 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE 1=1";

        $params = [];

        // 1. Lọc theo Tour ID (Khi bấm từ trang Tour sang)
        if (!empty($filters['tour_id'])) {
            $sql .= " AND l.MaTour = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        // 2. Tìm kiếm theo Mã lịch hoặc Tên tour
        if (!empty($filters['keyword'])) {
            $sql .= " AND (l.LichCode LIKE :kw OR t.TenTour LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        // 3. Lọc theo ngày khởi hành (Nếu có)
        if (!empty($filters['date'])) {
            $sql .= " AND l.NgayKhoiHanh >= :date";
            $params['date'] = $filters['date'];
        }

        // Sắp xếp: Ngày mới nhất lên đầu
        $sql .= " ORDER BY l.NgayKhoiHanh DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ĐẾM TỔNG SỐ LỊCH (ĐỂ TÍNH SỐ TRANG) ---
    public function countSchedules($filters = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['tour_id'])) {
            $sql .= " AND l.MaTour = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (l.LichCode LIKE :kw OR t.TenTour LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND l.NgayKhoiHanh >= :date";
            $params['date'] = $filters['date'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // --- KIỂM TRA TÌNH TRẠNG BẬN CỦA HDV ---
    public function getGuidesAvailability($startDate, $endDate, $excludeScheduleId = 0) {
        // 1. Lấy danh sách ID của các HDV đang bận
        // (Tức là đã được gán vào lịch khác có ngày chồng lấn với khoảng thời gian này)
        $sqlBusy = "SELECT DISTINCT p.MaNhanSu 
                    FROM phanbonhansu p
                    JOIN lichkhoihanh l ON p.MaLichKhoiHanh = l.MaLichKhoiHanh
                    WHERE l.TrangThai IN ('Nhận khách', 'Đang chuẩn bị', 'Đang chạy') -- Chỉ tính các lịch active
                    AND l.MaLichKhoiHanh != :excludeId -- Trừ chính lịch đang sửa (nếu có)
                    AND (
                        (l.NgayKhoiHanh <= :end_date) AND (l.NgayKetThuc >= :start_date)
                    )";
        
        $stmt = $this->conn->prepare($sqlBusy);
        $stmt->execute([
            'end_date' => $endDate,
            'start_date' => $startDate,
            'excludeId' => $excludeScheduleId
        ]);
        $busyIds = $stmt->fetchAll(PDO::FETCH_COLUMN); // Mảng [1, 5, ...]

        // 2. Lấy tất cả HDV và đánh dấu ai bận
        $allGuides = $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV'")->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($allGuides as $guide) {
            $guide['is_busy'] = in_array($guide['MaNhanSu'], $busyIds);
            $result[] = $guide;
        }
        
        return $result;
    }

    
}
?>